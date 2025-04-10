<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageUploader;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductVariationTypeStore;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Company;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\ProductVariationOption;
use App\Models\ProductVariationType;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends ApiController
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        $perPage = request('perPage', 10);
        $query = Category::isParent();
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
        $categories = $query->paginate($perPage);
        return $this->collectionRespond(CategoryListResource::collection($categories));
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function categoryDetail(Category $category): JsonResponse
    {
        $category->load('children');
        return $this->detailRespond(
            new CategoryListResource($category)
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $perPage = request('perPage', 10);
        $cacheKey = 'products_page_' . request('page', 1);

        $products = Cache::remember($cacheKey, now()->addMinutes(20), function () use ($perPage) {
            return Product::with('company')->paginate($perPage);
        });

        return $this->collectionRespond(
            ProductListResource::collection($products)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'company_id' => $validated['company_id'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'status' => $validated['status'],
            'quantity' => $validated['quantity'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product
                    ->addMedia($image)
                    ->toMediaCollection('images');
            }
        }

        return $this->detailRespond(
            new ProductResource($product)
        );
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product
                    ->addMedia($image)
                    ->toMediaCollection('images');
            }
        }

        return response()->json([
            'message' => 'Images uploaded successfully.',
            'images' => $product->getMedia('images')->map(fn($media) => $media->getUrl('small')),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return $this->respondSuccess(
            new ProductResource($product)
        );
    }
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        // Validate and update product logic here
        // ...
        return $this->respondSuccess(
            new ProductResource($product)
        );
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        // Delete product logic here
        if ($product->images) {
            foreach ($product->images as $image) {
                $image->delete();
            }
        }

        $product->delete();

        return $this->respondSuccess(
            new ProductResource($product)
        );
    }


    /**
     * Store variation types and options for a product.
     *
     * @param ProductVariationTypeStore $request
     * @param Product $product
     * @return JsonResponse
     */
    public function storeOrUpdateVariationTypes(ProductVariationTypeStore $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            foreach ($request->variationTypes as $vtIndex => $variationTypeData) {
                // Create or update variation type
                $variationType = isset($variationTypeData['id'])
                    ? $product->variationTypes()->find($variationTypeData['id'])
                    : null;

                if ($variationType) {
                    $variationType->update([
                        'name' => $variationTypeData['name'],
                        'type' => $variationTypeData['type'],
                    ]);
                } else {
                    $variationType = $product->variationTypes()->create([
                        'name' => $variationTypeData['name'],
                        'type' => $variationTypeData['type'],
                    ]);
                }

                // Process options
                foreach ($variationTypeData['options'] ?? [] as $opIndex => $optionData) {
                    $option = isset($optionData['id'])
                        ? $variationType->options()->find($optionData['id'])
                        : null;

                    if ($option) {
                        $option->update(['name' => $optionData['name']]);
                    } else {
                        $option = $variationType->options()->create([
                            'name' => $optionData['name'],
                        ]);
                    }

                    // Attach new images if provided
                    if (request()->hasFile("variationTypes.$vtIndex.options.$opIndex.images")) {
                        // Optionally clear old images
                        $option->clearMediaCollection('images');

                        foreach (request()->file("variationTypes.$vtIndex.options.$opIndex.images") as $image) {
                            $option->addMedia($image)->toMediaCollection('images');
                        }
                    }
                }
            }
        });

        return response()->json(['message' => 'Variation types and options saved successfully.']);
    }


    public function getProductVariations(Product $product)
    {
        $variations = $product->variations->toArray();
        $variations = $this->productService->mergeCartesianWithExisting($product->variationTypes, $variations, $product);

        return response()->json([
            'variations' => $variations
        ]);
    }





    public function updateProductVariations(Request $request, Product $product)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'variations' => 'required|array',
            'variations.*' => 'required|array',
            'variations.*.variation_type_*' => 'required|array',
            'variations.*.quantity' => 'required|integer',
            'variations.*.price' => 'required|numeric',
        ]);


        // Process the variations
        $data = $this->productService->mutateFormDataBeforeSave($validatedData, $product);


        // Update the product with the variations
        $updatedProduct = $this->productService->handleRecordUpdate($product, $data);

        return response()->json([
            'message' => 'Product variations updated successfully',
            'product' => new ProductResource($updatedProduct)
        ]);
    }
}
