<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\Product;
use App\Models\SaleProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // ✅ Correct import
use Illuminate\Support\Facades\App;
use App\Jobs\OrderTranslationJob;

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
        'order_notes' => 'nullable|string',
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
            'order_notes' => $validatedData['order_notes'] ?? null,
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

            // Update product stock
            Product::where('id', $product['id'])->decrement('stock', $product['qty']);
        }

        // Dispatch translation job
        OrderTranslationJob::dispatch($order);

        DB::commit();
        session()->forget('cart'); // Clear cart
        return redirect()->route('user.order.list')->with('success', 'Your order has been placed successfully! Order ID: ' . $randomOrderId);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Order creation failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong! Please try again.');
    }
}


    public function orderList()
{
    $orders = Order::with('saleProducts.product')
        ->latest()
        ->paginate(10);

    return Inertia::render('admin/order/OrderList', [
        'orders' => $orders,
        'translations' => __('messages'),
        'locale' => App::getLocale(),
    ]);
}
    public function userOrderList()
    {
        try {
            $orders = Order::with('saleProducts.product')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            return Inertia::render('frontend/order/Index', [
                'orders' => [
                    'data' => $orders // Wrap the collection in a data key
                ],
                'translations' => __('messages'),
                'locale' => App::getLocale(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to load user orders: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading your orders.');
        }
    }

}
