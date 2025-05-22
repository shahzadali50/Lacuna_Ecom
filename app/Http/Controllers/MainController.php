<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Auth\LoginRequest;

class MainController extends Controller
{

    public function index()
    {
        try {
            $locale = session('locale', App::getLocale());
            // Load categories with product count and translations
            $categories = Category::withCount('products')
                ->with([
                    'category_translations' => fn($q) => $q->where('lang', $locale),
                ])
                ->get()
                ->map(function ($category) use ($locale) {
                    return [
                        'id' => $category->id,
                        'name' => $category->category_translations->first()?->name ?? $category->name,
                        'image' => $category->image,
                        'product_count' => $category->products_count,
                    ];
                });


            // Load products with brand, category, and their translations for current locale
            $products = Product::where('status', 1)
                ->with([
                    'category' => fn($q) => $q->with(['category_translations' => fn($q) => $q->where('lang', $locale)]),
                    'product_translations' => fn($q) => $q->where('lang', $locale),
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Transform products to include translated fields
            $products->getCollection()->transform(function ($product) use ($locale) {
                return [
                    'id' => $product->id,
                    'name' => $product->product_translations->first()?->name ?? $product->name,
                    'slug' => $product->slug,
                    'thumnail_img' => $product->thumnail_img,
                    'sale_price' => $product->sale_price,
                    'final_price' => $product->final_price,
                    'category_name' => $product->category?->category_translations->first()?->name ?? $product->category?->name ?? 'N/A',
                ];
            });
            // dd($products->sale_price);
            return Inertia::render('frontend/Index', [
                'categories' => $categories,
                'products' => $products,
                'translations' => __('messages'),
                'locale' => $locale,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to load products in index(): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading products.');
        }
    }

    public function productDetail($slug)
    {
        try {
            $locale = session('locale', App::getLocale());

            $product = Product::where('slug', $slug)
                ->with([
                    'category' => fn($q) => $q->with(['category_translations' => fn($q) => $q->where('lang', $locale)]),
                    'product_translations' => fn($q) => $q->where('lang', $locale),
                ])
                ->first();

            if (!$product) {
                return redirect()->back()->with('error', 'Product detail not found.');
            }
            // dd($product->gallary_img);
            return Inertia::render('frontend/products/ProductDetail', [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->product_translations->first()?->name ?? $product->name,
                    'description' =>  $product->product_translations->first()?->description ?? $product->description,
                    'slug' => $product->slug,
                    'final_price' => $product->final_price,
                    'sale_price' => $product->sale_price,
                    'discount' => $product->discount,
                    'stock' => $product->stock,
                    'thumbnail_image' => $product->thumnail_img ?? null,
                    'gallery_images' => collect(
                        is_array($product->gallary_img)
                            ? $product->gallary_img
                            : (is_string($product->gallary_img) && str_starts_with($product->gallary_img, '[')
                                ? json_decode($product->gallary_img, true)
                                : explode(',', $product->gallary_img)
                            )
                    )->map(fn($img) => asset("storage/" . trim($img)))->toArray(),
                    'category_name' => $product->category?->category_translations->first()?->name ?? $product->category?->name ?? 'N/A',
                ],
                'translations' => __('messages'),
                'locale' => $locale,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Product detail error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }


    public function switchLanguage($locale)
    {
        if (in_array($locale, ['en', 'pt', 'ja'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }

    public function cacheClear()
    {
        try {
            // Run the Artisan command to clear caches
            Artisan::call('optimize:clear');
            // Return success message using Inertia flash data
            Log::info('Application cache cleared successfully!');
            return redirect()->back()->with('success', 'Application cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Cache clear error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function migrate()
    {
        try {
            // Run the Artisan command to clear caches
            Artisan::call('migrate');
            // Return success message using Inertia flash data
            Log::info('Database migrated successfully.');
            return redirect()->back()->with('success', 'Database migrated successfully.');
        } catch (\Exception $e) {
            Log::error('Migration error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }
    public function storageLink()
    {
        try {
            Artisan::call('storage:link');
            Log::info('Storage link created successfully.');
            return redirect()->back()->with('success', 'Storage link created successfully.');
        } catch (\Exception $e) {
            Log::error('Storage link error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Storage link failed: ' . $e->getMessage());
        }
    }
    public function checkRole()
    {
        if (auth()->check()) {
            if (auth()->user()->role == "admin") {

                return redirect()->route('admin.dashboard')->with('success', 'login successfully');
            } else {

                return redirect()->route('profile.edit')->with('success', 'login successfully');
            }
        } else {

            return redirect()->route('login');
        }
    }
    public function dashboard()
    {
        try {
            $brands = Brand::where('user_id', Auth::id())->count();
            $totalProduct = Product::where('user_id', Auth::id())->count();
            $category = Category::where('user_id', Auth::id())->count();

            // Send all orders instead of filtering them
            $orders = Order::select('total_price', 'created_at')->get();

            return Inertia::render('admin/Dashboard', [
                'brands' => $brands,
                'totalProduct' => $totalProduct,
                'category' => $category,
                'orders' => $orders,
                'translations' => __('messages'),
                'locale' => App::getLocale(),
            ]);
        } catch (\Throwable $e) {
            // Optional: log the error for debugging
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // You can return a fallback view or redirect with error
            return redirect()->back()->withErrors(['message' => 'Something went wrong while loading the dashboard.']);
        }
    }
    public function loginModal(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        return back()->with('success', 'login successfully');
    }
}
