@extends('layouts.app')

@section('style')
<style>
  .table-container {
    border-radius: 15px;
    overflow: hidden;
    background-color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 25px;
  }

  .table thead th {
    background-color: #f8fafc;
    color: #475569 !important;
    font-weight: 600;
    padding: 15px;
    border-top: none;
  }

  .table tbody td {
    padding: 15px;
    vertical-align: middle;
    color: #334155;
    border-color: #f1f5f9;
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.5rem;
  }

  .btn-outline-primary {
    color: #149d80;
    border-color: #149d80;
  }

  .btn-outline-primary:hover {
    background-color: #149d80;
    color: white;
  }

  .items-list {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .items-list li {
    padding: 5px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 0.9rem;
  }

  .items-list li:last-child {
    border-bottom: none;
  }

  .items-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #149d80;
    color: white;
    border-radius: 30px;
    padding: 0.15rem 0.5rem;
    font-size: 0.7rem;
    margin-left: 0.5rem;
  }

  .collapse-card {
    border-radius: 10px;
    padding: 10px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    margin-top: 8px;
  }

  .page-title {
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    font-size: 1.5rem;
  }

  .page-title i {
    color: #149d80;
    margin-right: 10px;
    font-size: 1.5rem;
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
  <h1 class="page-title">
    <i class="bx bx-history"></i> Riwayat Penjualan
  </h1>

  <!-- Tabel Riwayat Penjualan -->
  <div class="table-container">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID Penjualan</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Total Harga</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($penjualans as $penjualan)
            <tr>
              <td>{{ $penjualan->id_penjualan }}</td>
              <td>{{ \Carbon\Carbon::parse($penjualan->created_at)->format('d-m-Y') }}, {{ \Carbon\Carbon::parse($penjualan->created_at)->format('H:i') }}</td>
              <td>
                @if(isset($penjualan->items) && count($penjualan->items) > 0)
                  <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $penjualan->id_penjualan }}">
                    Lihat Barang <span class="items-badge">{{ count($penjualan->items) }}</span>
                  </button>
                  <div class="collapse" id="collapse{{ $penjualan->id_penjualan }}">
                    <div class="collapse-card">
                      <ul class="items-list">
                        @foreach($penjualan->items as $item)
                          <li>{{ $item->product_name ?? $item->product->name ?? 'Barang' }} ({{ $item->quantity }} pcs)</li>
                        @endforeach
                      </ul>
                    </div>
                  </div>
                @else
                  <span class="text-muted">Tidak ada detail</span>
                @endif
              </td>
              <td>Rp {{ number_format($penjualan->total_price, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
