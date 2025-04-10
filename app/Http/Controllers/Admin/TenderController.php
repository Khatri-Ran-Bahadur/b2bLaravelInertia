<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TenderDetailResource;
use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenderController extends Controller
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


        $query = Tender::select(
            'tenders.id',
            'tenders.title',
            'tenders.description',
            'tenders.start_date',
            'tenders.end_date',
            'tenders.created_at',
            'companies.name as company_name'
        )
            ->join('companies', 'companies.id', '=', 'tenders.company_id')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('tenders.title', 'like', "%{$search}%");
                });
            });

        // Filter by tender category
        if ($request->filled('tender_category_id')) {
            $query->where('tenders.tender_category_id', $request->input('tender_category_id'));
        }
        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('tenders.company_id', $request->input('company_id'));
        }


        // Add sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $tenders = $query->paginate($perPage)
            ->withQueryString();

        $tenderCategories = TenderCategory::select('id', 'name')->get();
        $companies = Company::select('id', 'name')->get();

        return Inertia::render('Admin/Tender/Index', [
            'tenders' => $tenders,
            'tenderCategories' => $tenderCategories,
            'companies' => $companies,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'page' => $request->input('page', 1),
                'sort' => $sort,
                'direction' => $direction,
                'tender_category_id' => $request->input('tender_category_id', ''),
                'company_id' => $request->input('company_id', ''),
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
     * Show 
     *
     * @return Response
     */
    public function show(Tender $tender): Response
    {
        $tender->load(['owner']);
        return Inertia::render('Admin/Tender/Show', [
            'tender' => new TenderDetailResource($tender),
        ]);
    }

    public function destroy(Tender $tender)
    {
        $tender->delete();
        return redirect()->route('admin.users.index');
    }
}
