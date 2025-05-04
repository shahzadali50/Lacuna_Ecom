<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\SaleProduct;
use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // ✅ Correct import

class OrderController extends Controller
{
 public function orderCreate()
{
    $products = Product::with(['purchaseProducts' => function ($query) {
        $query->where('remaining_stock', '>', 0);
    }])
    ->where('user_id', Auth::id())
    ->whereHas('purchaseProducts', function ($query) {
        $query->where('remaining_stock', '>', 0);
    })
    ->get(['id', 'name']);

    return Inertia::render('admin/order/Create', compact('products'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required|numeric',
            'payment_method' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0|max:100',
            'products' => 'required|array',
            'products.*.product_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Product::where('id', $value)->whereNull('deleted_at')->exists()) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
            'products.*.purchase_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!PurchaseProduct::where('id', $value)->whereNull('deleted_at')->exists()) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                },
            ],
            'products.*.qty' => 'required|integer|min:1',
            'products.*.sale_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

                        // Calculate subtotal
                $subTotal = collect($request->products)->sum(fn($product) => $product['sale_price'] * $product['qty']);

                // Calculate discount amount
                $discountAmount = ($request->discount / 100) * $subTotal;

                // Calculate total price after discount
                $totalPrice = $subTotal - $discountAmount;

                // Create the order
                $order = Order::create([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'total_price' => $totalPrice,
                    'discount' => $request->discount,
                    'subtotal_price' => $subTotal,
                    'payment_method' => $request->payment_method,
                    'user_id' => Auth::id(),
                ]);


            // Process each ordered product
            foreach ($request->products as $product) {
                $purchaseProduct = PurchaseProduct::findOrFail($product['purchase_id']);

                // Ensure stock is available
                if ($purchaseProduct->remaining_stock < $product['qty']) {
                    throw new \Exception("Insufficient stock for product ID: {$product['product_id']}");
                }

                // Create sale product record
                SaleProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'purchase_id' => $product['purchase_id'],
                    'user_id' => Auth::id(),
                    'sale_price' => $product['sale_price'],
                    'qty' => $product['qty'],
                    'total_price' => $product['sale_price'] * $product['qty'],
                ]);

                // Update stock
                $purchaseProduct->decrement('stock', $product['qty']);
                $purchaseProduct->decrement('remaining_stock', $product['qty']);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
    }

    public function orderList()
{
    $orders = Order::with('saleProducts.product') // Eager load products
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(10);

    return Inertia::render('admin/order/OrderList', compact('orders'));
}

}
