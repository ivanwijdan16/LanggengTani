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
    color: #00724F;
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
    transition: color 0.2s;
  }

  .breadcrumb-item a:hover {
    color: #00724F;
  }

  .breadcrumb-divider {
    margin: 0 0.5rem;
    color: #cbd5e1;
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
    color: #00724F;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
  }

  .form-section-title i {
    margin-right: 0.75rem;
    font-size: 1.25rem;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
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

  .form-control:focus {
    border-color: #00724F;
    outline: 0;
    box-shadow: 0 0 0 3px rgba(0, 114, 79, 0.1);
  }

  .form-control::placeholder {
    color: #94a3b8;
  }

  textarea.form-control {
    min-height: 120px;
    resize: vertical;
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

  .btn-primary {
    color: #fff;
    background-color: #00724F;
    border-color: #00724F;
  }

  .btn-primary:hover, .btn-primary:focus {
    background-color: #005a3f;
    border-color: #005a3f;
    box-shadow: 0 4px 10px rgba(0, 114, 79, 0.2);
    transform: translateY(-2px);
  }

  .btn-secondary {
    color: #fff;
    background-color: #94a3b8;
    border-color: #94a3b8;
  }

  .btn-secondary:hover, .btn-secondary:focus {
    background-color: #64748b;
    border-color: #64748b;
    box-shadow: 0 4px 10px rgba(100, 116, 139, 0.2);
    transform: translateY(-2px);
  }

  .btn i {
    margin-right: 0.5rem;
    font-size: 1.1rem;
  }

  .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
  }

  .form-hint {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 0.4rem;
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

  .file-input-wrapper {
    position: relative;
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

  .input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
  }

  .input-group-text {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    font-weight: 400;
    line-height: 1.5;
    color: #475569;
    text-align: center;
    white-space: nowrap;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem 0 0 0.5rem;
    border-right: none;
  }

  .input-group .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }

  .required-label::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
  }

  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .form-card-body {
      padding: 1.5rem;
    }

    .form-actions {
      flex-direction: column;
    }

    .form-actions .btn {
      width: 100%;
    }

    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem;
    }
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="header-title">
      <i class="bx bx-edit"></i> Edit Stok
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
    <div class="breadcrumb-item">
      <a href="{{ route('stocks.show', $stock->id) }}">{{ $stock->masterStock->name }}</a>
    </div>
    <div class="breadcrumb-divider">
      <i class="bx bx-chevron-right"></i>
    </div>
    <div class="breadcrumb-item">Edit</div>
  </div>

  <!-- Form Card -->
  <div class="form-card">
    <div class="form-card-body">
      <form method="POST" action="{{ route('stocks.update', $stock->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Info Section -->
        <div class="form-section">
          <h3 class="form-section-title">
            <i class="bx bx-info-circle"></i> Informasi Dasar
          </h3>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label required-label">Nama Stok</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $stock->masterStock->name) }}" placeholder="Masukkan nama produk" readonly>
            </div>

            <div class="form-group">
              <label class="form-label">Tipe</label>
              <div class="form-control" style="background-color: #f8fafc;">
                {{ $stock->masterStock->type }}
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Ukuran</label>
              <div class="form-control" style="background-color: #f8fafc;">
                {{ $stock->size }}
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Gambar Produk</label>
              @if ($stock->masterStock->image)
                <div class="file-input-preview">
                  <img src="{{ asset('storage/' . $stock->masterStock->image) }}" class="preview-image" alt="{{ $stock->masterStock->name }}">
                  <span class="current-image-label">Gambar saat ini</span>
                </div>
              @endif
              {{-- <input type="file" name="image" class="form-control" accept="image/*">
              <div class="form-hint">Unggah gambar baru untuk mengganti yang lama. Biarkan kosong jika tidak ingin mengganti.</div> --}}
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" placeholder="Masukkan deskripsi produk" readonly>{{ old('description', $stock->masterStock->description) }}</textarea>
          </div>
        </div>

        <!-- Inventory Section - Read Only Information -->
        <div class="form-section">
          <h3 class="form-section-title">
            <i class="bx bx-cabinet"></i> Informasi Inventaris
          </h3>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Harga Beli</label>
              <div class="form-control" style="background-color: #f8fafc;">
                Rp{{ number_format($stock->purchase_price, 0, ',', '.') }}
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Harga Jual</label>
              <div class="form-control" style="background-color: #f8fafc;">
                Rp{{ number_format($stock->selling_price, 0, ',', '.') }}
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Jumlah</label>
              <div class="form-control" style="background-color: #f8fafc;">
                {{ $stock->quantity }} pcs
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Tanggal Kadaluwarsa</label>
              <div class="form-control" style="background-color: #f8fafc;">
                {{ \Carbon\Carbon::parse($stock->expiration_date)->format('d M Y') }}
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <a href="{{ route('stocks.show', $stock->id) }}" class="btn btn-secondary">
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
