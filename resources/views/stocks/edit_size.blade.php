@extends('layouts.app')

@section('style')
<style>
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

  .form-card {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    overflow: hidden;
    border: none;
  }

  .form-card-body {
    padding: 2rem;
  }

  .form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
  }

  .form-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #149d80;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
  }

  .form-section-title i {
    margin-right: 0.75rem;
    font-size: 1.25rem;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #475569;
    font-size: 0.95rem;
  }

  .form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    line-height: 1.5;
    color: #1e293b;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  .form-control-static {
    background-color: #f8fafc;
    cursor: not-allowed;
  }

  .preview-image {
    display: block;
    max-width: 100px;
    border-radius: 0.5rem;
    margin-top: 0.75rem;
    border: 2px solid #e2e8f0;
    padding: 2px;
    background-color: #f8fafc;
  }

  .file-input-preview {
    margin-top: 0.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .current-image-label {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    display: block;
  }

  .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
  }

  .required-label::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
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
    cursor: pointer;
    user-select: none;
    border: 1px solid transparent;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
  }

  .btn-primary {
    color: #fff;
    background-color: #149d80;
    border-color: #149d80;
  }

  .btn-primary:hover {
    background-color: #0c8b71;
    border-color: #0c8b71;
    box-shadow: 0 4px 10px rgba(0, 114, 79, 0.2);
    transform: translateY(-2px);
  }

  .btn-secondary {
    color: #fff;
    background-color: #94a3b8;
    border-color: #94a3b8;
  }

  .btn-secondary:hover {
    background-color: #64748b;
    border-color: #64748b;
    box-shadow: 0 4px 10px rgba(100, 116, 139, 0.2);
    transform: translateY(-2px);
  }

  .btn i {
    margin-right: 0.5rem;
    font-size: 1.1rem;
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="header-title">
      <i class="bx bx-edit"></i> Edit Stok {{ $masterStock->name }} - {{ $size }}
    </h1>
    <a href="{{ route('stocks.sizes', $masterStock->id) }}" class="btn btn-secondary">
      <i class="bx bx-arrow-back"></i> Kembali
    </a>
  </div>

  <!-- Form Card -->
  <div class="form-card">
    <div class="form-card-body">
      <form action="{{ route('stocks.update.size') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="master_stock_id" value="{{ $masterStock->id }}">
        <input type="hidden" name="size" value="{{ $size }}">

        <!-- Basic Info Section - Read Only -->
        <div class="form-section">
          <h3 class="form-section-title">
            <i class="bx bx-info-circle"></i> Informasi Produk
          </h3>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Nama Produk</label>
                <input type="text" class="form-control form-control-static" value="{{ $masterStock->name }}" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">SKU</label>
                <input type="text" class="form-control form-control-static" value="{{ $masterStock->sku }}" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Tipe</label>
                <input type="text" class="form-control form-control-static" value="{{ $masterStock->type }}" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Ukuran</label>
                <input type="text" class="form-control form-control-static" value="{{ $size }}" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Harga Beli</label>
                <input type="text" class="form-control form-control-static" value="Rp {{ number_format($sizeStock->purchase_price, 0, ',', '.') }}" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label required-label">Harga Jual</label>
                <input type="number" name="selling_price" class="form-control" value="{{ $sizeStock->selling_price }}" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Gambar Produk untuk Ukuran Ini</label>
            @if(isset($sizeImage) && $sizeImage && $sizeImage->image)
              <div class="file-input-preview">
                <img src="{{ asset('storage/' . $sizeImage->image) }}"
                     alt="{{ $masterStock->name }} - {{ $size }}"
                     style="max-height: 150px; max-width: 100%;">
                <span class="current-image-label">Gambar saat ini untuk ukuran {{ $size }}</span>
              </div>
            @elseif($masterStock->image)
              <div class="file-input-preview">
                <img src="{{ asset('storage/' . $masterStock->image) }}"
                     alt="{{ $masterStock->name }}"
                     style="max-height: 150px; max-width: 100%;">
                <p class="text-muted small">Saat ini menggunakan gambar default produk</p>
              </div>
            @endif
            <input type="file" name="image" class="form-control mt-3" accept="image/*">
            <div class="form-text text-muted">Unggah gambar baru untuk ukuran ini. Biarkan kosong jika tidak ingin mengganti.</div>
          </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <a href="{{ route('stocks.sizes', $masterStock->id) }}" class="btn btn-secondary">
            <i class="bx bx-x"></i> Batal
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
