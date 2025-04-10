<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenderCategoryStoreUpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\TenderCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class TenderCategoryController extends Controller
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
        $query = TenderCategory::select(
            'id',
            'name'
        )->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });

        // Add sorting
        $query->orderBy($sort, $direction);

        // Get paginated results
        $tenderCategories = $query->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Tender/Category/Index', [
            'tenderCategories' => $tenderCategories,
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

    public function create()
    {
        return Inertia::render('Admin/Tender/Category/Create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(TenderCategoryStoreUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        TenderCategory::create($validated);
        return redirect()
            ->route('admin.tender-categories.index')
            ->with('message', __('Tender Category created successfully!'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function destroy(TenderCategory $tenderCategory): RedirectResponse
    {
        $tenderCategory->delete();
        return redirect()
            ->route('admin.tender-categories.index')
            ->with('message', __('Tender Category deleted successfully!'));
    }
    public function edit(TenderCategory $tenderCategory): Response
    {
        return Inertia::render('Admin/Tender/Category/Edit', [
            'tenderCategory' => $tenderCategory,
        ]);
    }
    public function update(TenderCategoryStoreUpdateRequest $request, TenderCategory $tenderCategory): RedirectResponse
    {
        $validated = $request->validated();
        $tenderCategory->update($validated);
        return redirect()
            ->route('admin.tender-categories.index')
            ->with('message', __('Tender Category updated successfully!'));
    }
}
