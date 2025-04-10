<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CustomPaginationHelper;
use App\Helpers\ImageUploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreUpdateRequest;
use App\Http\Resources\CategoryListResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;


class CategoryController extends Controller
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
        $query = Category::select(
            'id',
            'name',
            'slug',
            'parent_id',
            'image',
            'created_at',
        )->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });

        // Add sorting
        $query->orderBy($sort, $direction);

        // Get paginated results


        $categories = $query->paginate($perPage)
            ->withQueryString();
        $categories->getCollection()->transform(function ($item) {
            // Add full image URL
            $item->image = asset($item->image);
            // Add parent name if parent_id exists
            if ($item->parent_id) {
                $item->parent_name = $item->parent->name;
            }

            return $item;
        });


        return Inertia::render('Admin/Product/Category/Index', [
            'categories' => CustomPaginationHelper::data($categories),
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
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = Category::with('descendants')->isParent()->get();
        $flattenedCategories = $this->flattenCategories($categories);

        return Inertia::render('Admin/Product/Category/Create', [
            'categories' => $flattenedCategories,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CategoryStoreUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $validated['image'] = ImageUploader::upload($request->file('image'), 'categories');
        }

        Category::create($validated);
        return redirect()
            ->route('admin.categories.index')
            ->with('message', __('Category created successfully!'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return Response
     */
    public function edit(Category $category): Response
    {
        $categories = Category::with('descendants')->isParent()->get();
        $flattenedCategories = $this->flattenCategories($categories);
        $category->append('image_url');

        return Inertia::render('Admin/Product/Category/Edit', [
            'category' => $category,
            'categories' => $flattenedCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryStoreUpdateRequest $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(CategoryStoreUpdateRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                ImageUploader::delete($category->image);
            }
            $validated['image'] = ImageUploader::upload($request->file('image'), 'categories');
        }

        $category->update($validated);
        return redirect()
            ->route('admin.categories.index')
            ->with('message', __('Category updated successfully!'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()
            ->route('admin.categories.index')
            ->with('message', __('Category deleted successfully!'));
    }


    /**
     * Flatten the hierarchical categories into a one-level array with path information
     */
    private function flattenCategories($categories, $prefix = '', $result = [])
    {
        foreach ($categories as $category) {
            $path = $prefix ? "$prefix > $category->name" : $category->name;
            $result[] = [
                'id' => $category->id,
                'name' => $category->name,
                'path' => $path,
                'level' => substr_count($path, '>'),
            ];

            if ($category->descendants && $category->descendants->count() > 0) {
                $result = $this->flattenCategories($category->descendants, $path, $result);
            }
        }

        return $result;
    }
}
