<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\Registered;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'register', 'forgotPassword', 'resetPassword', 'showResetForm', 'refresh']]);
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $credentials = $request->only('email', 'password');
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => $request->password], 401);
            }

            return $this->respondWithToken($token, $this->refreshToken(JWTAuth::user()));
        } catch (Throwable $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }

    }

    public function register(RegisterRequest $request)
    {
        try {
            $requestData = $request->only([
                'first_name',
                'last_name',
                'email',
                'password',
                'date_of_birth',
                'country'
            ]);

            if (User::where('email', $request['email'])->exists()) {
                return response()->json(['message' => 'User already exists'], 409);
            }
            $user = User::create(
                $requestData
            );
            event(new Registered($user));
            $token = JWTAuth::fromUser($user);
            return $this->respondWithToken($token, $this->refreshToken($user));
        } catch (Throwable $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function resendVerificationEmail()
    {
        try {
            $user = JWTAuth::user();

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'message' => 'Email has been verified.'
                ], 400);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Verified email has been send.'
            ]);
        } catch (Throwable $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    private function refreshToken(User $user)
    {
        $access = [
            'id' => $user->id,
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refresh_ttl')
        ];
        return JWTAuth::getJWTProvider()->encode($access);
    }
    public function profile()
    {

        return response()->json(new ProfileResource(JWTAuth::user()));
    }
    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->input('refresh_token');
            if (!$refreshToken) {
                return response()->json(['error' => 'Refresh token not provided'], 400);
            }
            $payload = JWTAuth::getJWTProvider()->decode($refreshToken);
            // Retrieve the user by the ID in the payload
            $user = User::find($payload['id']);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Generate a new token
            $token = JWTAuth::fromUser($user);

            return $this->respondWithToken($token);
        } catch (Throwable $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)], 200);
            }

            return response()->json(['error' => __($status)], 400);
        } catch (Throwable $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => __($status)], 200);
            }

            return response()->json(['error' => [__($status)]], 400);
        } catch (Throwable $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }
    public function showResetForm($token, Request $request)
    {
        return response()->json(['token' => $token, 'email' => $request->query('email')]);
    }

    protected function respondWithToken($token, $refreshToken = null)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::getPayloadFactory()->getTTL() * 60,
            'refresh_token' => $refreshToken,
        ]);
    }
}
