<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Pembelian;
use App\Models\MasterStock;
use Illuminate\Http\Request;
use App\Models\MasterPembelian;
use App\Helpers\IdGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\StockSizeImage;

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
            'type',
            'created_at'
        ];

        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction);
        } else {
            // Default fallback ke name asc
            $query->orderBy('name', 'asc');
        }

        $stocks = $query->paginate(12);

        return view('stocks.index', compact('stocks'));
    }

    public function sizes($masterId, Request $request)
    {
        $masterStock = MasterStock::findOrFail($masterId);

        // Get all stocks for this master stock
        $stocks = Stock::where('master_stock_id', $masterId)->get();

        // Get sort parameters
        $sort = $request->input('sort', '');
        $direction = $request->input('direction', 'asc');

        // Group stocks by size
        $sizeGroups = $stocks->groupBy('size');

        // Apply sorting to each size group if sorting by price
        if ($sort === 'price') {
            // Sort each size group by selling_price
            foreach ($sizeGroups as $size => $stocksInSize) {
                if ($direction === 'asc') {
                    $sizeGroups[$size] = $stocksInSize->sortBy('selling_price');
                } else {
                    $sizeGroups[$size] = $stocksInSize->sortByDesc('selling_price');
                }
            }

            // Sort the size groups based on the first item's selling_price
            $sizeGroups = $direction === 'asc'
                ? $sizeGroups->sortBy(function ($stocks) {
                    return $stocks->first()->selling_price;
                })
                : $sizeGroups->sortByDesc(function ($stocks) {
                    return $stocks->first()->selling_price;
                });
        }

        // Get all size images for this master stock
        $sizeImages = StockSizeImage::where('master_stock_id', $masterId)->get()->keyBy('size');

        return view('stocks.sizes', compact('masterStock', 'sizeGroups', 'sizeImages', 'sort', 'direction'));
    }
    public function batches($masterId, $size, Request $request)
    {
        $masterStock = MasterStock::findOrFail($masterId);

        // Get sort parameters
        $sort = $request->input('sort', 'expiration_date');
        $direction = $request->input('direction', 'asc');

        // Get all stocks for this master stock and size with sorting
        $query = Stock::where('master_stock_id', $masterId)
            ->where('size', $size);

        // Apply sorting
        if ($sort === 'expiration_date') {
            $query->orderBy('expiration_date', $direction);
        }

        $stocks = $query->get();

        // Get the size image if it exists
        $sizeImage = StockSizeImage::where('master_stock_id', $masterId)
            ->where('size', $size)
            ->first();

        return view('stocks.batches', compact('masterStock', 'stocks', 'size', 'sizeImage', 'sort', 'direction'));
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function createSize($masterId, $size = null)
    {
        $masterStock = MasterStock::findOrFail($masterId);

        // Get a representative stock for this size to pre-fill values
        $sizeStock = Stock::where('master_stock_id', $masterId)
            ->where('size', $size)
            ->first();

        return view('stocks.create_size', compact('masterStock', 'size', 'sizeStock'));
    }

    public function editMaster($id)
    {
        $masterStock = MasterStock::findOrFail($id);
        return view('stocks.edit_master', compact('masterStock'));
    }

    public function editSize($masterId, $size)
    {
        $masterStock = MasterStock::findOrFail($masterId);

        // Get a representative stock for this size
        $sizeStock = Stock::where('master_stock_id', $masterId)
            ->where('size', $size)
            ->first();

        // Get the size image if it exists
        $sizeImage = StockSizeImage::where('master_stock_id', $masterId)
            ->where('size', $size)
            ->first();

        return view('stocks.edit_size', compact('masterStock', 'size', 'sizeStock', 'sizeImage'));
    }

    public function show($id)
    {
        $stock = Stock::with('masterStock')->findOrFail($id);

        $expired = \Carbon\Carbon::parse($stock->expiration_date)->isPast();
        $almostExpired = !$expired && \Carbon\Carbon::parse($stock->expiration_date)->diffInDays(now()) < 30;

        return view('stocks.show', compact('stock', 'expired', 'almostExpired'));
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

        // Initialize arrays to store all product information for the session
        $productNames = [];
        $productTypes = [];
        $productSizes = [];
        $productQuantities = [];
        $productPrices = [];

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

            // Add product info to arrays for session
            $productNames[] = $name;
            $productTypes[] = $type;
            $productSizes[] = $size;
            $productQuantities[] = $quantity;
            $productPrices[] = $selling_price;

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

        return redirect()->route('stocks.create')->with([
            'success' => 'Stok berhasil ditambahkan!',
            // Store all product info in session
            'product_names' => $productNames,
            'product_types' => $productTypes,
            'product_sizes' => $productSizes,
            'product_quantities' => $productQuantities,
            'product_prices' => $productPrices,
            // Also keep the last item for backward compatibility
            'product_name' => end($productNames),
            'product_type' => end($productTypes),
            'product_size' => end($productSizes),
            'product_quantity' => end($productQuantities),
            'product_selling_price' => end($productPrices)
        ]);
    }

    public function storeSize(Request $request)
    {
        $validated = $request->validate([
            'master_stock_id' => 'required|exists:master_stocks,id',
            'size' => 'required',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'expiration_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $masterStock = MasterStock::findOrFail($validated['master_stock_id']);

        // Handle size-specific image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stocks', 'public');

            // Create or update the size image record
            StockSizeImage::updateOrCreate(
                [
                    'master_stock_id' => $validated['master_stock_id'],
                    'size' => $validated['size']
                ],
                [
                    'image' => $imagePath
                ]
            );
        }

        // Create master pembelian entry
        $master_pembelian = MasterPembelian::create([
            'total' => $validated['purchase_price'] * $validated['quantity'],
            'date' => date('Y-m-d'),
        ]);

        // Get the batch number for new stock
        $batchNumber = Stock::where('master_stock_id', $validated['master_stock_id'])
            ->where('size', $validated['size'])
            ->count() + 1;

        // Generate stock ID
        $stockId = IdGenerator::generateStockId(
            $masterStock->sku,
            $validated['size'],
            $validated['expiration_date'],
            $batchNumber
        );

        // Create stock
        $stock = Stock::create([
            'master_stock_id' => $validated['master_stock_id'],
            'size' => $validated['size'],
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'quantity' => $validated['quantity'],
            'expiration_date' => $validated['expiration_date'],
            'stock_id' => $stockId,
        ]);

        // Create pembelian record
        $this->createPembelian($stock, $validated['quantity'], $validated['purchase_price'], $validated['expiration_date'], $master_pembelian);

        // Check for notifications
        $stock->checkAndCreateNotifications();

        return redirect()->route('stocks.sizes', $validated['master_stock_id'])->with([
            'success' => 'Stok berhasil ditambahkan!',
            'quantity' => $validated['quantity'],
            'product_name' => $masterStock->name,
            'product_size' => $validated['size']
        ]);
    }

    public function updateMaster(Request $request, $id)
    {
        $masterStock = MasterStock::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Obat,Pupuk,Bibit',
            'sub_type' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($masterStock->image) {
                Storage::disk('public')->delete($masterStock->image);
            }
            $validated['image'] = $request->file('image')->store('stocks', 'public');
        }

        $masterStock->update($validated);

        return redirect()->route('stocks.index')->with([
            'success' => true,
            'message' => 'Produk berhasil diupdate.'
        ]);
    }

    public function updateSize(Request $request)
    {
        $validated = $request->validate([
            'master_stock_id' => 'required|exists:master_stocks,id',
            'size' => 'required',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $masterStock = MasterStock::findOrFail($validated['master_stock_id']);

        // Handle size-specific image
        if ($request->hasFile('image')) {
            // Check if there's an existing size image
            $existingSizeImage = StockSizeImage::where('master_stock_id', $validated['master_stock_id'])
                ->where('size', $validated['size'])
                ->first();

            if ($existingSizeImage && $existingSizeImage->image) {
                // Delete old image if it exists
                Storage::disk('public')->delete($existingSizeImage->image);
            }

            $imagePath = $request->file('image')->store('stocks', 'public');

            // Create or update the size image record
            StockSizeImage::updateOrCreate(
                [
                    'master_stock_id' => $validated['master_stock_id'],
                    'size' => $validated['size']
                ],
                [
                    'image' => $imagePath
                ]
            );
        }

        // Update purchase_price and selling_price for all stocks of this size
        Stock::where('master_stock_id', $validated['master_stock_id'])
            ->where('size', $validated['size'])
            ->update([
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price']
            ]);

        return redirect()->route('stocks.sizes', $validated['master_stock_id'])->with([
            'success' => true,
            'message' => 'Stok berhasil diupdate.'
        ]);
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
            // Jika barang memiliki riwayat penjualan, gunakan soft delete
            if ($hasTransactions) {
                $stock->delete(); // soft delete
                return redirect()->back()->with([
                    'showSuccessModal' => true,
                    'message' => 'Stok berhasil dihapus. Data historis tetap tersimpan.'
                ]);
            } else {
                // Jika belum pernah dijual, hapus pembelian dan stok secara permanen
                \App\Models\Pembelian::where('stock_id', $stock->id)->delete();
                $stock->forceDelete();
                return redirect()->back()->with([
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

    public function destroyMaster($id)
    {
        $masterStock = MasterStock::findOrFail($id);

        // Check if any associated stocks have transaction history
        $stockIds = Stock::where('master_stock_id', $id)->pluck('id');
        $hasTransactions = \App\Models\TransactionItem::whereIn('product_id', $stockIds)->exists();

        if ($hasTransactions) {
            // Use soft delete for stocks
            Stock::where('master_stock_id', $id)->delete();
        } else {
            // Delete associated pembelian records
            $stockIds = Stock::where('master_stock_id', $id)->pluck('id');
            \App\Models\Pembelian::whereIn('stock_id', $stockIds)->delete();

            // Force delete stocks
            Stock::where('master_stock_id', $id)->forceDelete();
        }

        // Delete master stock
        if ($masterStock->image) {
            Storage::disk('public')->delete($masterStock->image);
        }
        $masterStock->delete();

        return redirect()->route('stocks.index')->with([
            'showSuccessModal' => true,
            'message' => 'Produk dan semua stoknya berhasil dihapus.'
        ]);
    }

    public function destroySize($masterId, $size)
    {
        $masterStock = MasterStock::findOrFail($masterId);

        // Get all stocks for this size
        $stockIds = Stock::where('master_stock_id', $masterId)
            ->where('size', $size)
            ->pluck('id');

        // Check if any stocks have transaction history
        $hasTransactions = \App\Models\TransactionItem::whereIn('product_id', $stockIds)->exists();

        if ($hasTransactions) {
            // Use soft delete
            Stock::where('master_stock_id', $masterId)
                ->where('size', $size)
                ->delete();
        } else {
            // Delete associated pembelian records
            \App\Models\Pembelian::whereIn('stock_id', $stockIds)->delete();

            // Force delete stocks
            Stock::where('master_stock_id', $masterId)
                ->where('size', $size)
                ->forceDelete();
        }

        return redirect()->route('stocks.sizes', $masterId)->with([
            'showSuccessModal' => true,
            'message' => 'Stok dengan ukuran ' . $size . ' berhasil dihapus.'
        ]);
    }

    // Fungsi untuk mencatat pembelian baru
    private function createPembelian($stock, $quantity, $purchasePrice, $expirationDate, $master_pembelian)
    {
        $pembelianData = [
            'stock_id' => $stock->id,
            'purchase_price' => $purchasePrice,
            'quantity' => $quantity,
            'purchase_date' => now(),
            'master_pembelians_id' => $master_pembelian->id,
            'purchase_code' => $master_pembelian->purchase_code, // Gunakan kode pembelian dari master_pembelian
        ];

        Pembelian::create($pembelianData);
    }
}
