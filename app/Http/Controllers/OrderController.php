<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\SaleProduct;
use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // ✅ Correct import
use Illuminate\Support\Facades\App;

class OrderController extends Controller
{


public function orderGenerate(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string',
        'phone_number' => 'required|numeric',
        'email' => 'required|email',
        'address' => 'required|string',
        'country' => 'required|string',
        'state' => 'required|string',
        'city' => 'required|string',
        'postal_code' => 'required|numeric',
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

        // Generate a random order ID with prefix ORD-
        $randomOrderId = 'ORD-' . rand(100000, 999999);

        $order = Order::create([
            'name' => $validatedData['name'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'country' => $validatedData['country'],
            'state' => $validatedData['state'],
            'city' => $validatedData['city'],
            'postal_code' => $validatedData['postal_code'],
            'payment_method' => $validatedData['payment_method'],
            'user_id' => Auth::id(),
            'subtotal_price' => $subTotal,
            'discount' => $discount,
            'total_price' => $totalPrice,
            'order_id' => $randomOrderId,
        ]);

        foreach ($cart as $product) {
            SaleProduct::create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'user_id' => Auth::id(),
                'sale_price' => $product['final_price'],
                'qty' => $product['qty'],
                'total_price' => $product['final_price'] * $product['qty'],
            ]);

            // Optionally update product stock here
            Product::where('id', $product['id'])->decrement('stock', $product['qty']);
        }

        DB::commit();
        session()->forget('cart'); // Clear cart
        return redirect()->back()->with('success', 'Order Created successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order creation failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong! Please try again.');
    }
}


    public function orderList()
{
    $orders = Order::with('saleProducts.product') // Eager load products
        ->latest()
        ->paginate(10);

    return Inertia::render('admin/order/OrderList', [
        'orders' => $orders,
        'translations' => __('messages'),
        'locale' => App::getLocale(),
    ]);
}
    public function userOrderDetail()
    {
        $orders = Order::with('saleProducts.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('frontend/track_order/Index', [
            'orders' => $orders,
            'translations' => __('messages'),
            'locale' => App::getLocale(),
        ]);
    }

}
