<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Helpers\ImageHelper;
use App\Helpers\ImageUploader;
use App\Http\Requests\Api\Auth\FcmTokenUpdateRequest;
use App\Http\Requests\Api\Auth\PasswordChangeRequest;
use App\Http\Requests\Api\Auth\UploadImageRequest;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends ApiController
{
    protected $user;
    protected $imageUpload;

    /**
     * Constructor
     *
     * @param User $user
     */
    public function __construct(
        User $user
    ) {
        $this->user =   $user;
    }

    /**
     * Get authenticated user info
     *
     * @return JsonResponse
     */
    public function info(): JsonResponse
    {
        return $this->detailRespond(new UserResource(auth()->user()));
    }

    /**
     * Verify user
     *
     * @return JsonResponse
     */
    public function verify()
    {
        $user = auth()->user();
        $user->update([
            'status' => 'verified'
        ]);
        return $this->respondSuccess(__("messages.profile_verified"));
    }

    /**
     * Update FCM token
     *
     * @param FcmTokenUpdateRequest $request
     * @return JsonResponse
     */
    public function fcm_token_update(FcmTokenUpdateRequest $request): JsonResponse
    {
        auth()->user()->update([
            'fcm_token' => $request->fcm_token,
            'firebase_id' => $request->firebase_id ?? auth()->user()->firebase_id,
        ]);
        return $this->respondSuccess(__("messages.fcm_token_updated"));
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone' => 'sometimes|string|unique:users,phone,' . auth()->id(),
            'password' => 'sometimes|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }

        $user = auth()->user();
        $data = $validator->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $this->detailRespond(new UserResource($user));
    }

    /**
     * Update user profile image
     *
     * @param UploadImageRequest $request
     * @return JsonResponse
     */
    public function update_image(UploadImageRequest $request)
    {
        $user = $this->user->find(auth()->user()->id);
        if ($request->file('image')) {
            if (file_exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }
            $imagePath = ImageHelper::uploadImage($request->file('image'), 'profile');
            $user->image = $imagePath;
            $user->save();
        }
        return $this->respondSuccess(__("messages.profile_image_updated"));
    }

    /**
     * Change user password
     *
     * @param PasswordChangeRequest $request
     * @return JsonResponse
     */
    public function change_password(PasswordChangeRequest $request): JsonResponse
    {
        auth()->user()->update([
            'password' => bcrypt($request->password),
            'firebase_id' => $request->firebase_id ?? auth()->user()->firebase_id,
        ]);
        return $this->respondSuccess(__("messages.change_password_message"));
    }

    /**
     * Delete user account
     *
     * @return JsonResponse
     */
    public function delete()
    {
        try {
            $user = $this->user->findOrFail(Auth::id());
            $user->delete();
            return $this->respondSuccess(__("messages.account_deleted_message"));
        } catch (Exception $e) {
            Log::info("Can not delete account." . $e->getMessage());
            return $this->respondWithError(__("messages.can_not_be_deleted"));
        }
    }
}
