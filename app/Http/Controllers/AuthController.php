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
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

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
            Log::error('Login error: ' . $error->getMessage());
            return response()->json(['error' => 'An error occurred during login. Please try again.'], 500);
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
            $user = User::create($requestData);

            event(new Registered($user));
            $token = JWTAuth::fromUser($user);
            // Đặt token vào request để JWTAuth có thể nhận diện user
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // Refresh để đảm bảo JWTAuth nhận diện user mới
            JWTAuth::setToken($token)->toUser();
            return $this->respondWithToken($token, $this->refreshToken($user));
        } catch (Throwable $error) {
            Log::error('Registration error: ' . $error->getMessage());
            return response()->json(['error' => 'An error occurred during registration. Please try again.'], 500);
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
            Log::error('Email verification error: ' . $error->getMessage());
            return response()->json(['error' => 'Failed to send verification email. Please try again later.'], 500);
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
    public function fetchProfile()
    {

        return response()->json(new UserResource(JWTAuth::user()));
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

            if ($user->remember_token !== $refreshToken) {
                return response()->json(['error' => 'Invalid refresh token'], 401);
            }

            // Generate a new token
            $token = JWTAuth::fromUser($user);

            return $this->respondWithToken($token);
        } catch (Throwable $error) {
            Log::error('Token refresh error: ' . $error->getMessage());
            return response()->json(['error' => 'Failed to refresh token. Please log in again.'], 500);
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
            Log::error('Forgot password error: ' . $error->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request. Please try again later.'], 500);
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
            Log::error('Password reset error: ' . $error->getMessage());
            return response()->json(['error' => 'An error occurred while resetting your password. Please try again.'], 500);
        }
    }

    protected function respondWithToken($token, $refreshToken = null)
    {
        $user = JWTAuth::user();
        if ($refreshToken && $user) {
            $user->forceFill([
                'remember_token' => $refreshToken
            ])->save();
        }
        Log::debug($user);
        Log::debug($refreshToken);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::getPayloadFactory()->getTTL() * 60,
            'refresh_token' => $refreshToken,
        ]);
    }
}
