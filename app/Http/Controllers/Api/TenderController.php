<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TenderStoreRequest;
use App\Http\Requests\Api\TenderUpdateRequest;
use App\Http\Resources\Tender\TenderDetailResource;
use App\Http\Resources\Tender\TenderListResource;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

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
        $data = $request->all();
        Tender::create($data);
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
        $tender->load(['company', 'tenderCategory']);

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
        $data = $request->all();
        $tender->update($data);

        return $this->respondSuccess(__('tender.tender_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        Tender::findOrFail($id)->delete();

        return $this->respondSuccess(__('tender.tender_deleted'));
    }
}
