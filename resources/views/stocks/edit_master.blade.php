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

  textarea.form-control {
    min-height: 120px;
    resize: vertical;
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
      <i class="bx bx-edit"></i> Edit Produk {{ $masterStock->name }}
    </h1>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
      <i class="bx bx-arrow-back"></i> Kembali
    </a>
  </div>

  <!-- Form Card -->
  <div class="form-card">
    <div class="form-card-body">
      <form action="{{ route('stocks.update.master', $masterStock->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Info Section -->
        <div class="form-section">
          <h3 class="form-section-title">
            <i class="bx bx-info-circle"></i> Informasi Produk
          </h3>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label required-label">Nama Produk</label>
                <input type="text" name="name" class="form-control" value="{{ $masterStock->name }}" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">SKU</label>
                <input type="text" class="form-control" value="{{ $masterStock->sku }}" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label required-label">Tipe</label>
                <select name="type" class="form-control" required>
                  <option value="Obat" {{ $masterStock->type == 'Obat' ? 'selected' : '' }}>Obat</option>
                  <option value="Pupuk" {{ $masterStock->type == 'Pupuk' ? 'selected' : '' }}>Pupuk</option>
                  <option value="Bibit" {{ $masterStock->type == 'Bibit' ? 'selected' : '' }}>Bibit</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Sub Tipe</label>
                <select name="sub_type" class="form-control">
                  <option value="" {{ $masterStock->sub_type == null ? 'selected' : '' }}>Tidak Ada</option>
                  <option value="Subsidi" {{ $masterStock->sub_type == 'Subsidi' ? 'selected' : '' }}>Subsidi</option>
                  <option value="Non-Subsidi" {{ $masterStock->sub_type == 'Non-Subsidi' ? 'selected' : '' }}>Non-Subsidi</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Gambar Produk</label>
            @if ($masterStock->image)
              <div class="file-input-preview">
                <img src="{{ asset('storage/' . $masterStock->image) }}" class="preview-image" alt="{{ $masterStock->name }}">
                <span class="current-image-label">Gambar saat ini</span>
              </div>
            @endif
            <input type="file" name="image" class="form-control mt-3" accept="image/*">
            <div class="form-text text-muted">Unggah gambar baru untuk mengganti yang lama. Biarkan kosong jika tidak ingin mengganti.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control">{{ $masterStock->description }}</textarea>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
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
