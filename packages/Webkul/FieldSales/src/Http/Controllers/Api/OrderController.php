<?php

namespace Webkul\FieldSales\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\FieldSales\Models\Order;
use Webkul\FieldSales\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $productInventoryRepository;

    public function __construct(
        \Webkul\Product\Repositories\ProductInventoryRepository $productInventoryRepository
    ) {
        $this->productInventoryRepository = $productInventoryRepository;
    }

    /**
     * List Orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with(['person', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'message' => 'Orders fetched successfully.',
            'data' => $orders
        ]);
    }

    /**
     * Create Order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'type' => 'required|in:primary,secondary',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();

            // 1. Validate Stock & Calculate Total
            $grandTotal = 0;
            foreach ($request->items as $item) {
                $grandTotal += $item['qty'] * $item['price'];

                // Stock Check
                $inventory = $this->productInventoryRepository->findOneWhere([
                    'product_id' => $item['product_id']
                ]);

                if (!$inventory || $inventory->in_stock < $item['qty']) {
                    throw new \Exception("Insufficient stock for Product ID: " . $item['product_id']);
                }
            }

            // 2. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'person_id' => $request->person_id,
                'type' => $request->type,
                'status' => 'pending',
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
                'delivery_date' => $request->delivery_date,
            ]);

            // 3. Create Items & Deduct Stock
            foreach ($request->items as $item) {
                OrderItem::create([
                    'field_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);

                // Deduct Stock
                $inventory = $this->productInventoryRepository->findOneWhere([
                    'product_id' => $item['product_id']
                ]);

                if ($inventory) {
                    $inventory->decrement('in_stock', $item['qty']);
                }

                // Update Product Total Quantity Cache if exists
                $product = \Webkul\Product\Models\Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('quantity', $item['qty']);
                }
            }

            DB::commit();

            \Illuminate\Support\Facades\Event::dispatch('field_sales.order.created', $order);

            return response()->json([
                'message' => 'Order created successfully.',
                'data' => $order->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 400); // 400 Bad Request
        }
    }
}
