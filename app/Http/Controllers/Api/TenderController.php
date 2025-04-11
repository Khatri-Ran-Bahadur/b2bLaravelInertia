<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TenderStoreRequest;
use App\Http\Requests\Api\TenderUpdateRequest;
use App\Http\Resources\Tender\TenderDetailResource;
use App\Http\Resources\Tender\TenderListResource;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TenderController extends ApiController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param TenderStoreRequest $request
     * @return JsonResponse
     */
    public function store(TenderStoreRequest $request): JsonResponse
    {
        $data = $request->except('tender_products', 'images');
        $data['company_id'] = $request->company_id;

        // Check if user is authorized to create tender for this company
        $user = Auth::user();
        $userCompanies = $user->companies()->pluck('company_id')->toArray();



        if (!in_array($request->company_id, $userCompanies)) {
            return $this->respondWithError(__('tender.unauthorized_company'));
        }

        DB::beginTransaction();
        try {
            $tender = Tender::create($data);

            // Create tender products
            if (isset($request->tender_products)) {
                $tender->tenderProducts()->createMany($request->tender_products);
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $tender->addMedia($image)->toMediaCollection('tender_images');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithException($e->getMessage(), $e->getFile(), $e->getLine());
        }

        return $this->respondSuccess(__('tender.tender_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $tender = Tender::findOrFail($id);
        $tender->load(['company', 'tenderCategory', 'media']);

        return $this->detailRespond(new TenderDetailResource($tender));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TenderUpdateRequest $request
     * @param Tender $tender
     * @return JsonResponse
     */
    public function update(TenderUpdateRequest $request, Tender $tender): JsonResponse
    {
        // Check if user is authorized to update this tender
        $user = Auth::user();

        if (!$user->companies()->where('company_id', $tender->company_id)->exists()) {
            return $this->respondWithError(__('tender.unauthorized'), 403);
        }

        $data = $request->except('tender_products', 'images');

        DB::beginTransaction();
        try {
            $tender->update($data);

            // Update tender products if provided
            if (isset($request->tender_products)) {
                $tender->tenderProducts()->delete();
                $tender->tenderProducts()->createMany($request->tender_products);
            }

            // Handle image updates
            if ($request->hasFile('images')) {
                // Clear existing images if new ones are being uploaded
                $tender->clearMediaCollection('tender_images');

                // Add new images
                foreach ($request->file('images') as $image) {
                    $tender->addMedia($image)->toMediaCollection('tender_images');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithException($e->getMessage(), $e->getFile(), $e->getLine());
        }

        return $this->respondSuccess(__('tender.tender_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tender $tender
     * @return JsonResponse
     */
    public function delete(Tender $tender): JsonResponse
    {
        // Check if user is authorized to delete this tender
        $user = Auth::user();
        if (!$user->companies()->where('company_id', $tender->company_id)->exists()) {
            return $this->respondWithError(__('tender.unauthorized'), 403);
        }

        try {
            $tender->delete();
            return $this->respondSuccess(__('tender.tender_deleted'));
        } catch (\Exception $e) {
            return $this->respondWithException($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * Remove a specific image from the tender.
     *
     * @param Tender $tender
     * @param int $mediaId
     * @return JsonResponse
     */
    public function removeImage(Tender $tender, int $mediaId): JsonResponse
    {
        // Check if user is authorized to modify this tender
        $user = Auth::user();
        if (!$user->companies()->where('company_id', $tender->company_id)->exists()) {
            return $this->respondWithError(__('tender.unauthorized'), 403);
        }

        try {
            $media = $tender->getMedia('tender_images')->where('id', $mediaId)->first();

            if (!$media) {
                return $this->respondWithError(__('tender.image_not_found'), 404);
            }

            $media->delete();
            return $this->respondSuccess(__('tender.image_deleted'));
        } catch (\Exception $e) {
            return $this->respondWithException($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * Update the active status of the tender.
     *
     * @param Tender $tender
     * @return JsonResponse
     */
    public function activeStatus(Request $request, Tender $tender): JsonResponse
    {
        $tender->active_status = $request->active_status;
        $tender->save();
        return $this->respondSuccess(__('tender.tender_updated'));
    }
}
