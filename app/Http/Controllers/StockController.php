<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Pembelian;
use App\Models\MasterStock;
use Illuminate\Http\Request;
use App\Models\MasterPembelian;
use App\Helpers\IdGenerator;
use Illuminate\Support\Facades\Storage;


class StockController extends Controller
{
  public function index(Request $request)
  {
    $query = MasterStock::query();

    // Pencarian berdasarkan nama
    if ($request->has('search')) {
      $query->where('name', 'LIKE', '%' . $request->search . '%')
        ->orWhere('sku', 'LIKE', '%' . $request->search . '%');
    }

    // Pengurutan
    $sort = $request->input('sort', 'name');
    $direction = $request->input('direction', 'asc');

    // Memastikan field sort valid untuk menghindari SQL injection
    $allowedSortFields = [
      'name',
      'selling_price',
      'expiration_date',
      'created_at'
    ];

    if (in_array($sort, $allowedSortFields)) {
      $query->orderBy($sort, $direction);
    } else {
      // Default fallback ke created_at desc
      $query->orderBy('name', 'asc');
    }

    $stocks = $query->paginate(12);

    return view('stocks.index', compact('stocks'));
  }

  public function detail(Request $request, $id)
  {
    $query = Stock::query();

    $query->with('masterStock');

    $query->where('master_stock_id', $id);

    // // Pencarian berdasarkan nama
    // if ($request->has('search')) {
    //   $query->where('name', 'LIKE', '%' . $request->search . '%')
    //     ->orWhere('sku', 'LIKE', '%' . $request->search . '%');
    // }

    // Pengurutan
    $sort = $request->input('sort', 'created_at');
    $direction = $request->input('direction', 'desc');

    // Memastikan field sort valid untuk menghindari SQL injection
    $allowedSortFields = [
      'name',
      'selling_price',
      'expiration_date',
      'created_at'
    ];

    if ($sort === 'name') {
      $query->orderBy(MasterStock::select('stock_name')->whereColumn('master_stocks.id', 'stocks.master_stock_id'), $direction);
    } else if (in_array($sort, $allowedSortFields)) {
      $query->orderBy($sort, $direction);
    } else {
      // Default fallback to created_at desc
      $query->orderBy('created_at', 'desc');
    }

    $stocks = $query->paginate(12);

    return view('stocks.detail', compact('stocks', 'id'));
  }

  public function create()
  {
    return view('stocks.create');
  }

  public function store(Request $request)
{
    $validated = $request->validate([
        'name.*' => 'required|string|max:255',
        'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description.*' => 'nullable|string',
        'type.*' => 'required|in:Obat,Pupuk,Bibit',
        'size.*' => 'required',
        'purchase_price.*' => 'required|numeric|min:0',
        'selling_price.*' => 'required|numeric|min:0',
        'quantity.*' => 'required|integer|min:1',
        'expiration_date.*' => 'required|date',
        'retail_price.*' => 'nullable',
        'retail_quantity.*' => 'nullable',
        'sub_type.*' => 'nullable',
    ]);

    // Initialize total variable
    $total = 0;

    // Loop through the validated data to calculate total purchase_price * quantity
    for ($i = 0; $i < count($validated['purchase_price']); $i++) {
        $purchasePrice = $validated['purchase_price'][$i];
        $quantity = $validated['quantity'][$i];

        // Calculate total for the current item and add it to the total
        $total += $purchasePrice * $quantity;
    }

    $master_pembelian = MasterPembelian::create([
        'total' => $total,
        'date' => date('Y-m-d'),
    ]);

    for ($i = 0; $i < count($validated['name']); $i++) {
        // Access the validated data for each item in the array
        $name = $validated['name'][$i];
        $image = $validated['image'][$i] ?? null; // Image is nullable
        $description = $validated['description'][$i] ?? null;
        $type = $validated['type'][$i];
        $sub_type = $validated['sub_type'][$i] ?? null;
        $size = $validated['size'][$i];
        $purchase_price = $validated['purchase_price'][$i];
        $selling_price = $validated['selling_price'][$i];
        $quantity = $validated['quantity'][$i];
        $expiration_date = $validated['expiration_date'][$i];
        $retail_price = $validated['retail_price'][$i] ?? null;
        $retail_quantity = $validated['retail_quantity'][$i] ?? null;

        if ($request->hasFile("image.$i")) {  // Check for each individual file
            $imagePath = $request->file("image.$i")->store('stocks', 'public');
            // Store the image path in the validated data
            $validated['image'][$i] = $imagePath;
        }

        // Generate SKU using the new format
        $sku = IdGenerator::generateSku($name, $type, $sub_type);

        $existingMasterStock = MasterStock::where('name', $name)
            ->where('type', $type)
            ->where('sub_type', $sub_type)
            ->first();

        // Create or get master stock first
        if ($existingMasterStock) {
            $masterStockId = $existingMasterStock->id;

            if ($request->hasFile("image.$i")) {
                // Hapus gambar lama jika ada
                if ($existingMasterStock->image) {
                    Storage::disk('public')->delete($existingMasterStock->image);
                }
                $existingMasterStock->image = $request->file("image.$i")->store('stocks', 'public');
            }

            $existingMasterStock->description = $description;
            $existingMasterStock->save();
            $masterStock = $existingMasterStock;
            $sku = $existingMasterStock->sku; // Use existing SKU
        } else {
            // Create a new master stock with the new SKU format
            $masterStock = MasterStock::create([
                'name' => $name,
                'image' => $validated['image'][$i] ?? null,
                'description' => $description,
                'type' => $type,
                'sub_type' => $sub_type,
                'sku' => $sku, // Save the new SKU format
            ]);

            $masterStockId = $masterStock->id;
        }

        // Now handle the stock
        // Get the batch number - you may want to customize this logic
        $batchNumber = 1; // Default to 1 for new items

        // Generate the new stock_id format
        $stockId = IdGenerator::generateStockId($sku, $size, $expiration_date, $batchNumber);

        // Check if stock with this ID exists
        $existingStock = Stock::withTrashed()->where('stock_id', $stockId)->first();

        if ($existingStock) {
            if ($existingStock->trashed()) {
                // Jika stok sebelumnya telah dihapus (soft delete), restore dan update jumlahnya
                $existingStock->restore();
                $existingStock->quantity = $quantity;
                $existingStock->save();
            } else {
                // Jika stok ada dan tidak dihapus, tambahkan jumlahnya
                $existingStock->increment('quantity', $quantity);
            }
            // Catat pembelian ke tabel pembelian
            $this->createPembelian($existingStock, $quantity, $purchase_price, $expiration_date, $master_pembelian);
        } else {
            // Jika stok baru, buat entri stok baru
            $stock = Stock::create([
                'master_stock_id' => $masterStockId,
                'size' => $size,
                'purchase_price' => $purchase_price,
                'selling_price' => $selling_price,
                'quantity' => $quantity,
                'expiration_date' => $expiration_date,
                'retail_price' => $retail_price,
                'retail_quantity' => $retail_quantity,
                'stock_id' => $stockId,
            ]);

            $stock->checkAndCreateNotifications();

            // Catat pembelian ke tabel pembelian
            $this->createPembelian($stock, $quantity, $purchase_price, $expiration_date, $master_pembelian);
        }
    }

    // Simpan data untuk ditampilkan di modal sukses
    return redirect()->route('stocks.create')->with([
        'success' => 'Stok berhasil ditambahkan!',
        'product_name' => $masterStock->name,
        'product_type' => $masterStock->type,
        'product_size' => $stock->size,
        'product_quantity' => $stock->quantity,
        'product_selling_price' => $stock->selling_price
    ]);
}

