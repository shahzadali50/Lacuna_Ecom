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
    dd($request->all());
    $validatedData = $request->validate([
        'name' => 'required|string',
        'phone_number' => 'required|numeric',
        'email' => 'required|email',
        'address' => 'required|string',
        'country' => 'required|string',
        'state' => 'required|string',
        'city' => 'required|string',
        'zip_code' => 'required|numeric',
        'payment_method' => 'required|string',
    ]);

    $cart = session('cart', []);

    if (empty($cart)) {
        return back()->with('error', 'Cart is empty.');
    }

    try {
        DB::beginTransaction();

        $subTotal = collect($cart)->sum(fn($product) => $product['final_price'] * $product['qty']);
        $discount = 0; // You can set dynamic discount logic
        $discountAmount = ($discount / 100) * $subTotal;
        $totalPrice = $subTotal - $discountAmount;

        $order = Order::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'address' => $request->address,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'zip_code' => $request->zip_code,
            'payment_method' => $request->payment_method,
            'user_id' => Auth::id(),
            'subtotal_price' => $subTotal,
            'discount' => $discount,
            'total_price' => $totalPrice,
        ]);

        foreach ($cart as $product) {
            SaleProduct::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'user_id' => Auth::id(),
                'sale_price' => $product['final_price'],
                'qty' => $product['qty'],
                'total_price' => $product['final_price'] * $product['qty'],
            ]);

            // Optionally update product stock here
            Product::where('id', $product['product_id'])->decrement('stock', $product['qty']);
        }

        DB::commit();
        session()->forget('cart'); // Clear cart
        return redirect()->route('home')->with('success', 'Order placed successfully!');
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
