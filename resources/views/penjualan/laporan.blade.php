<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Penjualan - Toko Pertanian Joyo Langgeng Sejahtera</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #149d80;
      --primary-light: #E8F5F0;
      --primary-dark: #005a3f;
      --text-dark: #1e293b;
      --text-medium: #475569;
      --text-light: #64748b;
      --border-color: #e2e8f0;
      --background-light: #f8fafc;
      --white: #ffffff;
    }

    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      color: var(--text-dark);
      background-color: var(--background-light);
    }

    .container {
      width: 90%;
      max-width: 1200px;
      margin: 20px auto;
      padding: 30px;
      background-color: var(--white);
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-color);
    }

    .company-name {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 5px;
    }

    .report-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-medium);
      margin-top: 5px;
      margin-bottom: 10px;
    }

    .report-subtitle {
      font-size: 16px;
      font-weight: 500;
      color: var(--text-light);
      margin-top: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
      font-size: 14px;
    }

    table th,
    table td {
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    table th {
      background-color: var(--primary);
      color: white;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 12px;
      letter-spacing: 0.5px;
    }

    table tr:last-child td {
      border-bottom: none;
    }

    table tr:nth-child(even) {
      background-color: var(--primary-light);
    }

    table tr:hover {
      background-color: rgba(0, 114, 79, 0.05);
    }

    .table-container {
      overflow-x: auto;
      border-radius: 8px;
      border: 1px solid var(--border-color);
    }

    .currency {
      text-align: right;
      font-family: 'Inter', monospace;
      font-variant-numeric: tabular-nums;
      white-space: nowrap;
    }

    .currency:before {
      content: 'Rp ';
    }

    .text-center {
      text-align: center;
    }

    .total-row td {
      font-weight: 600;
    }

    .summary-table {
      margin-top: 20px;
      width: 350px;
      margin-left: auto;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      overflow: hidden;
    }

    .summary-table table {
      margin-bottom: 0;
    }

    .summary-table td {
      padding: 15px;
    }

    .summary-table tr:last-child {
      background-color: var(--primary-light);
      font-weight: 700;
    }

    .summary-table tr:last-child td {
      color: var(--primary);
      font-size: 15px;
    }

    @media print {
      body {
        background-color: white;
      }

      .container {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 20px;
        box-shadow: none;
        border-radius: 0;
      }

      table tr {
        page-break-inside: avoid;
      }

      table th {
        background-color: var(--primary) !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      table tr:nth-child(even) {
        background-color: rgba(0, 114, 79, 0.05) !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1 class="company-name">Toko Pertanian Joyo Langgeng Sejahtera</h1>
      <h2 class="report-title">Laporan Penjualan</h2>
      <p class="report-subtitle">Bulan {{ Carbon\Carbon::parse($penjualans->first()->sale_date ?? now())->translatedFormat('F Y') }}</p>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID Penjualan</th>
            <th>Nama Barang</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($penjualans as $penjualan)
            <tr>
              <td>{{ $penjualan->id_penjualan }}</td>
              <td>{{ $penjualan->product_name }}</td>
              <td>{{ \Carbon\Carbon::parse($penjualan->created_at)->format('d-m-Y') }}, {{ \Carbon\Carbon::parse($penjualan->created_at)->format('H:i') }}</td>
              <td>{{ $penjualan->quantity }}</td>
              <td class="currency">{{ number_format($penjualan->unit_price, 0, ',', '.') }}</td>
              <td class="currency">{{ number_format($penjualan->total_price, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Total Penjualan -->
    <div class="summary-table">
      <table>
        <tr>
          <td><strong>Total Penjualan</strong></td>
          <td class="currency">{{ number_format($penjualans->sum('total_price'), 0, ',', '.') }}</td>
        </tr>
      </table>
    </div>
  </div>
</body>

</html>
