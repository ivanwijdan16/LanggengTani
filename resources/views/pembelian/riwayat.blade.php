@extends('layouts.app')

@section('content')
  <div class="container mt-5">
    <h2>Riwayat Pembelian</h2>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID Pembelian</th>
          <th>Nama Barang</th>
          <th>Tanggal Pembelian</th>
          <th>Jumlah</th>
          <th>Harga Satuan</th>
          <th>Total Harga</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($pembelians as $pembelian)
          <tr>
            <td>{{ $pembelian->purchase_code }}</td>
            <td>{{ $pembelian->stock->name }}</td>
            <td>{{ \Carbon\Carbon::parse($pembelian->purchase_date)->format('d/m/Y') }}, {{ \Carbon\Carbon::parse($pembelian->created_at)->format('H:i') }}</td>
            <td>{{ $pembelian->quantity }}</td>
            <td>Rp {{ number_format($pembelian->purchase_price, 2, ',', '.') }}</td>
            <td>Rp {{ number_format($pembelian->purchase_price * $pembelian->quantity, 2, ',', '.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
