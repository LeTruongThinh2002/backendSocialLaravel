<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\User;
use App\Models\users;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ChangePwdRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\StoreusersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\UpdateusersRequest;
use App\Http\Requests\ChangePasswordRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getProfile(User $user)
    {
        try {
            return new ProfileResource($user);
        } catch (Throwable $error) {
            return response()->json([
                'message' => $error->getMessage()
            ], 500);
        }
    }


    public function toggleBlockUser(User $user)
    {
        try {
            $authUser = JWTAuth::user();
            $isBlocked = $authUser->userBlock()->where('id', $user->id)->exists();

            if ($isBlocked) {
                $authUser->userBlock()->detach($user->id);
                $message = 'User unblocked successfully.';
            } else {
                $authUser->userBlock()->attach($user->id, ['created_at' => now()]);
                $message = 'User blocked successfully.';
            }

            return response()->json(['message' => $message], 200);
        } catch (Throwable $error) {
            Log::error('Toggle block user error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while toggling user block status.',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the email resource in storage.
     */
    public function changeEmail(ChangeEmailRequest $request, User $user)
    {
        try {
            if (JWTAuth::user()->id != $user->id) {
                return response()->json([
                    'message' => 'Unauthorized to change email for this user.'
                ], 403);  // Forbidden
            }
            if (!Hash::check($request->password, JWTAuth::user()->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.'
                ], 401); // Unauthorized
            }
            $user->update([
                'email' => $request->email,
                'email_verified_at' => null
            ]);
            return response()->json([
                'message' => 'Email changed successfully.'
            ], 200);

        } catch (Throwable $error) {
            return response()->json([
                'message' => 'Error changing email.',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the password in storage.
     */
    public function changePassword(ChangePwdRequest $request, User $user)
    {
        try {
            if (JWTAuth::user()->id != $user->id) {
                return response()->json([
                    'message' => 'Unauthorized to change password for this user.'
                ], 403);  // Forbidden
            }
            if (!Hash::check($request->password, JWTAuth::user()->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.'
                ], 401); // Unauthorized
            }
            if ($request->new_password != $request->confirm_password) {
                return response()->json([
                    'message' => 'New password and confirmation password do not match.'
                ], 422); // Unprocessable Entity
            }
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Password changed successfully.'
            ], 200);

        } catch (Throwable $error) {
            return response()->json([
                'message' => 'Error changing password.',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            if (JWTAuth::user()->id != $user->id) {
                return response()->json([
                    'message' => 'Unauthorized to update this user.'
                ], 401);  // Unauthorized
            }
            $user->update($request->only([
                'first_name',
                'last_name',
                'avatar',
                'background',
                'date_of_birth',
                'country'
            ]));
            return response()->json([
                'message' => 'User updated successfully.'
            ], 200);

        } catch (Throwable $error) {
            return response()->json([
                'message' => 'Error updating user.',
                'error' => $error->getMessage()
            ], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if (JWTAuth::user()->id != $user->id) {
                return response()->json([
                    'message' => 'Unauthorized to update this user.'
                ], 401);  // Unauthorized
            }
            $user->deleteOrFail();
            return response()->json([
                'message' => 'User deleted successfully.'
            ], 200);

        } catch (Throwable $error) {
            return response()->json([
                'message' => 'Error deleting user.',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
