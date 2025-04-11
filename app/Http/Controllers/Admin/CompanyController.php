<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CompanyDetailResource;
use App\Http\Resources\Api\CompanyDocumentResource;
use App\Http\Resources\Api\CompanyProductResource;
use App\Http\Resources\Api\CompanyReviewResource;
use App\Http\Resources\Api\CompanyTenderResource;
use App\Http\Resources\CompanyRelationalResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\Tender\TenderDetailResource;
use App\Http\Resources\Tender\TenderListResource;
use App\Models\Company;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // Query builder with search functionality
        /*name
        •⁠  ⁠tin_number
        •⁠  ⁠verification_status
        •⁠  ⁠contact_info
        */


        // Concatenate first_name and last_name for owner_name
        $query = Company::select(
            'companies.id',
            'companies.name',
            'companies.tin_number',
            'companies.verification_status',
            'companies.phone',
            'companies.created_at',
            DB::raw("CONCAT(users.first_name, ' ', users.last_name) as owner_name")
        )
            ->join('company_users', function ($join) {
                $join->on('company_users.company_id', '=', 'companies.id')
                    ->where('company_users.role', 'owner');
            })
            ->join('users', 'users.id', '=', 'company_users.user_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('companies.name', 'like', "%{$search}%")
                        ->orWhere('companies.tin_number', 'like', "%{$search}%")
                        ->orWhere('companies.verification_status', 'like', "%{$search}%")
                        ->orWhere('companies.phone', 'like', "%{$search}%")
                        ->orWhere('users.name', 'like', "%{$search}%");
                });
            });



        // Add sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $companies = $query->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Company/Index', [
            'companies' => $companies,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'page' => $request->input('page', 1),
                'sort' => $sort,
                'direction' => $direction,
            ],
            'can' => [
                'view' => true,
                'create' => true,
                'edit' => true,
                'delete' => true,
            ]
        ]);
    }



    /**
     * Display the company details page.
     */
    public function show(Company $company): Response
    {
        return Inertia::render('Admin/Company/Show', [
            'company' => CompanyDetailResource::make($company),
            'activeTab' => 'details',
        ]);
    }

    /**
     * Get company details.
     */
    public function details(Company $company): Response
    {
        $company->load([
            'owner' => function ($query) {
                $query->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.image')
                    ->where('company_users.role', 'owner');
            },
        ]);

        return Inertia::render('Admin/Company/Show', [
            'company' => CompanyDetailResource::make($company),
            'activeTab' => 'details',
        ]);
    }

    /**
     * Get company documents.
     */
    public function documents(Company $company): Response
    {
        $documents = $company->documents()
            ->latest()
            ->get();

        return Inertia::render('Admin/Company/Show', [
            'company' => CompanyDetailResource::make($company),
            'documents' => DocumentResource::collection($documents)->resolve(),
            'activeTab' => 'documents',
        ]);
    }

    /**
     * Get company products.
     */
    public function products(Company $company, Request $request): Response|JsonResponse
    {
        $perPage = $request->input('per_page', 12);

        $products = $company->products()
            ->with('media')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate($perPage);

        $meta = [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ];

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'avg_rating' => $product->reviews_avg_rating ?? 0,
                'description' => $product->description,
                'status' => $product->status,
                'created_at' => $product->created_at,
                'images' => $product->getFirstMediaUrl('images')
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'products' => $formattedProducts,
                'meta' => $meta,
            ]);
        }

        return Inertia::render('Admin/Company/Show', [
            'company' => CompanyDetailResource::make($company),
            'initialProducts' => $formattedProducts,
            'initialMeta' => $meta,
            'activeTab' => 'products',
        ]);
    }

    /**
     * Get company tenders.
     */
    public function tenders(Company $company, Request $request): Response|JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12);

        $tenders = $company->tenders()
            ->latest()
            ->paginate($perPage);

        $meta = [
            'current_page' => $tenders->currentPage(),
            'last_page' => $tenders->lastPage(),
            'per_page' => $tenders->perPage(),
            'total' => $tenders->total(),
        ];

        $formattedTenders = $tenders->map(function ($tender) {
            return [
                'id' => $tender->id,
                'title' => $tender->title,
                'budget' => $tender->price,
                'phone' => $tender->phone,
                'description' => $tender->description,
                'is_active' => $tender->is_active,
                'created_at' => $tender->created_at,
                'images' => $tender->getFirstMediaUrl('tender_images')
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'tenders' => $formattedTenders,
                'meta' => $meta,
            ]);
        }

        return Inertia::render('Admin/Company/Show', [
            'company' => CompanyDetailResource::make($company),
            'initialTenders' => $formattedTenders,
            'initialMeta' => $meta,
            'activeTab' => 'tenders',
        ]);
    }

    /**
     * Get company reviews.
     */
    public function reviews(Company $company, Request $request): Response|JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 12);

        $reviews = Review::whereHas('product', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->with(['user', 'answers.user', 'media', 'product'])
            ->latest()
            ->paginate($perPage);

        // Calculate average rating and rating counts for all product reviews
        $averageRating = Review::whereHas('product', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->avg('rating');

        $ratingCounts = Review::whereHas('product', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $meta = [
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'per_page' => $reviews->perPage(),
            'total' => $reviews->total(),
            'average_rating' => round($averageRating, 1),
            'rating_counts' => $ratingCounts,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'reviews' => ReviewResource::collection($reviews)->resolve(),
                'meta' => $meta,
            ]);
        }


        return Inertia::render('Admin/Company/Show', [
            'company' => CompanyDetailResource::make($company),
            'initialReviews' => ReviewResource::collection($reviews->items())->resolve(),
            'initialMeta' => $meta,
            'activeTab' => 'reviews',
        ]);
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.users.index');
    }
}
