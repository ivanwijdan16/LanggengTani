<?php

// app/Http/Controllers/CartController.php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Stock;
use Illuminate\Http\Request;

class CartController extends Controller
{
  public function index()
  {
    $carts = Cart::all();
    return view('cart.index', compact('carts'));
  }

  public function getCart()
  {
    // Ambil data keranjang berdasarkan user yang sedang login
    $carts = Cart::with('product.masterStock')->where('user_id', auth()->id())->get();

    // Mengembalikan data ke view dengan AJAX
    return response()->json([
      'carts' => $carts
    ]);
  }

  public function search(Request $request)
  {
    $searchQuery = $request->input('query', ''); // Rename $query to $searchQuery to avoid conflict
    $sortBy = $request->input('sort_by', 'created_at'); // Default sort field: created_at
    $direction = $request->input('direction', 'desc'); // Default direction: desc

    // Membuat query dasar
    $productsQuery = Stock::with('masterStock') // Eager load the masterStock relation
      ->whereHas('masterStock', function ($query) use ($searchQuery) {
        // Filter by masterStock name using the renamed $searchQuery
        $query->where('name', 'like', "%{$searchQuery}%");
      })
      ->whereDate('expiration_date', '>', \Carbon\Carbon::today()); // Filter produk yang belum expired

    // Mapping sort option dari frontend ke field di database
    $sortField = 'created_at'; // Default field

    switch ($sortBy) {
      case 'name':
        // Sort by masterStock's name
        $sortField = 'master_stocks.name';
        break;
      case 'price':
        $sortField = 'selling_price';
        break;
      case 'expiry':
        $sortField = 'expiration_date';
        break;
      case 'newest':
        $sortField = 'created_at';
        break;
    }

    // Menerapkan pengurutan
    $productsQuery->orderBy($sortField, $direction);

    // Batasi jumlah produk dan ambil data
    $products = $productsQuery->limit(12)
      ->get()
      ->map(function ($product) {
        // Add a custom expired field
        $product->expired = \Carbon\Carbon::parse($product->expiration_date)->isPast();
        // Format expiration_date
        $product->expiration_date = \Carbon\Carbon::parse($product->expiration_date)->format('d M Y');

        // Add master stock details to the product
        if ($product->masterStock) {
          $product->master_stock = $product->masterStock; // Including related master stock data
        }

        return $product;
      });

    return response()->json([
      'products' => $products
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
