@extends('layouts.app')

@section('style')
  <style>
    .detail-card {
      border-radius: 15px;
      border: none;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      background-color: white;
    }

    .detail-container {
      display: flex;
      flex-direction: row;
    }

    .product-img-container {
      width: 40%;
      background-color: #ffffff;
      position: relative;
      overflow: hidden;
    }

    .product-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      transition: transform 0.3s ease;
    }

    .status-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      border-radius: 30px;
      padding: 0.4rem 1rem;
      font-size: 0.8rem;
      font-weight: 500;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .badge-active {
      background-color: #149d80;
      color: white;
    }

    .badge-expired {
      background-color: #ef4444;
      color: white;
    }

    .detail-content {
      width: 60%;
      padding: 2.5rem;
    }

    .product-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 0.75rem;
      line-height: 1.2;
    }

    .product-type {
      display: inline-block;
      background-color: #e7f0fa;
      color: #149d80;
      border-radius: 20px;
      padding: 0.3rem 0.8rem;
      font-size: 0.8rem;
      font-weight: 500;
      margin-bottom: 1.25rem;
    }

    .product-description {
      color: #64748b;
      font-size: 1rem;
      margin-bottom: 1.75rem;
      line-height: 1.6;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.25rem;
      margin-bottom: 2rem;
    }

    .info-item {
      padding: 1rem;
      background-color: #f8fafc;
      border-radius: 12px;
      transition: all 0.3s ease;
      border: 1px solid #f1f5f9;
    }

    .info-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      border-color: #e2e8f0;
    }

    .info-label {
      display: block;
      font-size: 0.875rem;
      color: #64748b;
      margin-bottom: 0.4rem;
    }

    .info-value {
      font-size: 1.125rem;
      font-weight: 600;
      color: #1e293b;
      display: flex;
      align-items: center;
    }

    .info-value i {
      margin-right: 0.5rem;
      font-size: 1.25rem;
      color: #64748b;
    }

    .price-value {
      color: #149d80;
    }

    .expired-value {
      color: #ef4444;
    }

    .warning-value {
      color: #f59e0b;
    }

    .actions {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }

    .btn-action {
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.65rem 1.5rem;
      font-size: 0.95rem;
      font-weight: 500;
      line-height: 1.5;
      text-align: center;
      white-space: nowrap;
      vertical-align: middle;
      cursor: pointer;
      user-select: none;
      border: 1px solid transparent;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }

    .btn-secondary {
      color: #fff;
      background-color: #94a3b8;
      border-color: #94a3b8;
    }

    .btn-secondary:hover,
    .btn-secondary:focus {
      background-color: #64748b;
      border-color: #64748b;
      box-shadow: 0 4px 10px rgba(100, 116, 139, 0.2);
      transform: translateY(-2px);
    }

    .btn i {
      margin-right: 0.5rem;
      font-size: 1.1rem;
    }

    .btn-edit {
      background-color: #149d80;
      color: white;
      border: none;
      flex: 1;
    }

    .btn-edit:hover {
      background-color: #005a3f;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 114, 79, 0.2);
    }

    .btn-delete {
      background-color: #ef4444;
      color: white;
      border: none;
    }

    .btn-delete:hover {
      background-color: #dc2626;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(239, 68, 68, 0.2);
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .header-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1e293b;
      display: flex;
      align-items: center;
    }

    .header-title i {
      color: #149d80;
      margin-right: 0.75rem;
      font-size: 1.5rem;
    }

    .breadcrumbs {
      display: flex;
      align-items: center;
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
    }

    .breadcrumb-item {
      color: #64748b;
    }

    .breadcrumb-item a {
      color: #64748b;
      text-decoration: none;
    }

    .breadcrumb-divider {
      margin: 0 0.5rem;
      color: #cbd5e1;
    }

    .updated-info {
      margin-top: 1.5rem;
      font-size: 0.85rem;
      color: #94a3b8;
      text-align: right;
    }

    /* Modal styles */
    .delete-modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(15, 23, 42, 0.7);
      z-index: 1050;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .delete-modal-backdrop.show {
      opacity: 1;
      visibility: visible;
    }

    .delete-modal-dialog {
      background-color: white;
      border-radius: 15px;
      max-width: 450px;
      width: 90%;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      transform: translateY(-30px) scale(0.95);
      transition: transform 0.3s ease;
      margin: 1.5rem;
      overflow: hidden;
    }

    .delete-modal-backdrop.show .delete-modal-dialog {
      transform: translateY(0) scale(1);
    }

    .delete-modal-header {
      padding: 1.5rem;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
    }

    .delete-modal-title {
      margin: 0;
      color: #1e293b;
      font-size: 1.35rem;
      font-weight: 600;
      display: flex;
      align-items: center;
    }

    .delete-modal-title i {
      color: #ef4444;
      margin-right: 0.75rem;
      font-size: 1.5rem;
    }

    .delete-modal-body {
      padding: 1.5rem;
      color: #475569;
    }

    .delete-modal-product {
      margin: 0.5rem 0;
      padding: 1rem;
      background-color: #f8fafc;
      border-radius: 12px;
      text-align: center;
    }

    .delete-modal-product-name {
      font-weight: 600;
      color: #1e293b;
      font-size: 1.15rem;
    }

    .delete-modal-product-type {
      display: inline-block;
      color: #64748b;
      font-size: 0.9rem;
      margin-top: 0.3rem;
    }

    .delete-modal-footer {
      padding: 1.25rem 1.5rem;
      border-top: 1px solid #f1f5f9;
      display: flex;
      justify-content: space-between;
      gap: 1rem;
    }

    .delete-modal-btn {
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 1;
    }

    .delete-modal-btn-cancel {
      background-color: #f1f5f9;
      color: #475569;
      border: none;
    }

    .delete-modal-btn-cancel:hover {
      background-color: #e2e8f0;
      color: #334155;
    }

    .delete-modal-btn-delete {
      background-color: #ef4444;
      color: white;
      border: none;
    }

    .delete-modal-btn-delete:hover {
      background-color: #dc2626;
      box-shadow: 0 5px 15px rgba(239, 68, 68, 0.2);
    }

    @media (max-width: 1024px) {
      .detail-container {
        flex-direction: column;
      }

      .product-img-container,
      .detail-content {
        width: 100%;
      }

      .product-img-container {
        height: 350px;
      }

      .detail-content {
        padding: 2rem 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .info-grid {
        grid-template-columns: 1fr;
      }

      .actions {
        flex-direction: column;
      }

      .btn-action {
        width: 100%;
      }

      .delete-modal-footer {
        flex-direction: column;
      }

      .delete-modal-btn {
        width: 100%;
      }

      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
      }
    }

    /* Shared Modal Styles */
    .success-modal-backdrop,
    .error-modal-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(15, 23, 42, 0.7);
      z-index: 1050;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .success-modal-backdrop.show,
    .error-modal-backdrop.show {
      opacity: 1;
      visibility: visible;
    }

    .success-modal-dialog,
    .error-modal-dialog {
      background-color: white;
      border-radius: 15px;
      max-width: 450px;
      width: 90%;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      transform: translateY(-30px) scale(0.95);
      transition: transform 0.3s ease;
      margin: 1.5rem;
      overflow: hidden;
    }

    .success-modal-backdrop.show .success-modal-dialog,
    .error-modal-backdrop.show .error-modal-dialog {
      transform: translateY(0) scale(1);
    }

    .success-modal-header,
    .error-modal-header {
      padding: 1.5rem;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
    }

    .success-modal-title,
    .error-modal-title {
      margin: 0;
      font-size: 1.35rem;
      font-weight: 600;
      display: flex;
      align-items: center;
    }

    .success-modal-title {
      color: #149d80;
    }

    .error-modal-title {
      color: #ef4444;
    }

    .success-modal-title i,
    .error-modal-title i {
      margin-right: 0.75rem;
      font-size: 1.5rem;
    }

    .success-modal-body,
    .error-modal-body {
      padding: 1.75rem;
      color: #475569;
      text-align: center;
    }

    .success-modal-footer,
    .error-modal-footer {
      padding: 1.25rem 1.5rem;
      border-top: 1px solid #f1f5f9;
      display: flex;
      justify-content: center;
      gap: 1rem;
    }

    /* Success Modal Specific Styles */
    .success-icon-wrapper {
      width: 80px;
      height: 80px;
      background-color: rgba(0, 114, 79, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
    }

    .success-icon-wrapper i {
      color: #149d80;
      font-size: 2.5rem;
    }

    .success-message {
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 1rem;
    }

    .success-detail {
      color: #64748b;
      margin-bottom: 0;
    }

    .success-modal-btn {
      background-color: #149d80;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .success-modal-btn:hover {
      background-color: #005a3f;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 114, 79, 0.2);
    }

    /* Error Modal Specific Styles */
    .error-icon-wrapper {
      width: 80px;
      height: 80px;
      background-color: rgba(239, 68, 68, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
    }

    .error-icon-wrapper i {
      color: #ef4444;
      font-size: 2.5rem;
    }

    .error-message {
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 1rem;
    }

    .error-detail {
      color: #64748b;
      margin-bottom: 1.5rem;
    }

    .error-info-box {
      background-color: #f1f5f9;
      border-radius: 10px;
      padding: 1.25rem;
      text-align: left;
      margin-bottom: 1rem;
      border-left: 4px solid #64748b;
    }

    .error-info-box ul {
      padding-left: 1.25rem;
      margin-bottom: 0;
    }

    .error-info-box li {
      margin-bottom: 0.5rem;
    }

    .error-info-box li:last-child {
      margin-bottom: 0;
    }

    .error-modal-footer {
      justify-content: space-between;
    }

    .error-modal-btn-primary {
      background-color: #149d80;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .error-modal-btn-primary:hover {
      background-color: #005a3f;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 114, 79, 0.2);
      color: white;
      text-decoration: none;
    }

    .error-modal-btn-secondary {
      background-color: #f1f5f9;
      color: #475569;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .error-modal-btn-secondary:hover {
      background-color: #e2e8f0;
      color: #334155;
    }

    @media (max-width: 768px) {
      .error-modal-footer {
        flex-direction: column;
      }

      .error-modal-btn-primary,
      .error-modal-btn-secondary {
        width: 100%;
      }
    }
  </style>
@endsection

@section('content')
  <div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header">
      <h1 class="header-title">
        <i class="bx bx-detail"></i> Detail Stok
      </h1>
      <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
        <i class="bx bx-arrow-back"></i> Kembali
      </a>
    </div>

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
      <div class="breadcrumb-item">
        <a href="{{ route('stocks.index') }}">Stok</a>
      </div>
      <div class="breadcrumb-divider">
        <i class="bx bx-chevron-right"></i>
      </div>
      <div class="breadcrumb-item">{{ $stock->masterStock->name }}</div>
    </div>

    <!-- Product Detail Card -->
    <div class="card detail-card">
      <div class="detail-container">
        <!-- Product Image (Left Side) -->
        <div class="product-img-container">
          <img
            src="{{ $stock->masterStock->image ? asset('storage/' . $stock->masterStock->image) : asset('images/default.png') }}"
            class="product-img" alt="{{ $stock->masterStock->name }}">

          @php
            $expired = \Carbon\Carbon::parse($stock->expiration_date)->isPast();
            $almostExpired = !$expired && \Carbon\Carbon::parse($stock->expiration_date)->diffInDays(now()) < 30;
          @endphp


        </div>

        <!-- Product Details (Right Side) -->
        <div class="detail-content">
          <h1 class="product-title">{{ $stock->masterStock->name }}</h1>
          <div class="product-type">{{ $stock->masterStock->type }}</div>
          @if ($stock->masterStock->sub_type)
            <div class="product-type">{{ $stock->masterStock->sub_type }}</div>
          @endif

          @if ($stock->masterStock->description)
            <p class="product-description">{{ $stock->masterStock->description }}</p>
          @else
            <p class="product-description text-muted">Tidak ada deskripsi produk</p>
          @endif

          <div class="info-grid">
            <!-- Purchase Price -->
            <div class="info-item">
              <span class="info-label">Harga Beli</span>
              <span class="info-value price-value">
                <i class="bx bx-purchase-tag"></i>
                Rp{{ number_format($stock->purchase_price, 0, ',', '.') }}
              </span>
            </div>

            <!-- Selling Price -->
            <div class="info-item">
              <span class="info-label">Harga Jual</span>
              <span class="info-value price-value">
                <i class="bx bx-dollar-circle"></i>
                Rp{{ number_format($stock->selling_price, 0, ',', '.') }}
              </span>
            </div>

            <!-- Quantity -->
            <div class="info-item">
              <span class="info-label">Jumlah Stok</span>
              <span class="info-value">
                <i class="bx bx-cabinet"></i>
                {{ $stock->quantity }} pcs
              </span>
            </div>

            <!-- Size -->
            <div class="info-item">
              <span class="info-label">Ukuran</span>
              <span class="info-value">
                <i class="bx bx-ruler"></i>
                {{ $stock->size ?: 'Tidak Ada' }}
              </span>
            </div>

            <!-- Expiration Date -->
            <div class="info-item">
              <span class="info-label">Tanggal Kadaluwarsa</span>
              <span class="info-value {{ $expired ? 'expired-value' : ($almostExpired ? 'warning-value' : '') }}">
                <i class="bx bx-calendar"></i>
                {{ \Carbon\Carbon::parse($stock->expiration_date)->format('d M Y') }}
                @if ($expired)
                  <span class="badge bg-danger ms-2"
                    style="font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 20px;">
                    Kadaluwarsa
                  </span>
                @elseif($almostExpired)
                  <span class="badge bg-warning text-dark ms-2"
                    style="font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 20px;">
                    Hampir Kadaluwarsa
                  </span>
                @endif
              </span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="actions">
            <a href="{{ route('stocks.edit', $stock->id) }}" class="btn btn-action btn-edit">
              <i class="bx bx-edit me-2"></i> Edit Produk
            </a>
            <button type="button" class="btn btn-action btn-delete" onclick="openDeleteModal()">
              <i class="bx bx-trash me-2"></i> Hapus
            </button>
          </div>

          <form id="delete-form" action="{{ route('stocks.destroy', $stock->id) }}" method="POST">
            @csrf
            @method('DELETE')
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="delete-modal-backdrop" id="deleteModal">
    <div class="delete-modal-dialog">
      <div class="delete-modal-content">
        <div class="delete-modal-header">
          <h5 class="delete-modal-title">
            <i class="bx bx-error-circle"></i> Konfirmasi Hapus
          </h5>
        </div>
        <div class="delete-modal-body">
          <p>Apakah Anda yakin ingin menghapus stok ini?</p>

          <div class="delete-modal-product">
            <div class="delete-modal-product-name">{{ $stock->name }}</div>
            <span class="delete-modal-product-type">{{ $stock->type }}</span>
          </div>

          <p>Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data produk ini.</p>
        </div>
        <div class="delete-modal-footer">
          <button type="button" class="delete-modal-btn delete-modal-btn-cancel" onclick="closeDeleteModal()">
            <i class="bx bx-x me-1"></i> Batal
          </button>
          <button type="button" class="delete-modal-btn delete-modal-btn-delete" onclick="submitDelete()">
            <i class="bx bx-trash me-1"></i> Hapus Produk
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Error Modal - Tampil ketika penghapusan gagal -->
  <div class="error-modal-backdrop" id="errorModal">
    <div class="error-modal-dialog">
      <div class="error-modal-content">
        <div class="error-modal-header">
          <h5 class="error-modal-title">
            <i class="bx bx-error-circle"></i> Gagal Menghapus
          </h5>
        </div>
        <div class="error-modal-body">
          <div class="text-center mb-4">
            <div class="error-icon-wrapper">
              <i class="bx bx-x"></i>
            </div>
          </div>
          <h4 class="error-message">Stok Tidak Dapat Dihapus</h4>
          <p class="error-detail">Stok ini tidak dapat dihapus karena telah memiliki riwayat transaksi dan masih memiliki
            stok tersisa.</p>

          <div class="error-info-box">
            <p class="mb-2">Stok hanya dapat dihapus jika memenuhi salah satu kondisi berikut:</p>
            <ul>
              <li>Stok telah habis (jumlah 0)</li>
              <li>Stok telah kadaluwarsa</li>
              <li>Stok belum pernah memiliki riwayat penjualan</li>
            </ul>
          </div>
        </div>
        <div class="error-modal-footer">
          <button type="button" class="error-modal-btn-secondary" onclick="closeErrorModal()">
            Kembali
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    function openDeleteModal() {
      // Show the modal
      const modal = document.getElementById('deleteModal');
      modal.classList.add('show');

      // Prevent background scrolling
      document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
      // Hide the modal
      const modal = document.getElementById('deleteModal');
      modal.classList.remove('show');

      // Re-enable background scrolling
      document.body.style.overflow = '';
    }

    function submitDelete() {
      // Submit the delete form
      document.getElementById('delete-form').submit();
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(event) {
      if (event.target === this) {
        closeDeleteModal();
      }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && document.getElementById('deleteModal').classList.contains('show')) {
        closeDeleteModal();
      }
    });

    // Fungsi untuk menampilkan error modal
    function showErrorModal() {
      const modal = document.getElementById('errorModal');
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    // Fungsi untuk menutup error modal
    function closeErrorModal() {
      const modal = document.getElementById('errorModal');
      modal.classList.remove('show');
      document.body.style.overflow = '';
    }

    // Menutup modal saat klik di luar modal
    document.getElementById('errorModal').addEventListener('click', function(event) {
      if (event.target === this) {
        closeErrorModal();
      }
    });

    // Menutup modal dengan tombol ESC
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && document.getElementById('errorModal').classList.contains('show')) {
        closeErrorModal();
      }
    });

    // Cek jika perlu menampilkan error modal (setelah gagal delete)
    @if (session('showErrorModal'))
      document.addEventListener('DOMContentLoaded', function() {
        showErrorModal();
      });
    @endif
  </script>
@endsection
