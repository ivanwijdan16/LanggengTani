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

  /* Sorting styles */
  .sort-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 15px;
    align-items: center;
  }

  .sort-label {
    font-size: 0.9rem;
    color: #64748b;
    margin: 0;
    padding: 6px 0;
    display: flex;
    align-items: center;
    margin-right: 5px;
  }

  .sort-link {
    font-size: 0.9rem;
    color: #64748b;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 8px;
    background-color: #f1f5f9;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
  }

  .sort-link:hover {
    background-color: #e2e8f0;
    color: #334155;
  }

  .sort-link.active {
    background-color: #149d80;
    color: white;
  }

  .sort-link i {
    margin-left: 5px;
    font-size: 0.75rem;
  }

  .sort-dropdown {
    border-radius: 8px;
    background-color: #f1f5f9;
    border: none;
    color: #64748b;
    padding: 6px 15px;
    font-size: 0.9rem;
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
    background-color: #0c8b71;
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
    background-color: #0c8b71;
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
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-sm-flex justify-content-between align-items-center">
        <h1 class="header-title mb-3">
          <i class="bx bx-box"></i>{{ $masterStock->name }} ({{ $masterStock->sku }})
        </h1>
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary mr-2">
          <i class="bx bx-arrow-back"></i> Kembali
        </a>
      </div>
      <p class="text-muted">{{ $masterStock->type }} @if($masterStock->sub_type) - {{ $masterStock->sub_type }} @endif</p>
    </div>
  </div>

  <!-- Size-based Stock Grid -->
  <div class="row">
    @forelse ($sizeGroups as $size => $stocks)
      @php
        $totalQuantity = $stocks->sum('quantity');
        $stock = $stocks->first(); // Representative stock for this size
      @endphp
      <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
        <div class="card stock-card" onclick="viewStockBatches({{ $masterStock->id }}, '{{ $size }}')" style="cursor: pointer;">
          <div class="card-body p-3">
            <h5 class="card-title fw-bold">{{ $masterStock->name }} - {{ $size }}</h5>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="card-subtitle mb-0">{{ $stock->stock_id }}</h6>
              <span class="stock-quantity-badge">
                <i class="bx bx-package"></i> {{ $totalQuantity }} pcs
              </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="price-tag fs-5">Rp {{ number_format($stock->selling_price, 0, ',', '.') }}</span>
            </div>

            <div class="mt-3 pt-2 border-top d-flex justify-content-between">
              <a href="{{ route('stocks.create.size', ['master_id' => $masterStock->id, 'size' => $size]) }}"
                class="btn btn-sm btn-success" onclick="event.stopPropagation();">
                <i class="bx bx-plus"></i> Tambah Stok
              </a>
              <a href="{{ route('stocks.edit.size', ['master_id' => $masterStock->id, 'size' => $size]) }}"
                class="btn btn-sm btn-primary" onclick="event.stopPropagation();">
                <i class="bx bx-edit"></i> Edit
              </a>
              <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); openDeleteSizeModal('{{ $size }}')">
                <i class="bx bx-trash"></i> Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card empty-state border-0 shadow-sm">
          <div class="card-body text-center py-5">
            <i class="bx bx-package" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3 class="mb-3">Tidak ada stok untuk produk ini</h3>
            <a href="{{ route('stocks.create.size', ['master_id' => $masterStock->id]) }}" class="btn add-btn btn-success">
              <i class="bx bx-plus me-2"></i> Tambah Stok Baru
            </a>
          </div>
        </div>
      </div>
    @endforelse
  </div>
</div>

<!-- Delete Size Modal -->
<div class="delete-modal-backdrop" id="deleteSizeModal">
  <div class="delete-modal-dialog">
    <div class="delete-modal-content">
      <div class="delete-modal-header">
        <h5 class="delete-modal-title">
          <i class="bx bx-error-circle"></i> Konfirmasi Hapus
        </h5>
      </div>
      <div class="delete-modal-body">
        <p>Apakah Anda yakin ingin menghapus seluruh stok untuk ukuran ini?</p>
        <div class="delete-modal-product" id="deleteSizeProduct">
          <!-- Size info will be inserted here -->
        </div>
        <p>Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data stok untuk ukuran ini.</p>
      </div>
      <div class="delete-modal-footer">
        <button type="button" class="delete-modal-btn delete-modal-btn-cancel" onclick="closeDeleteSizeModal()">
          <i class="bx bx-x me-1"></i> Batal
        </button>
        <button type="button" class="delete-modal-btn delete-modal-btn-delete" onclick="deleteSize()">
          <i class="bx bx-trash me-1"></i> Hapus
        </button>
      </div>
    </div>
  </div>
</div>

<form id="delete-size-form" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>
@endsection

@section('script')
<script>
  function viewStockBatches(masterStockId, size) {
    window.location.href = "{{ url('stocks/batches') }}/" + masterStockId + "/" + encodeURIComponent(size);
  }

  function openDeleteSizeModal(size) {
    event.stopPropagation();
    // Set form action URL
    document.getElementById('delete-size-form').action = "{{ url('stocks/size') }}/" + {{ $masterStock->id }} + "/" + encodeURIComponent(size);

    // Set size info in modal
    document.getElementById('deleteSizeProduct').innerHTML = `
      <div class="delete-modal-product-name">{{ $masterStock->name }} - ${size}</div>
    `;

    // Show the modal
    const modal = document.getElementById('deleteSizeModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeDeleteSizeModal() {
    const modal = document.getElementById('deleteSizeModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }

  function deleteSize() {
    document.getElementById('delete-size-form').submit();
  }

  // Close modal when clicking outside
  document.getElementById('deleteSizeModal').addEventListener('click', function(event) {
    if (event.target === this) {
      closeDeleteSizeModal();
    }
  });

  // Close modal with ESC key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('deleteSizeModal').classList.contains('show')) {
      closeDeleteSizeModal();
    }
  });
</script>
@endsection
