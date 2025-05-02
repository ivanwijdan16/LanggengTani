<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
  // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  Route::get('/', [HomeController::class, 'index'])->name('home');

  Route::prefix('stocks')->name('stocks.')->group(function () {
    Route::get('/', [StockController::class, 'index'])->name('index'); // List stok
    Route::get('/create', [StockController::class, 'create'])->name('create'); // Form tambah stok
    Route::get('/detail/{id}', [StockController::class, 'detail'])->name('detail'); // List stok
    Route::post('/', [StockController::class, 'store'])->name('store'); // Simpan stok baru
    Route::get('/{id}', [StockController::class, 'show'])->name('show'); // Detail stok
    Route::get('/{id}/edit', [StockController::class, 'edit'])->name('edit'); // Form edit stok
    Route::put('/{id}', [StockController::class, 'update'])->name('update'); // Update stok
    Route::delete('/{id}', [StockController::class, 'destroy'])->name('destroy'); // Hapus stok
  });

  // Route untuk mencari produk
  Route::get('/cart/search', [CartController::class, 'search'])->name('cart.search');

  // Route untuk melihat keranjang
  Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
  Route::get('/get-cart', [CartController::class, 'getCart'])->name('cart.get');

  // Route untuk menambahkan barang ke keranjang
  Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');

  // Route untuk menghapus barang dari keranjang
  Route::delete('/cart/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

  Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout');

  Route::prefix('pembelian')->group(function () {
    Route::get('/', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::get('/riwayat/{bulan}/{tahun}', [PembelianController::class, 'riwayatPembelianPerBulan'])->name('pembelian.riwayat');
    Route::get('/grafik/{bulan}/{tahun}', [PembelianController::class, 'grafikPembelianPerBulan'])->name('pembelian.grafik');
    Route::get('/laporan/{bulan}/{tahun}', [PembelianController::class, 'cetakLaporan'])->name('pembelian.laporan');
    Route::get('/kadaluarsa/{bulan}/{tahun}', [PembelianController::class, 'barangKadaluarsaPerBulan'])->name('pembelian.kadaluarsa');
  });

  Route::prefix('penjualan')->group(function () {
    Route::get('/', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/riwayat/{bulan}/{tahun}', [PenjualanController::class, 'riwayatPenjualanPerBulan'])->name('penjualan.riwayat');
    Route::get('/grafik/{bulan}/{tahun}', [PenjualanController::class, 'grafikPenjualanPerBulan'])->name('penjualan.grafik');
    Route::get('/laporan/{bulan}/{tahun}', [PenjualanController::class, 'cetakLaporan'])->name('penjualan.laporan');
  });

  Route::get('/notifications', [NotificationController::class, 'getNotifications']);
  Route::post('/notifications/{id}/markAsRead', [NotificationController::class, 'markAsRead']);
  Route::get('/notifications/all', [NotificationController::class, 'index'])->name('notifications.all');

  Route::resource('user', UserController::class);

  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

  // Route to update the profile (email, password)
  Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

  Route::get('/dokumentasi', [DokumentasiController::class, 'index'])->name('dokumentasi');


// Route untuk checkout (jika anda menggunakan GET method)
Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout');

// Route untuk menampilkan halaman sukses transaksi dengan ID
Route::get('/transaction/success/{id}', [TransactionController::class, 'showSuccess'])->name('transaction.success');


});

require __DIR__ . '/auth.php';