  // Fungsi untuk mencatat pembelian baru
  // Update the createPembelian method to use a better purchase code
private function createPembelian($stock, $quantity, $purchasePrice, $expirationDate, $master_pembelian)
{
    // Generate purchase code using the helper
    $purchaseCode = IdGenerator::generatePurchaseCode();

    $pembelianData = [
        'stock_id' => $stock->id,
        'purchase_code' => $purchaseCode,
        'purchase_price' => $purchasePrice,
        'quantity' => $quantity,
        'purchase_date' => now(),
        'master_pembelians_id' => $master_pembelian->id,
    ];

    Pembelian::create($pembelianData);
}

  public function show($id)
  {
    $stock = Stock::with('masterStock')->findOrFail($id);
    return view('stocks.show', compact('stock'));
  }

  public function edit($id)
  {
    $stock = Stock::with('masterStock')->findOrFail($id);
    return view('stocks.edit', compact('stock'));
  }

  public function update(Request $request, $id)
  {
    $stock = Stock::findOrFail($id);

    $validated = $request->validate([
      'name' => 'sometimes|string|max:255',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'description' => 'nullable|string',
    ]);

    if ($request->hasFile('image')) {
      if ($stock->image) {
        Storage::disk('public')->delete($stock->image);
      }
      $validated['image'] = $request->file('image')->store('stocks', 'public');
    }

    if (isset($validated['name']) || isset($validated['size']) || isset($validated['expiration_date'])) {
      $validated['stock_id'] = Stock::generateStockId(
        $validated['name'] ?? $stock->name,
        $validated['size'] ?? $stock->size,
        $validated['expiration_date'] ?? $stock->expiration_date
      );
    }

    $stock->update($validated);
    $stock->checkAndCreateNotifications();
    return redirect()->route('stocks.index')->with('success', 'Stok berhasil diupdate.');
  }

  public function destroy($id)
  {
    $stock = Stock::findOrFail($id);

    // Cek apakah barang ini memiliki riwayat penjualan
    $hasTransactions = \App\Models\TransactionItem::where('product_id', $stock->id)->exists();

    // Cek kondisi untuk penghapusan
    $isExpired = \Carbon\Carbon::parse($stock->expiration_date)->isPast();
    $isStockZero = $stock->quantity <= 0;

    // Jika barang tidak memiliki riwayat penjualan, atau stoknya habis, atau sudah kadaluarsa
    if (!$hasTransactions || $isStockZero || $isExpired) {
      // Hapus gambar jika ada
      if ($stock->image) {
        Storage::disk('public')->delete($stock->image);
      }

      // Jika barang memiliki riwayat penjualan, gunakan soft delete
      if ($hasTransactions) {
        $stock->delete(); // soft delete
        return redirect()->route('stocks.index')->with([
          'showSuccessModal' => true,
          'message' => 'Stok berhasil dihapus. Data historis tetap tersimpan.'
        ]);
      } else {
        // Jika belum pernah dijual, hapus pembelian dan stok secara permanen
        \App\Models\Pembelian::where('stock_id', $stock->id)->delete();
        $stock->forceDelete();
        return redirect()->route('stocks.index')->with([
          'showSuccessModal' => true,
          'message' => 'Stok berhasil dihapus permanen.'
        ]);
      }
    } else {
      // Jika stok masih ada dan belum kadaluarsa, dan pernah dijual, tolak penghapusan
      return back()->with([
        'showErrorModal' => true,
        'stockId' => $stock->id,
        'error' => 'Stok tidak dapat dihapus. Stok masih tersisa dan belum kadaluarsa.'
      ]);
    }
  }
}
