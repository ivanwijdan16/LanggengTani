<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
  // Menampilkan halaman utama menu penjualan
  public function index(Request $request)
  {
    $bulan = $request->query('bulan', now()->month);
    $tahun = $request->query('tahun', now()->year);

    $penjualans = Transaction::with(['items.product' => function($query) {
        $query->withTrashed();
    }])
    ->whereYear('created_at', $tahun)
    ->whereMonth('created_at', $bulan)
    ->get();

    // Initialize an array to store total sales per day
    $totalPerHari = [];

    // Loop through each transaction and calculate the total sales per day
    foreach ($penjualans as $penjualan) {
      // Format the date (using Carbon for consistency)
      $tanggal = $penjualan->created_at->format('d-m-Y');

      // If the date is not already in the array, initialize it to 0
      if (!isset($totalPerHari[$tanggal])) {
        $totalPerHari[$tanggal] = 0;
      }

      // Add the total price of the transaction to that date
      $totalPerHari[$tanggal] += $penjualan->total_price;
    }

    // Pass the necessary data to the view
    return view('penjualan.index', compact('penjualans', 'totalPerHari', 'bulan', 'tahun'));
  }

  // Menampilkan riwayat penjualan per bulan
  public function riwayatPenjualanPerBulan($bulan, $tahun)
  {
    $penjualans = Transaction::whereYear('created_at', $tahun)
      ->whereMonth('created_at', $bulan)
      ->get();

    return view('penjualan.riwayat', compact('penjualans'));
  }

  // Menampilkan grafik penjualan per bulan
  public function grafikPenjualanPerBulan($bulan, $tahun)
  {
    $penjualans = Transaction::whereYear('created_at', $tahun)
      ->whereMonth('created_at', $bulan)
      ->get();

    $totalPerHari = [];
    foreach ($penjualans as $penjualan) {
      $tanggal = date('d-m-Y', strtotime($penjualan->created_at));
      if (!isset($totalPerHari[$tanggal])) {
        $totalPerHari[$tanggal] = 0;
      }
      $totalPerHari[$tanggal] += $penjualan->total_price;
    }

    return view('penjualan.grafik', compact('totalPerHari', 'bulan', 'tahun'));
  }

  // Cetak laporan penjualan per bulan
  public function cetakLaporan($bulan, $tahun)
{
    // Get the base query for transaction items first
    $penjualans = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
        ->whereYear('transactions.created_at', $tahun)
        ->whereMonth('transactions.created_at', $bulan);

    // Then do a separate subquery to get stocks including trashed ones
    $stocksWithTrashed = DB::table('stocks')
        ->select('id', 'name', 'purchase_price', 'size')
        ->whereNull('deleted_at')
        ->union(
            DB::table('stocks')
                ->select('id', 'name', 'purchase_price', 'size')
                ->whereNotNull('deleted_at')
        );

    // Now join with our combined result
    $penjualans = $penjualans
        ->leftJoinSub($stocksWithTrashed, 'stocks', function($join) {
            $join->on('transaction_items.product_id', '=', 'stocks.id');
        })
        ->select(
            'transactions.id_penjualan as id_penjualan',
            'transaction_items.quantity',
            'transaction_items.price as unit_price',
            'stocks.name as product_name',
            'stocks.purchase_price',
            'transaction_items.subtotal as total_price',
            'transactions.created_at as sale_date'
        )
        ->get();

    // Membuat PDF menggunakan data yang sudah didapat
    $pdf = PDF::loadView('penjualan.laporan', compact('penjualans'));
    $pdf->setOption('orientation', 'landscape');

    // Mengunduh PDF
    return $pdf->download("laporan_penjualan_{$bulan}_{$tahun}.pdf");
}
}
