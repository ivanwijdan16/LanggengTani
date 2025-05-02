@extends('layouts.app')

@section('style')
  <style>
    :root {
      --primary-color: #149d80;
      --primary-dark: #0c8b71;
      --primary-light: rgba(0, 114, 79, 0.1);
      --text-dark: #1e293b;
      --text-medium: #475569;
      --text-light: #64748b;
      --card-border: #f1f5f9;
      --background-light: #f8fafc;
      --white: #ffffff;
      --danger: #ef4444;
      --warning: #f59e0b;
      --success: #10b981;
    }

    .page-container {
      padding: 1.5rem 0;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-dark);
      display: flex;
      align-items: center;
      margin: 0;
    }

    .page-title i {
      color: var(--primary-color);
      margin-right: 0.75rem;
      font-size: 1.5rem;
    }

    /* Cards Styling */
    .card {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-bottom: 2rem;
      background-color: var(--white);
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .card-img-container {
      position: relative;
      height: 180px;
      overflow: hidden;
      background-color: var(--background-light);
    }

    .card-img-top {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover .card-img-top {
      transform: scale(1.05);
    }

    .stock-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      border-radius: 20px;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 500;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      background-color: var(--primary-color);
      color: var(--white);
    }

    .expired-badge {
      background-color: var(--danger);
    }

    .low-stock-badge {
      background-color: var(--warning);
    }

    .card-body {
      padding: 1.25rem;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 0.5rem;
      line-height: 1.3;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .product-type {
      display: inline-block;
      background-color: var(--primary-light);
      color: var(--primary-color);
      border-radius: 20px;
      padding: 0.2rem 0.6rem;
      font-size: 0.7rem;
      font-weight: 500;
      margin-bottom: 0.75rem;
    }

    .product-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
    }

    .product-price {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    .product-quantity {
      font-size: 0.85rem;
      color: var(--text-medium);
      font-weight: 500;
    }

    .product-expiry {
      font-size: 0.75rem;
      color: var(--text-light);
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
    }

    .product-expiry i {
      margin-right: 0.3rem;
    }

    .expired-text {
      color: var(--danger);
      font-weight: 500;
    }

    .add-to-cart-btn {
      width: 100%;
      padding: 0.6rem 1rem;
      margin-top: 1rem;
      border-radius: 8px;
      background-color: var(--primary-color);
      border: none;
      color: var(--white);
      font-weight: 500;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }

    .add-to-cart-btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .add-to-cart-btn i {
      margin-right: 0.5rem;
    }

    .expired-btn {
      background-color: var(--text-light);
      cursor: not-allowed;
    }

    .expired-btn:hover {
      background-color: var(--text-light);
      transform: none;
    }

    /* Search Bar Styling */
    .search-container {
      position: relative;
      margin-bottom: 1rem;
    }

    .search-input {
      padding: 0.75rem 1rem 0.75rem 3rem;
      border-radius: 10px;
      border: 1px solid var(--card-border);
      width: 100%;
      font-size: 0.95rem;
      transition: all 0.2s;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(0, 114, 79, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 1.2rem;
    }

    /* Cart Section Styling */
    .cart-container {
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      background-color: var(--white);
      padding: 1.5rem;
      position: sticky;
      top: 20px;
    }

    .cart-header {
      display: flex;
      align-items: center;
      margin-bottom: 1.25rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--card-border);
    }

    .cart-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-dark);
      margin: 0;
      display: flex;
      align-items: center;
    }

    .cart-title i {
      color: var(--primary-color);
      margin-right: 0.5rem;
      font-size: 1.3rem;
    }

    .cart-items {
      margin-bottom: 1.5rem;
      max-height: 320px;
      overflow-y: auto;
      padding-right: 0.5rem;
    }

    .cart-item {
      display: flex;
      align-items: center;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--card-border);
    }

    .cart-item:last-child {
      border-bottom: none;
    }

    .cart-item-details {
      flex: 1;
    }

    .cart-item-name {
      font-size: 0.95rem;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 0.2rem;
    }

    .cart-item-price {
      font-size: 0.85rem;
      color: var(--text-medium);
    }

    .cart-item-quantity {
      display: flex;
      align-items: center;
      margin: 0 1rem;
    }

    .cart-quantity-text {
      padding: 0.2rem 0.6rem;
      min-width: 2rem;
      text-align: center;
      font-weight: 500;
      color: var(--text-dark);
    }

    .cart-item-total {
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-right: 0.75rem;
      white-space: nowrap;
    }

    .cart-remove-btn {
      background-color: transparent;
      border: none;
      color: var(--danger);
      font-size: 1.1rem;
      padding: 0.3rem;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .cart-remove-btn:hover {
      transform: scale(1.1);
    }

    .cart-empty {
      text-align: center;
      padding: 2rem 0;
      color: var(--text-light);
    }

    .cart-empty i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #cbd5e1;
    }

    .cart-summary {
      background-color: var(--background-light);
      border-radius: 10px;
      padding: 1.25rem;
      margin-bottom: 1.5rem;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
      color: var(--text-medium);
    }

    .summary-row.total {
      margin-top: 0.5rem;
      padding-top: 0.5rem;
      border-top: 1px dashed var(--card-border);
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-dark);
    }

    .payment-input {
      padding: 0.75rem;
      border-radius: 8px;
      border: 1px solid var(--card-border);
      width: 100%;
      font-size: 0.95rem;
      margin-bottom: 1rem;
      transition: all 0.2s;
    }

    .payment-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(0, 114, 79, 0.1);
    }

    .payment-label {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-medium);
      margin-bottom: 0.5rem;
      display: block;
    }

    .checkout-btn {
      width: 100%;
      padding: 0.75rem;
      border-radius: 8px;
      background-color: var(--primary-color);
      border: none;
      color: var(--white);
      font-weight: 600;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }

    .checkout-btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .checkout-btn i {
      margin-right: 0.5rem;
    }

    /* Alert Styling */
    .alert {
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      position: relative;
      border: none;
    }

    .alert-danger {
      background-color: #fee2e2;
      color: #b91c1c;
    }

    .alert-success {
      background-color: #dcfce7;
      color: #15803d;
    }

    .alert strong {
      font-weight: 600;
    }

    .btn-close {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      font-size: 1.2rem;
      color: currentColor;
      opacity: 0.7;
    }

    .btn-close:hover {
      opacity: 1;
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
      cursor: pointer;
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

    /* Responsive Adjustments */
    @media (max-width: 992px) {
      .cart-container {
        position: static;
        margin-top: 2rem;
      }
    }

    @media (max-width: 768px) {
      .product-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .sort-links {
        justify-content: flex-start;
        margin-bottom: 20px;
      }
    }

    @media (max-width: 576px) {
      .product-grid {
        grid-template-columns: 1fr;
      }

      .cart-item {
        flex-wrap: wrap;
      }

      .cart-item-details {
        width: 100%;
        margin-bottom: 0.5rem;
      }

      .cart-item-actions {
        display: flex;
        justify-content: space-between;
        width: 100%;
        align-items: center;
      }

      .sort-links {
        gap: 5px;
      }

      .sort-link {
        padding: 4px 8px;
        font-size: 0.8rem;
      }
    }

    /* Scrollbar Styling */
    .cart-items::-webkit-scrollbar {
      width: 6px;
      border-radius: 3px;
    }

    .cart-items::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 3px;
    }

    .cart-items::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 3px;
    }
  </style>
