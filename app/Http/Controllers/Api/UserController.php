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
     * @OA\Get(
     *     path="/api/users/info",
     *     summary="Get authenticated user info",
     *     operationId="getUserInfo",
     *     tags={"User"},
     *     security={{"passport":{}}}, 
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User information retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function info(): JsonResponse
    {
        $data = new UserResource(auth()->user());
        return $this->detailRespond($data);
    }

    public function verify()
    {
        $user = auth()->user();
        $user->update([
            'status' => 'verified'
        ]);
        return $this->respondSuccess(__("messages.profile_verified"));
    }

    public function fcm_token_update(FcmTokenUpdateRequest $request): JsonResponse
    {
        auth()->user()->update([
            'fcm_token' => $request->fcm_token,
            'firebase_id' => $request->firebase_id ?? auth()->user()->firebase_id,
        ]);
        return $this->respondSuccess(__("messages.fcm_token_updated"));
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->user->find(auth()->user()->id);
        $path = $user->image;
        if ($request->hasFile('image')) {
            if ($path) {
                ImageUploader::delete($path);
            }
            $path = ImageUploader::upload($request->file('image'), 'profile');
        }
        try {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'registered' => 1,
                'phone' => $request->phone,
                'image' => $path
            ]);
            return $this->respondSuccess(__("messages.profile_updated"));
        } catch (\Exception $exception) {
            return $this->respondWithException($exception->getMessage(), $exception->getFile(), $exception->getLine());
        }
    }

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
    public function change_password(PasswordChangeRequest $request): JsonResponse
    {
        auth()->user()->update([
            'password' => bcrypt($request->password),
            'firebase_id' => $request->firebase_id ?? auth()->user()->firebase_id,
        ]);
        return $this->respondSuccess(__("messages.change_password_message"));
    }

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
