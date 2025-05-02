@extends('layouts.app')

@section('style')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
  <div class="container mt-5">
    <h2 class="text-center mb-4">Grafik Penjualan</h2>
    <canvas id="grafikPenjualan" width="400" height="200"></canvas>
  </div>
@endsection

@section('script')
  <script>
    var totalPerHari = @json($totalPerHari);
    var labels = Object.keys(totalPerHari);
    var data = Object.values(totalPerHari);

    $(document).ready(function() {
      var ctx = document.getElementById('grafikPenjualan').getContext('2d');
      var grafikPenjualan = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Total Penjualan',
            data: data,
            borderColor: '#4CAF50',
            fill: false,
            tension: 0.1
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
                  return 'Rp ' + tooltipItem.raw.toLocaleString();
                }
              }
            }
          }
        }
      });
    });
  </script>
@endsection
