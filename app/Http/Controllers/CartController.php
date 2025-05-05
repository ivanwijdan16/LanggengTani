<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Stock;
use App\Models\StockSizeImage;
use App\Models\MasterStock;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('search', '');
        $sort = $request->input('sort', 'newest');
        $direction = $request->input('direction', 'desc');

        // Start with master stocks, possibly filtered by search
        $masterStocksQuery = MasterStock::query();

        if ($searchQuery) {
            $masterStocksQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('type', 'like', "%{$searchQuery}%")
                    ->orWhere('sku', 'like', "%{$searchQuery}%");
            });
        }

        $masterStocks = $masterStocksQuery->get();
        $products = collect();

        foreach ($masterStocks as $masterStock) {
            // Get all unique sizes for this master stock
            $sizes = Stock::where('master_stock_id', $masterStock->id)
                ->select('size')
                ->distinct()
                ->get()
                ->pluck('size');

            // For each size, get all products with the closest non-expired expiration date and available stock
            foreach ($sizes as $size) {
                // Get all non-expired products of this size
                $stocks = Stock::where('master_stock_id', $masterStock->id)
                    ->where('size', $size)
                    ->where('quantity', '>', 0)
                    ->whereDate('expiration_date', '>', Carbon::today())
                    ->get();

                if ($stocks->isNotEmpty()) {
                    // Create a virtual product that represents all stocks of this size
                    $virtualProduct = new Stock();

                    // Use the first stock as the base
                    $baseStock = $stocks->first();

                    // Copy properties from base stock
                    $virtualProduct->id = $baseStock->id;
                    $virtualProduct->master_stock_id = $baseStock->master_stock_id;
                    $virtualProduct->size = $baseStock->size;
                    $virtualProduct->stock_id = $baseStock->stock_id;
                    $virtualProduct->selling_price = $baseStock->selling_price;
                    $virtualProduct->purchase_price = $baseStock->purchase_price;
                    $virtualProduct->retail_price = $baseStock->retail_price;
                    $virtualProduct->retail_quantity = $baseStock->retail_quantity;
                    $virtualProduct->created_at = $baseStock->created_at;

                    // Calculate total quantity and quantity details
                    $totalQuantity = $stocks->sum('quantity');
                    $virtualProduct->quantity = $totalQuantity;

                    // Create expiration details array
                    $expirationDetails = $stocks->map(function ($stock) {
                        return [
                            'date' => Carbon::parse($stock->expiration_date)->format('d M Y'),
                            'quantity' => $stock->quantity
                        ];
                    })->sortBy('date');

                    // Find the nearest expiration date (for info purposes)
                    $nearestExpiration = $stocks->sortBy('expiration_date')->first()->expiration_date;
                    $virtualProduct->expiration_date = $nearestExpiration;
                    $virtualProduct->expiration_date_formatted = Carbon::parse($nearestExpiration)->format('d M Y');

                    // Add expiration details
                    $virtualProduct->expirationDetails = $expirationDetails;

                    // Get the image (either size-specific or master stock image)
                    $sizeImage = StockSizeImage::where('master_stock_id', $masterStock->id)
                        ->where('size', $size)
                        ->first();

                    if ($sizeImage && $sizeImage->image) {
                        $virtualProduct->image = $sizeImage->image;
                    } else {
                        $virtualProduct->image = $masterStock->image;
                    }

                    // Load the master stock relationship
                    $virtualProduct->load('masterStock');

                    // Add to our collection
                    $products->push($virtualProduct);
                }
            }
        }

        // Sort the products based on the request
        if ($sort === 'name') {
            $products = $direction === 'asc'
                ? $products->sortBy(function ($p) {
                    return $p->masterStock->name;
                })
                : $products->sortByDesc(function ($p) {
                    return $p->masterStock->name;
                });
        } elseif ($sort === 'price') {
            $products = $direction === 'asc'
                ? $products->sortBy('selling_price')
                : $products->sortByDesc('selling_price');
        } elseif ($sort === 'expiry') {
            $products = $direction === 'asc'
                ? $products->sortBy('expiration_date')
                : $products->sortByDesc('expiration_date');
        } elseif ($sort === 'newest') {
            $products = $direction === 'desc'
                ? $products->sortByDesc('created_at')
                : $products->sortBy('created_at');
        }

        // Get the cart items
        $carts = Cart::where('user_id', auth()->id())->get();

        return view('cart.index', compact('carts', 'products', 'searchQuery', 'sort', 'direction'));
    }

    public function getCart()
    {
        // Ambil data keranjang berdasarkan user yang sedang login
        $carts = Cart::with('product.masterStock')->where('user_id', auth()->id())->get();

        // Add the size-specific image to each cart item
        $carts->map(function ($cart) {
            if ($cart->product && $cart->product->masterStock) {
                // Get size-specific image if available
                $sizeImage = StockSizeImage::where('master_stock_id', $cart->product->master_stock_id)
                    ->where('size', $cart->product->size)
                    ->first();

                if ($sizeImage && $sizeImage->image) {
                    $cart->product->image = $sizeImage->image;
                } else {
                    $cart->product->image = $cart->product->masterStock->image;
                }
            }
            return $cart;
        });

        // Mengembalikan data ke view dengan AJAX
        return response()->json([
            'carts' => $carts
        ]);
    }

    public function search(Request $request)
    {
        $searchQuery = $request->input('query', '');
        $sortBy = $request->input('sort_by', 'created_at');
        $direction = $request->input('direction', 'desc');

        // Create a collection for our unique products
        $uniqueProducts = collect();

        // First, find all master stocks that match the search query
        $masterStocksQuery = MasterStock::query();

        if ($searchQuery) {
            $masterStocksQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('type', 'like', "%{$searchQuery}%")
                    ->orWhere('sku', 'like', "%{$searchQuery}%");
            });
        }

        $masterStocks = $masterStocksQuery->get();

        // For each master stock, find all available products (non-expired, with quantity)
        foreach ($masterStocks as $masterStock) {
            // Get distinct sizes for this master stock
            $sizes = Stock::where('master_stock_id', $masterStock->id)
                ->select('size')
                ->distinct()
                ->get()
                ->pluck('size');

            // For each size, get all non-expired stocks with quantity
            foreach ($sizes as $size) {
                $stocks = Stock::where('master_stock_id', $masterStock->id)
                    ->where('size', $size)
                    ->where('quantity', '>', 0)
                    ->whereDate('expiration_date', '>', Carbon::today())
                    ->get();

                if ($stocks->isNotEmpty()) {
                    // Create a virtual product that represents all stocks of this size
                    $virtualProduct = new Stock();

                    // Use the first stock as the base
                    $baseStock = $stocks->first();

                    // Copy properties from base stock
                    $virtualProduct->id = $baseStock->id;
                    $virtualProduct->master_stock_id = $baseStock->master_stock_id;
                    $virtualProduct->size = $baseStock->size;
                    $virtualProduct->stock_id = $baseStock->stock_id;
                    $virtualProduct->selling_price = $baseStock->selling_price;
                    $virtualProduct->purchase_price = $baseStock->purchase_price;
                    $virtualProduct->retail_price = $baseStock->retail_price;
                    $virtualProduct->retail_quantity = $baseStock->retail_quantity;
                    $virtualProduct->created_at = $baseStock->created_at;

                    // Calculate total quantity
                    $totalQuantity = $stocks->sum('quantity');
                    $virtualProduct->quantity = $totalQuantity;

                    // Create expiration details array
                    $expirationDetails = $stocks->map(function ($stock) {
                        return [
                            'date' => Carbon::parse($stock->expiration_date)->format('d M Y'),
                            'quantity' => $stock->quantity
                        ];
                    })->sortBy('date');

                    // Find the nearest expiration date
                    $nearestExpiration = $stocks->sortBy('expiration_date')->first()->expiration_date;
                    $virtualProduct->expiration_date = $nearestExpiration;
                    $virtualProduct->expiration_date_formatted = Carbon::parse($nearestExpiration)->format('d M Y');

                    // Add expiration details
                    $virtualProduct->expirationDetails = $expirationDetails;

                    // Load the master stock relationship
                    $virtualProduct->load('masterStock');
                    $virtualProduct->master_stock = $virtualProduct->masterStock;

                    // Get size-specific image if available
                    $sizeImage = StockSizeImage::where('master_stock_id', $virtualProduct->master_stock_id)
                        ->where('size', $virtualProduct->size)
                        ->first();

                    if ($sizeImage && $sizeImage->image) {
                        $virtualProduct->image = $sizeImage->image;
                    } else {
                        $virtualProduct->image = $virtualProduct->masterStock->image;
                    }

                    $uniqueProducts->push($virtualProduct);
                }
            }
        }

        // Apply final sorting based on user's sort preference
        if ($sortBy === 'name') {
            $uniqueProducts = $direction === 'asc'
                ? $uniqueProducts->sortBy(function ($p) {
                    return $p->masterStock->name;
                })
                : $uniqueProducts->sortByDesc(function ($p) {
                    return $p->masterStock->name;
                });
        } elseif ($sortBy === 'price') {
            $uniqueProducts = $direction === 'asc'
                ? $uniqueProducts->sortBy('selling_price')
                : $uniqueProducts->sortByDesc('selling_price');
        } elseif ($sortBy === 'expiry') {
            $uniqueProducts = $direction === 'asc'
                ? $uniqueProducts->sortBy('expiration_date')
                : $uniqueProducts->sortByDesc('expiration_date');
        } elseif ($sortBy === 'newest') {
            $uniqueProducts = $direction === 'desc'
                ? $uniqueProducts->sortByDesc('created_at')
                : $uniqueProducts->sortBy('created_at');
        }

        return response()->json([
            'products' => $uniqueProducts->values()->all()
        ]);
    }

    // Menambahkan produk ke keranjang
    public function addToCart(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        // Ambil produk berdasarkan ID
        $product = Stock::findOrFail($request->product_id);

        // Cek apakah produk sudah ada di keranjang
        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('type', $request->type)
            ->first();

        $price = $request->type == 'normal' ? $product->selling_price : $product->retail_price;

        // Jika produk sudah ada di keranjang, update jumlahnya
        if ($cart) {
            $cart->quantity += $request->quantity;
            $cart->subtotal = $cart->quantity * $price;
            $cart->save();
        } else {
            // Jika produk belum ada, buat entri baru di keranjang
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'subtotal' => $request->quantity * $price,
                'type' => $request->type
            ]);
        }

        // Kembalikan respons sukses
        return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang']);
    }

    // Menghapus produk dari keranjang
    public function removeFromCart($id)
    {
        // Hapus produk berdasarkan ID keranjang
        $cart = Cart::findOrFail($id);

        // Pastikan hanya menghapus milik user yang sedang login
        if ($cart->user_id === auth()->id()) {
            $cart->delete();
        }

        // Mengembalikan respons setelah penghapusan
        return response()->json(['message' => 'Produk berhasil dihapus dari keranjang']);
    }
}
