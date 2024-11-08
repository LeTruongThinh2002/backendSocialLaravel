<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchUserRequest;
use App\Http\Resources\UserResource;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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
            Log::error('Get profile error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the profile.'
            ], 500);
        }
    }

    public function getAuthUserInfo()
    {
        try {
            return response()->json(new ProfileResource(JWTAuth::user()));
        } catch (Throwable $error) {
            Log::error('Get auth user info error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the auth user info.'
            ], 500);
        }
    }


    public function toggleBlockUser(User $user)
    {
        try {
            $authUser = JWTAuth::user();
            $isBlocked = $authUser->userBlock()->where('id', $user->id)->exists();

            if (!$isBlocked) {
                $authUser->userFollow()->detach($user->id);
                $user->userFollow()->detach($authUser->id);
            }
            $authUser->userBlock()->toggle($user->id);

            $authUser->load('userBlock');

            return response()->json([
                'userBlock' => $authUser->userBlock->map(function ($user) {
                    return $user->only(['id', 'first_name', 'last_name', 'avatar']);
                })
            ], 200);
        } catch (Throwable $error) {
            Log::error('Toggle block user error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while toggling user block status.',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function toggleFollowUser(User $user)
    {
        try {
            $authUser = JWTAuth::user();
            $authUser->userFollow()->toggle($user->id);
            $authUser->load('userFollow');

            return response()->json([
                'userFollow' => $authUser->userFollow->map(function ($user) {
                    return $user->only(['id', 'first_name', 'last_name', 'avatar']);
                })
            ], 200);

        } catch (Throwable $error) {
            Log::error('Toggle follow user error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while toggling user follow status.',
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
            Log::error('Change email error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while changing the email.',
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
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Password changed successfully.'
            ], 200);

        } catch (Throwable $error) {
            Log::error('Change password error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while changing the password.',
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
                ], 403);  // Unauthorized
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
            Log::error('Update user error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the user.',
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
                    'message' => 'Unauthorized to delete this user.'
                ], 403);
            }
            $user->deleteOrFail();
            return response()->json([
                'message' => 'User deleted successfully.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        } catch (Throwable $error) {
            Log::error('Delete user error: ' . $error->getMessage());
            return response()->json([
                'message' => 'An error occurred while deleting the user.'
            ], 500);
        }
    }

    public function recommendUsers()
    {
        $authUser = JWTAuth::user();

        $users = User::select('users.id', 'users.first_name', 'users.last_name', 'users.avatar')
            ->leftJoin('user_follow as uf', 'uf.user_following', '=', 'users.id')
            ->leftJoin('user_block as block1', function ($join) use ($authUser) {
                $join->on('block1.user_blocked', '=', 'users.id')
                    ->where('block1.user_id', '=', $authUser->id);
            })
            ->leftJoin('user_block as block2', function ($join) use ($authUser) {
                $join->on('block2.user_id', '=', 'users.id')
                    ->where('block2.user_blocked', '=', $authUser->id);
            })
            ->whereNull('block1.user_id') // Đảm bảo không có block từ authUser đến user
            ->whereNull('block2.user_id') // Đảm bảo không có block từ user đến authUser
            ->where('uf.user_id', '!=', $authUser->id) // Đảm bảo authUser không follow user
            ->selectRaw('COUNT(uf.user_id) as follow_count') // Thêm số lượng follow
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.avatar')
            ->orderByRaw('follow_count DESC') // Sắp xếp theo số lượng follower từ cao đến thấp
            ->limit(5)
            ->get();

        return response()->json([
            'recommendUsers' => $users
        ], status: 200);
    }

    public function searchUser(SearchUserRequest $request)
    {
        $authUser = JWTAuth::user();

        $users = User::select('id', 'first_name', 'last_name', 'avatar', 'background', 'email')
            ->leftJoin('user_block', function ($join) use ($authUser) {
                $join->on('user_block.user_blocked', '=', 'users.id')
                    ->where('user_block.user_id', $authUser->id);
            })
            ->leftJoin('user_block as block2', function ($join) use ($authUser) {
                $join->on('block2.user_id', '=', 'users.id')
                    ->where('block2.user_blocked', $authUser->id);
            })
            ->whereNull('user_block.user_id')
            ->whereNull('block2.user_id')
            ->where(function ($query) use ($request) {
                $searchTerm = $request->input('query');
                $query->where('first_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
            })
            ->paginate(10);

        return response()->json($users);
    }

}
