@extends('layouts.app')

@section('style')
<style>
  .stock-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    height: 100%;
    border: none;
  }

  .stock-card:hover {
    transform: translateY(-7px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
  }

  .stock-card .card-img-wrapper {
    height: 200px;
    overflow: hidden;
    position: relative;
  }

  .stock-card .card-img-top {
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }

  .stock-card:hover .card-img-top {
    transform: scale(1.08);
  }

  .stock-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    font-weight: 500;
  }

  .stat-card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  .icon-circle {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }

  .search-wrapper {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .search-control {
    border: none;
    padding: 12px 20px;
    height: auto;
  }

  .search-control:focus {
    box-shadow: none;
  }

  .search-btn {
    border-radius: 0 15px 15px 0 !important;
    padding-left: 25px;
    padding-right: 25px;
    background-color: #149d80;
    border-color: #149d80;
  }

  .search-btn:hover {
    background-color: #0c8b71;
    border-color: #0c8b71;
  }

  .search-addon {
    border: none;
    background-color: transparent;
  }

  .add-btn {
    background-color: #149d80;
    border-color: #149d80;
    border-radius: 12px;
    padding: 10px 20px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,114,79,0.2);
    transition: all 0.3s ease;
  }

  .add-btn:hover {
    background-color: #0c8b71;
    border-color: #0c8b71;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,114,79,0.3);
  }

  .price-tag {
    color: #149d80;
    font-weight: 700;
  }

  .quantity-badge {
    background-color: #f1f5f9;
    color: #334155;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
  }

  .card-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .empty-state {
    padding: 50px 20px;
    border-radius: 15px;
  }

  /* Custom Pagination */
  .pagination {
    gap: 5px;
  }

  .page-item .page-link {
    border-radius: 8px;
    border: none;
    color: #475569;
    padding: 10px 15px;
  }

  .page-item.active .page-link {
    background-color: #149d80;
    color: white;
  }

  .date-badge {
    transition: all 0.3s ease;
  }

  .date-badge i {
    transition: all 0.3s ease;
  }

  .stock-card:hover .date-badge i {
    transform: translateX(-3px);
  }

  .header-title {
    color: #1e293b;
    font-weight: 700;
    font-size: 1.75rem;
  }

  .header-title i {
    color: #149d80;
    margin-right: 10px;
    font-size: 1.5rem;
  }

  .badge-small {
    font-size: 0.65rem;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    font-weight: 500;
  }

  .badge-small i {
    font-size: 0.7rem;
    margin-right: 3px;
  }

  /* Breadcrumb styles */
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

  /* Total Stock Badge Style */
  .stock-quantity-badge {
    background-color: #e2e8f0;
    color: #334155;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    margin-top: 8px;
  }

  .stock-quantity-badge i {
    margin-right: 5px;
    font-size: 0.8rem;
    color: #149d80;
  }

  /* Delete Modal Styles */
  .delete-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(15, 23, 42, 0.7);
    z-index: 1050;
    display: none; /* Initially hidden */
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
  }

  .delete-modal-backdrop.show {
    display: flex;
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

  .delete-modal-content {
    width: 100%;
  }

  .delete-modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
  }

  .delete-modal-title {
    margin: 0;
    color: #ef4444;
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
    padding: 1.75rem;
    color: #475569;
    text-align: center;
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
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(239, 68, 68, 0.2);
  }

  @media (max-width: 768px) {
    .delete-modal-footer {
      flex-direction: column;
    }

    .delete-modal-btn {
      width: 100%;
    }
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-sm-flex justify-content-between align-items-center">
        <h1 class="header-title mb-3">
          <i class="bx bx-box"></i>{{ $masterStock->name }} - {{ $size }}
        </h1>
        <a href="{{ route('stocks.sizes', $masterStock->id) }}" class="btn btn-secondary mr-2">
          <i class="bx bx-arrow-back"></i> Kembali
        </a>
      </div>
    </div>
  </div>

  <!-- Breadcrumbs -->
  <div class="breadcrumbs">
    <div class="breadcrumb-item">
      <a href="{{ route('stocks.index') }}">Stok</a>
    </div>
    <div class="breadcrumb-divider">
      <i class="bx bx-chevron-right"></i>
    </div>
    <div class="breadcrumb-item">
      <a href="{{ route('stocks.sizes', $masterStock->id) }}">{{ $masterStock->name }}</a>
    </div>
    <div class="breadcrumb-divider">
      <i class="bx bx-chevron-right"></i>
    </div>
    <div class="breadcrumb-item">{{ $size }}</div>
  </div>

  <!-- Batch-based Stock Grid -->
  <div class="row">
    @forelse ($stocks as $stock)
      @php
        $expired = \Carbon\Carbon::parse($stock->expiration_date)->isPast();
        $almostExpired = !$expired && \Carbon\Carbon::parse($stock->expiration_date)->diffInDays(now()) < 30;
      @endphp
      <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
        <div class="card stock-card" onclick="viewStockDetail({{ $stock->id }})" style="cursor: pointer;">
          <div class="card-body p-3">
            <h5 class="card-title fw-bold">{{ $masterStock->name }} - {{ $size }}</h5>

            @if ($expired)
              <span class="badge bg-danger badge-small">
                <i class="bx bx-error-circle"></i> Kadaluwarsa
              </span>
            @elseif($almostExpired)
              <span class="badge bg-warning text-dark badge-small">
                <i class="bx bx-time"></i> Hampir Kadaluwarsa
              </span>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="card-subtitle mb-0">{{ $stock->stock_id }}</h6>
              <span class="stock-quantity-badge">
                <i class="bx bx-package"></i> {{ $stock->quantity }} pcs
              </span>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="price-tag fs-5">Rp {{ number_format($stock->selling_price, 0, ',', '.') }}</span>
            </div>

            <div class="mt-3 pt-2 border-top d-flex justify-content-between">
              <a href="{{ route('stocks.show', $stock->id) }}" class="btn btn-sm btn-info" onclick="event.stopPropagation();">
                <i class="bx bx-show"></i> Detail
              </a>
              <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); openDeleteStockModal({{ $stock->id }}, '{{ $masterStock->name }} - {{ $size }}', '{{ $stock->stock_id }}')">
                <i class="bx bx-trash"></i> Hapus
              </button>
            </div>

            <div class="mt-2">
              <div class="d-flex align-items-center small date-badge">
                <i class="bx bx-calendar me-2 text-muted"></i>
                <span class="{{ $expired ? 'text-danger' : ($almostExpired ? 'text-warning' : 'text-muted') }}">
                  {{ \Carbon\Carbon::parse($stock->expiration_date)->format('d M Y') }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card empty-state border-0 shadow-sm">
          <div class="card-body text-center py-5">
            <i class="bx bx-package" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3 class="mb-3">Tidak ada batch untuk ukuran ini</h3>
            <a href="{{ route('stocks.create.size', ['master_id' => $masterStock->id, 'size' => $size]) }}" class="btn add-btn btn-success">
              <i class="bx bx-plus me-2"></i> Tambah Stok Baru
            </a>
          </div>
        </div>
      </div>
    @endforelse
  </div>
</div>

<!-- Delete Stock Modal -->
<div class="delete-modal-backdrop" id="deleteStockModal">
  <div class="delete-modal-dialog">
    <div class="delete-modal-content">
      <div class="delete-modal-header">
        <h5 class="delete-modal-title">
          <i class="bx bx-error-circle"></i> Konfirmasi Hapus
        </h5>
      </div>
      <div class="delete-modal-body">
        <p>Apakah Anda yakin ingin menghapus stok ini?</p>
        <div class="delete-modal-product" id="deleteStockProduct">
          <!-- Stock info will be inserted here -->
        </div>
        <p>Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="delete-modal-footer">
        <button type="button" class="delete-modal-btn delete-modal-btn-cancel" onclick="closeDeleteStockModal()">
          <i class="bx bx-x me-1"></i> Batal
        </button>
        <button type="button" class="delete-modal-btn delete-modal-btn-delete" onclick="deleteStock()">
          <i class="bx bx-trash me-1"></i> Hapus
        </button>
      </div>
    </div>
  </div>
</div>

<form id="delete-stock-form" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>
@endsection

@section('script')
<script>
  function viewStockDetail(stockId) {
    window.location.href = "{{ url('stocks') }}/" + stockId;
  }

  function openDeleteStockModal(stockId, stockName, stockIdTag) {
    event.stopPropagation();

    // Set form action URL
    document.getElementById('delete-stock-form').action = "{{ url('stocks') }}/" + stockId;

    // Set stock info in modal
    document.getElementById('deleteStockProduct').innerHTML = `
      <div class="delete-modal-product-name">${stockName}</div>
      <div class="delete-modal-product-type">${stockIdTag}</div>
    `;

    // Show the modal
    const modal = document.getElementById('deleteStockModal');

    // Set display to flex first
    modal.style.display = 'flex';

    // Trigger a reflow
    void modal.offsetWidth;

    // Then add the show class for the transitions
    modal.classList.add('show');
    modal.style.opacity = '1';
    modal.style.visibility = 'visible';

    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
  }

  function closeDeleteStockModal() {
    const modal = document.getElementById('deleteStockModal');

    // Start the transition
    modal.style.opacity = '0';

    // Wait for transition to finish before hiding
    setTimeout(function() {
      modal.classList.remove('show');
      modal.style.visibility = 'hidden';
      modal.style.display = 'none';

      // Re-enable background scrolling
      document.body.style.overflow = '';
    }, 300); // Match this to your CSS transition duration
  }

  function deleteStock() {
    document.getElementById('delete-stock-form').submit();
  }

  // Close modal when clicking outside
  document.getElementById('deleteStockModal').addEventListener('click', function(event) {
    if (event.target === this) {
      closeDeleteStockModal();
    }
  });

  // Close modal with ESC key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('deleteStockModal').classList.contains('show')) {
      closeDeleteStockModal();
    }
  });

  // Show success modal if needed
  @if (session('showSuccessModal'))
    // Show success modal if it exists
    const successModal = document.getElementById('successModal');
    if (successModal) {
      successModal.style.display = 'flex';
      setTimeout(function() {
        successModal.classList.add('show');
        successModal.style.opacity = '1';
        successModal.style.visibility = 'visible';
        document.body.style.overflow = 'hidden';
      }, 10);
    }
  @endif
</script>
@endsection
