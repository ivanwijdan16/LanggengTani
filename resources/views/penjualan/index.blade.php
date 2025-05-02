@extends('layouts.app')

@section('style')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  @vite(['resources/css/penjualan/index.css'])

@endsection

@section('content')
<div class="container-fluid py-4">
    <h1 class="section-title mb-4">
        <i class="bx bx-cart-alt"></i> Menu Penjualan
      </h1>

  <!-- Filter Card -->
  <div class="filter-card mb-5">
    <div class="row g-3">
      <div class="col-md-4">
        <label for="bulan" class="form-label">Pilih Bulan</label>
        <select id="bulan" name="bulan" class="form-select">
  @php
    Carbon\Carbon::setLocale('id');
  @endphp

  @foreach (range(1, 12) as $bulanSelect)
    <option value="{{ $bulanSelect }}"
      {{ $bulanSelect == request('bulan', now()->month) ? 'selected' : '' }}>
      {{ Carbon\Carbon::create()->month($bulanSelect)->translatedFormat('F') }}
    </option>
  @endforeach
</select>
      </div>

      <div class="col-md-4">
        <label for="tahun" class="form-label">Pilih Tahun</label>
        <select id="tahun" name="tahun" class="form-select">
          @foreach (range(now()->year - 5, now()->year + 5) as $tahun)
            <option value="{{ $tahun }}" {{ $tahun == request('tahun', now()->year) ? 'selected' : '' }}>
              {{ $tahun }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4 d-flex align-items-end">
        <div class="d-flex gap-2 w-100">
          <button type="button" id="btn-tampilkan" class="btn btn-primary flex-grow-1">
            <i class="bx bx-search me-1"></i> Tampilkan Data
          </button>
          <a id="btn-cetak" href="#" class="btn btn-success flex-grow-1" target="_blank">
            <i class="bx bx-printer me-1"></i> Cetak Laporan
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Riwayat Penjualan -->
    <div class="col-lg-6">
      <h2 class="section-title">
        <i class="bx bx-history"></i> Riwayat Penjualan
      </h2>

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
              @php
                $total = 0;
              @endphp
              @foreach ($penjualans as $penjualan)
                <tr>
                  <td><strong>{{ $penjualan->id_penjualan }}</strong></td>
                  <td>{{ \Carbon\Carbon::parse($penjualan->created_at)->format('d-m-Y') }}, {{ \Carbon\Carbon::parse($penjualan->created_at)->format('H:i') }}</td>
                  <td>
                    @if(isset($penjualan->items) && count($penjualan->items) > 0)
                    <button class="btn btn-sm btn-outline-primary" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse{{ $penjualan->id_penjualan }}"
                    aria-expanded="false"
                    aria-controls="collapse{{ $penjualan->id_penjualan }}">
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
                @php
                  $total += $penjualan->total_price;
                @endphp
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Grafik Penjualan -->
    <div class="col-lg-6">
      <h2 class="section-title">
        <i class="bx bx-line-chart"></i> Grafik Penjualan
      </h2>

      <div class="chart-container">
        <canvas id="grafikPenjualan"></canvas>
      </div>
    </div>
  </div>

  <!-- Total Penjualan -->
  <div class="total-box">
    <h3>Total Penjualan: Rp {{ number_format($total, 0, ',', '.') }}</h3>
  </div>
</div>
@endsection

@section('script')
<script>
  var totalPerHari = @json($totalPerHari);
  var labels = Object.keys(totalPerHari);
  var data = Object.values(totalPerHari);

  // Debug data
  console.log("Labels:", labels);
  console.log("Data:", data);

  $(document).ready(function() {
    // Mengubah URL tombol saat dropdown bulan atau tahun berubah
    function updateLinks() {
      var bulan = $('#bulan').val();
      var tahun = $('#tahun').val();
      var baseURL = "{{ url('penjualan') }}";

      // Update link untuk tombol Tampilkan Data
      $('#btn-tampilkan').on('click', function() {
        window.location.href = baseURL + '/?bulan=' + bulan + '&tahun=' + tahun;
      });

      // Update link untuk tombol Cetak Laporan
      $('#btn-cetak').attr('href', baseURL + '/laporan/' + bulan + '/' + tahun);
    }

    // Panggil fungsi untuk memperbarui link ketika dropdown berubah
    $('#bulan, #tahun').on('change', function() {
      updateLinks();
    });

    // Memanggil updateLinks pada awal halaman dimuat
    updateLinks();

    // Membuat grafik jika data tersedia
    if (labels.length > 0 && data.length > 0) {
      var ctx = document.getElementById('grafikPenjualan').getContext('2d');
      var grafikPenjualan = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Total Penjualan',
            data: data,
            backgroundColor: 'rgba(0, 114, 79, 0.1)',
            borderColor: '#149d80',
            borderWidth: 3,
            pointBackgroundColor: '#149d80',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.3,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'white',
              titleColor: '#1e293b',
              bodyColor: '#475569',
              borderColor: '#e2e8f0',
              borderWidth: 1,
              padding: 10,
              displayColors: false,
              callbacks: {
                label: function(context) {
                  return 'Rp ' + context.raw.toLocaleString('id-ID');
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
              },
              ticks: {
                callback: function(value) {
                  return 'Rp ' + value.toLocaleString('id-ID');
                },
                color: '#94a3b8'
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#94a3b8',
                maxRotation: 0, // Mencegah label miring
                minRotation: 0,
                autoSkip: true,  // Otomatis melewati label jika terlalu banyak
                maxTicksLimit: 10 // Batasi jumlah maksimum label yang ditampilkan
              }
            }
          }
        }
      });
    } else {
      // Tampilkan pesan jika tidak ada data
      document.getElementById('grafikPenjualan').parentNode.innerHTML =
        '<div class="d-flex align-items-center justify-content-center h-100">' +
        '<p class="text-muted">Tidak ada data penjualan untuk ditampilkan</p>' +
        '</div>';
    }
  });

  // Solusi dengan JavaScript murni
document.addEventListener('DOMContentLoaded', function() {
  // Hapus event listener default dari Bootstrap
  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
    button.removeAttribute('data-bs-toggle');

    // Tambahkan event listener kustom
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const targetId = this.getAttribute('data-bs-target');
      const target = document.querySelector(targetId);

      if (target) {
        // Toggle class show
        if (target.classList.contains('show')) {
          target.classList.remove('show');
          this.setAttribute('aria-expanded', 'false');
        } else {
          target.classList.add('show');
          this.setAttribute('aria-expanded', 'true');
        }
      }
    });
  });
});


</script>
@endsection