@endsection

@section('content')
  <div class="container page-container">
    <!-- Error Alert -->
    @if (session('error'))
      <div class="alert alert-danger" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bx bx-x"></i>
        </button>
      </div>
    @endif

    <!-- Success Alert -->
    @if (session('success'))
      <div class="alert alert-success" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <i class="bx bx-x"></i>
        </button>
      </div>
    @endif

    <div class="row">
      <!-- Kolom Kiri: Daftar Barang -->
      <div class="col-lg-8">
        <div class="page-header">
          <h1 class="page-title">
            <i class="bx bx-cart"></i> Kasir
          </h1>
        </div>

        <!-- Pencarian Barang -->
        <div class="search-container">
          <i class="bx bx-search search-icon"></i>
          <input type="text" id="search-bar" class="search-input" placeholder="Cari nama barang..." />
        </div>

        <!-- Sorting Links -->
        <div class="sort-links mb-4">
          <p class="sort-label mb-0">Urutkan:</p>
          <a href="#" id="sort-name" class="sort-link">Nama</a>
          <a href="#" id="sort-price" class="sort-link">Harga</a>
          <a href="#" id="sort-expiry" class="sort-link">Kadaluwarsa</a>
          <a href="#" id="sort-newest" class="sort-link active">Terbaru <i class="bx bx-sort-down"></i></a>
        </div>

        <!-- Daftar Barang dalam bentuk Card Grid -->
        <div class="row" id="products-container">
          <!-- Products will be loaded here by AJAX -->
        </div>
      </div>

      <!-- Kolom Kanan: Keranjang Belanja -->
      <div class="col-lg-4">
        <div class="cart-container">
          <div class="cart-header">
            <h2 class="cart-title">
              <i class="bx bx-cart"></i> Keranjang Belanja
            </h2>
          </div>

          <div class="cart-items" id="cart-container">
            <!-- Cart items will be loaded here by AJAX -->
          </div>

          <div class="cart-summary">
            <div class="summary-row total">
              <span>Total</span>
              <span id="display-total">Rp 0</span>
            </div>
          </div>

          <form action="{{ route('checkout') }}" method="GET">
            <label for="total_paid" class="payment-label">Jumlah Bayar</label>
            <input type="number" class="payment-input" name="total_paid" id="total_paid" required>
            <input type="hidden" id="total_price" name="total_price">

            <button type="submit" class="checkout-btn">
              <i class="bx bx-check-circle"></i> Bayar Sekarang
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    // Menyimpan variabel sorting
    let currentSort = 'newest'; // Default sort
    let currentDirection = 'desc'; // Default direction

    // Fungsi untuk mengupdate tampilan produk
    function fetchProducts(query = '', sortBy = currentSort, direction = currentDirection) {
      $.ajax({
        url: "{{ route('cart.search') }}",
        method: 'GET',
        data: {
          query: query,
          sort_by: sortBy,
          direction: direction
        },
        success: function(response) {
          let productsHtml = '';
          if (response.products.length > 0) {
            // Sort products based on the selected option
            let sortedProducts = [...response.products];

            if (sortBy === 'name') {
              sortedProducts.sort((a, b) => {
                return direction === 'asc' ?
                  a.name.localeCompare(b.name) :
                  b.name.localeCompare(a.name);
              });
            } else if (sortBy === 'price') {
              sortedProducts.sort((a, b) => {
                return direction === 'asc' ?
                  a.selling_price - b.selling_price :
                  b.selling_price - a.selling_price;
              });
            } else if (sortBy === 'expiry') {
              sortedProducts.sort((a, b) => {
                const dateA = new Date(a.expiration_date);
                const dateB = new Date(b.expiration_date);
                return direction === 'asc' ?
                  dateA - dateB :
                  dateB - dateA;
              });
            }
            // For 'newest', we assume the products are already sorted by the backend

            sortedProducts.forEach(function(product) {
              // console.log(product.master_stock);

              let isExpired = product.expired;
              let expirationDate = new Date(product.expiration_date);
              let formattedDate = expirationDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
              });
              let image = product.master_stock.image ? '/storage/' + product.master_stock.image :
                '/images/default.png';
              let isLowStock = product.quantity < 5;

              let badgeClass = isExpired ? 'expired-badge' : (isLowStock ? 'low-stock-badge' : '');
              let badgeText = isExpired ? 'Kadaluarsa' : (isLowStock ? 'Stok Terbatas' : product.quantity +
                ' pcs');

              // console.log(image);


              productsHtml += `
              <div class="col-md-6 col-xl-4 px-3">
                <div class="card ${isExpired ? 'border-danger' : ''}">
                  <div class="card-img-container">
                    <img src="${image}" class="card-img-top" alt="${product.master_stock.name}">
                  </div>
                  <div class="card-body">
                    <span class="product-type">${product.master_stock.type}</span>
                    <h5 class="card-title">${product.master_stock.name} (${product.size})</h5>

                    <div class="product-meta">
                      <span class="product-price">Rp ${new Intl.NumberFormat('id-ID').format(product.selling_price)}</span>
                      ${product.retail_price !== null ? `
                            <div class="d-flex flex-column">
                              <span class="product-quantity">Stok: ${product.quantity}</span>
                              <span class="product-quantity">Eceran: ${product.retail_quantity}</span>
                            </div>
                          ` : `<span class="product-quantity">Stok: ${product.quantity}</span>`
                      }
                    </div>

                    <div class="product-quantity-input row">
                      <label for="quantity-${product.id}">Jumlah:</label>
                      <input type="number" id="quantity-${product.id}" name="quantity" value="1" min="1" max="${product.quantity}" class="form-control">
                    </div>

                    <br>

                    <div class="product-expiry">
                      <i class="bx ${isExpired ? 'bx-x-circle' : 'bx-calendar'}"></i>
                      <span class="${isExpired ? 'expired-text' : ''}">
                        ${isExpired ? 'Sudah Kadaluarsa' : 'Exp: ' + formattedDate}
                      </span>
                    </div>

                    <!-- Check if retail_price is not null -->
                    ${product.retail_price !== null ? `
                                  <button type="button" class="add-to-cart-btn ${isExpired ? 'expired-btn' : ''}" ${isExpired ? 'disabled' : 'onclick="addToCart(' + product.id + ')"'}>
                                    <i class="bx ${isExpired ? 'bx-x' : 'bx-cart-add'}"></i>
                                    Tambahkan
                                  </button>
                                  <button type="button" class="add-to-cart-btn ${isExpired ? 'expired-btn' : ''}" ${isExpired ? 'disabled' : 'onclick="addToCart(' + product.id + ',`retail`)"'}>
                                    <i class="bx ${isExpired ? 'bx-x' : 'bx-cart-add'}"></i>
                                    Tambah Eceran
                                  </button>
                                ` : `
                                  <button type="button" class="add-to-cart-btn ${isExpired ? 'expired-btn' : ''}" ${isExpired ? 'disabled' : 'onclick="addToCart(' + product.id + ')"'}>
                                    <i class="bx ${isExpired ? 'bx-x' : 'bx-cart-add'}"></i>
                                    Tambahkan
                                  </button>
                                `}
                  </div>
                </div>
              </div>
            `;
            });
          } else {
            productsHtml = `
            <div class="col-12 text-center py-5">
              <i class="bx bx-package" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
              <h4 style="color: #64748b; font-weight: 500;">Tidak ada stok ditemukan</h4>
              <p style="color: #94a3b8;">Coba cari dengan kata kunci lain</p>
            </div>
          `;
          }

          $('#products-container').html(productsHtml);
        }
      });
    }

    // Fungsi untuk menambahkan barang ke keranjang
    function addToCart(productId, type) {
      const quantity = $(`#quantity-${productId}`).val();
      $.ajax({
        url: "{{ route('cart.add') }}",
        method: 'POST',
        data: {
          product_id: productId,
          quantity: quantity, // Default amount is 1
          type: type ?? 'normal',
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          loadCart(); // Reload cart after adding an item
        }
      });
    }

    // Fungsi untuk memuat data keranjang menggunakan AJAX
    function loadCart() {
      $.ajax({
        url: "{{ route('cart.get') }}",
        method: 'GET',
        success: function(response) {
          let cartHtml = '';
          let totalPrice = 0; // Total price for cart
          let cartIsEmpty = response.carts.length === 0; // Check if cart is empty

          if (!cartIsEmpty) {
            response.carts.forEach(function(cart) {
              totalPrice += parseFloat(cart.subtotal);
              cartHtml += `
              <div class="cart-item">
                <div class="cart-item-details">
                  <div class="cart-item-name">${cart.product.master_stock.name} <span style="font-size: 0.8rem; color: var(--text-medium);">(${cart.product.size})</span></div>
                  <div class="cart-item-price">Rp ${new Intl.NumberFormat('id-ID').format(cart.type == 'normal' ? cart.product.selling_price : cart.product.retail_price)} × ${cart.quantity}</div>
                </div>
                <div class="cart-item-total">Rp ${new Intl.NumberFormat('id-ID').format(cart.subtotal)}</div>
                <button type="button" class="cart-remove-btn" onclick="removeFromCart(${cart.id})">
                  <i class="bx bx-trash"></i>
                </button>
              </div>
            `;
            });
          } else {
            cartHtml = `
            <div class="cart-empty">
              <i class="bx bx-cart"></i>
              <h4>Keranjang Kosong</h4>
              <p>Tambahkan barang ke keranjang</p>
            </div>
          `;
          }

          $('#cart-container').html(cartHtml);

          // Display total price
          const formattedTotal = new Intl.NumberFormat('id-ID').format(totalPrice);
          $('#display-total').text('Rp ' + formattedTotal);
          $('#total_price').val(totalPrice);

          // Enable/disable payment form based on cart status
          $('#total_paid').prop('disabled', cartIsEmpty);
          $('.checkout-btn').prop('disabled', cartIsEmpty);

          // Update checkout button style based on cart status
          if (cartIsEmpty) {
            $('.checkout-btn').css({
              'background-color': 'var(--text-light)',
              'cursor': 'not-allowed',
              'opacity': '0.6'
            }).hover(function() {
              $(this).css({
                'transform': 'none',
                'background-color': 'var(--text-light)'
              });
            }, function() {
              $(this).css({
                'transform': 'none',
                'background-color': 'var(--text-light)'
              });
            });
          } else {
            $('.checkout-btn').css({
              'background-color': 'var(--primary-color)',
              'cursor': 'pointer',
              'opacity': '1'
            }).off('mouseenter mouseleave');
          }
        }
      });
    }

    // Event handlers for sort links
    $('.sort-link').on('click', function(e) {
      e.preventDefault();

      // Get the current id
      const clickedId = this.id;

      // Check if clicking the same sort option
      if (clickedId === 'sort-' + currentSort) {
        // Toggle direction
        currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
      } else {
        // New sort option, reset direction to default based on sort type
        if (clickedId === 'sort-newest') {
          currentDirection = 'desc'; // Newest first
        } else {
          currentDirection = 'asc'; // A-Z, low-high, expiry-soon
        }
      }

      // Remove active class and icons from all links
      $('.sort-link').removeClass('active').find('i').remove();

      // Add active class to clicked link
      $(this).addClass('active');

      // Add appropriate icon based on direction
      if (currentDirection === 'asc') {
        $(this).append(' <i class="bx bx-sort-up"></i>');
      } else {
        $(this).append(' <i class="bx bx-sort-down"></i>');
      }

      // Set the current sort based on button id
      if (clickedId === 'sort-name') {
        currentSort = 'name';
      } else if (clickedId === 'sort-price') {
        currentSort = 'price';
      } else if (clickedId === 'sort-expiry') {
        currentSort = 'expiry';
      } else if (clickedId === 'sort-newest') {
        currentSort = 'newest';
      }

      // Fetch products with new sort order
      fetchProducts($('#search-bar').val(), currentSort, currentDirection);
    });

    // Product Search
    $('#search-bar').on('input', function() {
      let query = $(this).val();
      fetchProducts(query, currentSort, currentDirection);
    });

    function removeFromCart(cartId) {
      $.ajax({
        url: `/cart/${cartId}`,
        type: 'POST',
        data: {
          _method: 'DELETE',
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          loadCart();
        },
        error: function(xhr, status, error) {
          alert('Terjadi kesalahan saat menghapus barang.');
        }
      });
    }

    // Load products and cart when page first loads
    $(document).ready(function() {
      fetchProducts();
      loadCart();
    });
  </script>
@endsection
