<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IdGenerator
{
    /**
     * Generate a formatted ID with prefix and sequential number
     *
     * @param string $prefix The prefix for the ID
     * @param string $table The table to check for existing IDs
     * @param string $field The field containing the ID
     * @param int $padLength The length to pad the numeric part
     * @return string
     */
    public static function generateId($prefix, $table, $field, $padLength = 5)
    {
        // Get the latest ID from the table
        $lastId = DB::table($table)
            ->select($field)
            ->where($field, 'like', $prefix . '%')
            ->orderBy($field, 'desc')
            ->first();

        if (!$lastId) {
            // If no existing IDs, start with 1
            $number = 1;
        } else {
            // Extract the numeric part from the last ID
            $lastNumber = substr($lastId->$field, strlen($prefix));
            $number = (int)$lastNumber + 1;
        }

        // Format the new ID
        return $prefix . str_pad($number, $padLength, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a purchase code
     *
     * @return string
     */
    public static function generatePurchaseCode()
    {
        // Current date
        $dateCode = date('Ymd');

        DB::beginTransaction();
        try {
            // Lock the table to prevent concurrent access
            DB::table('master_pembelians')->lockForUpdate()->get();

            // Get the latest purchase with this date code
            $lastPurchase = DB::table('master_pembelians')
                ->select('purchase_code')
                ->where('purchase_code', 'like', "PB-{$dateCode}-%")
                ->orderBy('id', 'desc')
                ->first();

            // Set sequence number
            $sequence = 1;
            if ($lastPurchase) {
                // Extract the sequence number
                $lastSeq = substr($lastPurchase->purchase_code, strrpos($lastPurchase->purchase_code, '-') + 1);
                $sequence = (int)$lastSeq + 1;
            }

            DB::commit();

            // Format: PB-YYYYMMDD-XXXX
            return "PB-{$dateCode}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Generate a SKU in the format [JLS]-[ProdukType]-[FirstSixLettersOfName]
     */
    public static function generateSku($name, $type, $subType = null)
    {
        // Company prefix
        $prefix = 'JLS';

        // Get type code (first 2 letters of type)
        $typeCode = strtoupper(substr($type, 0, 2));

        // Get name code (first 6 letters of name, remove non-alphanumeric)
        $cleanedName = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $nameCode = strtoupper(substr($cleanedName, 0, 6));

        // Get sequence number for this name and type
        $lastSku = DB::table('master_stocks')
            ->where('sku', 'like', "{$prefix}-{$typeCode}-{$nameCode}-%")
            ->orderBy('sku', 'desc')
            ->first();

        $sequence = 1;
        if ($lastSku) {
            $matches = [];
            preg_match('/(\d+)$/', $lastSku->sku, $matches);
            if (!empty($matches)) {
                $sequence = (int)$matches[1] + 1;
            }
        }

        // Format: PREFIX-TYPE-NAME-SEQUENCE
        return "{$prefix}-{$typeCode}-{$nameCode}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a stock ID with more structure
     */
    public static function generateStockId($sku, $size, $expirationDate, $batchNumber = 1)
    {
        // Format size (uppercase, first character only)
        $sizeCode = strtoupper(substr($size, 0, 1));

        // Format entry date (today in YYMMDD format)
        $entryDate = date('ymd');

        // Format batch number
        $bn = str_pad($batchNumber, 3, '0', STR_PAD_LEFT);

        // Format: SKU-SIZE-TGLMASUK:YYMMDD-BN
        return "{$sku}-{$sizeCode}-{$entryDate}-{$bn}";
    }

    /**
     * Generate a transaction ID for sales
     */
    public static function generateSaleId()
    {
        // Current date
        $dateCode = date('Ymd');

        // Get the latest transaction with this date code
        $lastTransaction = DB::table('transactions')
            ->select('id_penjualan')
            ->where('id_penjualan', 'like', "PJ-{$dateCode}-%")
            ->orderBy('id', 'desc')
            ->first();

        // Set sequence number
        $sequence = 1;
        if ($lastTransaction) {
            // Extract the sequence number
            $lastSeq = substr($lastTransaction->id_penjualan, strrpos($lastTransaction->id_penjualan, '-') + 1);
            $sequence = (int)$lastSeq + 1;
        }

        // Format: PJ-YYYYMMDD-XXXX
        return "PJ-{$dateCode}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
