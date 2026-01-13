<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\Log;

class CatalogController extends Controller
{
    /**
     * Get Product Catalog.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Fetch products active for the company
            $products = Product::where('company_id', $user->company_id)
                ->with(['attribute_family', 'variants']) // Eager load necessary relationships
                ->paginate(50); // Pagination for large catalogs

            // Transform data for API
            $data = $products->getCollection()->transform(function ($product) {
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price ?? 0,
                    'stock' => $product->quantity ?? 0, // Simplified stock
                    'attributes' => $product->additional ?? [], // Custom attributes
                ];
            });

            return response()->json([
                'message' => 'Catalog fetched successfully.',
                'data' => $data,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Catalog Fetch Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch catalog.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
