<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageUploader;
use App\Http\Requests\Api\CompanyStoreRequest;
use App\Http\Requests\Api\CompanyUpdateRequest;
use App\Http\Requests\Api\StoreCompanyReviewRequest;
use App\Http\Requests\CompanyDocumentRequest;
use App\Http\Requests\StoreComplainRequest;
use App\Http\Resources\Api\CompanyDetailResource;
use App\Http\Resources\Api\CompanyListResource;
use App\Http\Resources\Api\TenderListResource;
use App\Http\Resources\CompanyComplainResource;
use App\Http\Resources\CompanyReviewListResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\Tender\TenderListResource as TenderTenderListResource;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompanyController extends ApiController
{

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function my(): JsonResponse
    {
        $perPage = request('perPage', 10);

        $companies = Company::whereHas('owner', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->with('owner')
            ->paginate($perPage);

        return $this->collectionRespond(
            CompanyListResource::collection($companies)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CompanyStoreRequest $request
     * @return JsonResponse
     */
    public function store(CompanyStoreRequest $request): JsonResponse
    {
        $data = $request->except('_token', 'category_ids');
        $data['verification_status'] = 'pending';

        if ($request->hasFile('logo')) {
            $data['logo'] = ImageUploader::upload($request->file('logo'), 'companies');
        }

        if ($request->hasFile('banner')) {
            $data['banner'] = ImageUploader::upload($request->file('banner'), 'companies');
        }



        DB::transaction(function () use ($request, $data) {
            $company = Company::create($data);
            $company->users()->attach(auth()->id(), [
                'role' => 'owner',
                'status' => 'active',
            ]);

            if ($request->has('category_ids')) {
                $company->categories()->attach($request->category_ids);
            }
        });

        return $this->respondSuccess(__('company.company_created'));
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $company = Company::findOrFail($id);
        $company->load(['owner']);
        return $this->detailRespond(new CompanyDetailResource($company));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        $data = $request->except('_token', 'category_ids');
        $data['verification_status'] = 'pending';

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                ImageUploader::delete($company->logo);
            }
            $data['logo'] = ImageUploader::upload($request->file('logo'), 'companies');
        }

        if ($request->hasFile('banner')) {
            if ($company->banner) {
                ImageUploader::delete($company->banner);
            }

            $data['banner'] = ImageUploader::upload($request->file('banner'), 'companies');
        }

        DB::transaction(function () use ($request, $data, $company) {
            $company->update($data);
            if ($request->has('category_ids')) {
                $company->categories()->sync($request->category_ids);
            }
        });
        return $this->respondSuccess(__('company.company_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        Company::findOrFail($id)->delete();
        return $this->respondSuccess(__('company.company_deleted'));
    }

    /**
     * Verify company
     *
     * @param $tin
     * @return JsonResponse
     */
    public function verify($tin): JsonResponse
    {
        $company = Company::where('tin_number', $tin)->first();
        if ($company) {
            $company->update(['verification_status' => 'verified']);
            return $this->respondSuccess(__('company.company_verified'));
        }
        return $this->respondWithError(__('company.company_not_found'));
    }

    /**
     * Store company review
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function storeReview(StoreCompanyReviewRequest $request, Company $company): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['company_id'] = $company->id;
        $company->reviews()->create($data);
        return $this->respondSuccess(__('company.review_created'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function reviews(Company $company): JsonResponse
    {
        $perPage = request('perPage', 10);
        $reviews = $company->reviews()->with('user')->paginate($perPage);
        return $this->collectionRespond(
            CompanyReviewListResource::collection($reviews)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function tenders(Company $company): JsonResponse
    {
        $tenders = $company->tenders()->with('company', 'tenderCategory')->paginate(10);
        return $this->collectionRespond(
            TenderTenderListResource::collection($tenders)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function documents(Company $company): JsonResponse
    {
        $documents = $company->documents()->paginate(10);
        return $this->collectionRespond(
            DocumentResource::collection($documents)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function uploadDocument(CompanyDocumentRequest $request, Company $company): JsonResponse
    {

        $file = $request->file('file');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/documents', $filename, 'public');
        CompanyDocument::create([
            'company_id' => $company->id,
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'file_path' => $path,
            'verified' => false,
        ]);

        return $this->respondSuccess(__("Document uploaded successfully"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function deleteDocument($id): JsonResponse
    {
        $document = CompanyDocument::find($id);
        if ($document->file_path) {
            ImageUploader::delete($document->file_path);
        }
        $document->delete();
        return $this->respondSuccess(__("Document deleted successfully"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function getCompanyComplains(Company $company): JsonResponse
    {
        $perPage = request('perPage', 10);
        $complains = $company->complains()->paginate($perPage);
        return $this->collectionRespond(
            CompanyComplainResource::collection($complains)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyReviewRequest $request
     * @return JsonResponse
     */
    public function storeComplain(StoreComplainRequest $request, Company $company): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['company_id'] = $company->id;
        $company->complains()->create($data);
        return $this->respondSuccess(__('company.complain_created'));
    }
}
