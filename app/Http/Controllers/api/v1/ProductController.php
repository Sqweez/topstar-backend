<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductBatchRequest;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Resources\Product\ProductsListResource;
use App\Models\Club;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection {
        $products = Product::query()
            ->when($request->has('store_id'), function ($query) {
                return $query->whereHas('batches', function ($q) {
                    return $q->where('store_id', \request('store_id'));
                });
            })
            ->with(['batches.club', 'category'])
            ->get();

        return ProductsListResource::collection($products);
    }

    public function store(CreateProductRequest $request) {
        $product = Product::create($request->validated())->refresh();
        return $this->respondSuccess([
            'product' => new ProductsListResource($product)
        ], 'Товар успешно создан!');
    }

    public function update() {

    }

    public function destroy() {

    }

    public function createProductBatch(Product $product, CreateProductBatchRequest $request): \Illuminate\Http\JsonResponse {
        $product->batches()->create($request->validated());
        $product->refresh();
        $product->load('batches.club');
        return $this->respondSuccess([
            'quantities' => $product->collectQuantities()
        ], 'Количество товара успешно обновлено');
    }
}
