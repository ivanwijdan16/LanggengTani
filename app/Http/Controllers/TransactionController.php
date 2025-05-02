<?php

// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
  public function checkout(Request $request)
  {
    // Ambil semua barang yang ada di keranjang
    $carts = Cart::where('user_id', auth()->id())->get();

    if ($carts->isEmpty()) {
      return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong!');
    }

    $total_price = $carts->sum('subtotal'); // Hitung total harga dari keranjang
    $total_paid = $request->total_paid ?? 0; // Uang yang dibayar
    $change = $total_paid - $total_price; // Kembalian

    if ($change < 0) {
      return redirect()->route('cart.index')->with('error', 'Uang tidak mencukupi!');
    }

    // Buat ID Penjualan (misalnya PJ-YYYYMMDD-xxx)
    $id_penjualan = 'PJ-' . date('Ymd') . '-' . str_pad(Transaction::count() + 1, 3, '0', STR_PAD_LEFT); // Format: PJ-20250217-001

    foreach ($carts as $cart) {
      $product = $cart->product; // Ambil produk yang dibeli

      if ($cart->type == 'normal') {
        $product->quantity -= $cart->quantity; // Kurangi stok sesuai dengan jumlah yang dibeli

        if ($product->quantity < 0) {
          // Jika stok kurang dari 0, kita bisa menambahkan pengecekan error atau memberi notifikasi
          return redirect()->route('cart.index')->with('error', 'Stok produk tidak mencukupi!');
        }
      } else {
        $product->retail_quantity -= $cart->quantity; // Kurangi stok sesuai dengan jumlah yang dibeli

        if ($product->retail_quantity < 0) {
          // Jika stok kurang dari 0, kita bisa menambahkan pengecekan error atau memberi notifikasi
          return redirect()->route('cart.index')->with('error', 'Stok produk tidak mencukupi!');
        }
      }
    }

    // Simpan transaksi baru dengan ID Penjualan
    $transaction = Transaction::create([
      'user_id' => auth()->id(),
      'total_price' => $total_price,
      'total_paid' => $total_paid,
      'change' => $change,
      'id_penjualan' => $id_penjualan, // Tambahkan ID Penjualan
    ]);

    // Loop untuk setiap item di keranjang dan mengurangi stok produk
    foreach ($carts as $cart) {
      $product = $cart->product; // Ambil produk yang dibeli

      // Simpan perubahan stok
      $product->save();

      // Jika method checkAndCreateNotifications ada
      if (method_exists($product, 'checkAndCreateNotifications')) {
        $product->checkAndCreateNotifications();
      }

      // Simpan detail transaksi ke tabel transaction_items
      $transaction->items()->create([
        'product_id' => $product->id,
        'quantity' => $cart->quantity,
        'price' => $cart->type == 'normal' ? $product->selling_price : $product->retail_price,  // Harga per unit produk
        'subtotal' => $cart->subtotal  // Subtotal per produk (harga * quantity)
      ]);
    }

    // Hapus barang dari keranjang setelah transaksi selesai
    Cart::where('user_id', auth()->id())->delete();

    // Alihkan ke halaman success dengan ID transaksi di URL
    return redirect()->route('transaction.success', ['id' => $transaction->id]);
  }

  // Method baru untuk menampilkan halaman sukses
  public function showSuccess($id)
{
    // Cari transaksi berdasarkan ID dan muat relasi items dengan produk (termasuk yang sudah soft delete)
    $transaction = Transaction::with(['items.product' => function($query) {
        $query->withTrashed(); // Ini akan mengambil produk meskipun sudah dihapus (soft deleted)
    }])->findOrFail($id);

    // Pastikan transaksi ini milik user yang sedang login
    if ($transaction->user_id != auth()->id()) {
        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke transaksi ini!');
    }

    // Tampilkan view success dengan data transaksi
    return view('transaction.success', compact('transaction'));
    }
}
