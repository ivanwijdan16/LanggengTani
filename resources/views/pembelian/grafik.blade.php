@extends('layouts.app')

@section('style')
  <!-- Link ke Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
  <div class="container mt-5">
    <h2 class="mb-4">Grafik Pembelian Per Bulan</h2>

    <!-- Tampilkan grafik -->
    <canvas id="grafikPembelian" width="400" height="200"></canvas>

    <div class="mt-4">
      <p><strong>Bulan:</strong> {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}</p>
      <p><strong>Tahun:</strong> {{ $tahun }}</p>
    </div>
  </div>
@endsection

@section('script')
  <script>
    // Data pembelian yang dikirim dari controller
    var totalPerHari = @json($totalPerHari);

    // Menyiapkan data grafik
    var labels = Object.keys(totalPerHari);
    var data = Object.values(totalPerHari);

    // Membuat grafik menggunakan Chart.js
    $(document).ready(function() {
      var ctx = document.getElementById('grafikPembelian').getContext('2d');
      var grafikPembelian = new Chart(ctx, {
        type: 'line', // Grafik jenis line
        data: {
          labels: labels, // Label tanggal (dari array totalPerHari)
          datasets: [{
            label: 'Total Pembelian',
            data: data, // Data total pembelian per hari
            borderColor: '#4CAF50', // Warna garis grafik
            fill: false, // Jangan mengisi area bawah grafik
            tension: 0.1 // Menyesuaikan kelengkungan garis
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            tooltip: {
              callbacks: {
                label: function(tooltipItem) {
                  return 'Rp ' + tooltipItem.raw.toLocaleString(); // Format angka sebagai IDR
                }
              }
            }
          }
        }
      });
    });
  </script>
@endsection
