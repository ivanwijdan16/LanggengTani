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
     * Generate a SKU in the format [JLS]-[ProdukType]-[FirstSixLettersOfName]
     *
     * @param string $name Product name
     * @param string $type Product type
     * @param string $subType Product sub-type (optional)
     * @return string
     */
    public static function generateSku($name, $type, $subType = null)
    {
        // Company prefix
        $prefix = 'JLS';

        // Get type code (first 2 letters of type)
        $typeCode = strtoupper(substr($type, 0, 2));

        // Get name code (first 6 letters of name, remove non-alphanumeric characters and convert to uppercase)
        $cleanedName = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $nameCode = strtoupper(substr($cleanedName, 0, 6));

        // Format: PREFIX-TYPE-NAME
        return "{$prefix}-{$typeCode}-{$nameCode}";
    }

    /**
     * Generate a stock ID with more structure
     *
     * @param string $sku Product SKU
     * @param string $size Product size (will use only first character)
     * @param string $expirationDate Expiration date (not used in the ID)
     * @param string $batchNumber Batch number
     * @return string
     */
    public static function generateStockId($sku, $size, $expirationDate, $batchNumber = 1)
    {
        // Format size (uppercase, first character only)
        $sizeCode = strtoupper(substr($size, 0, 1));

        // Format entry date (today in YYMMDD format)
        $entryDate = date('ymd');

        // Format batch number
        $bn = str_pad($batchNumber, 3, '0', STR_PAD_LEFT);

        // Format: SKU-SIZE-TGLMASUK:YYMMDD-BN (removed EXP part)
        return "{$sku}-{$sizeCode}-{$entryDate}-{$bn}";
    }

    /**
     * Generate a transaction ID for sales
     *
     * @return string
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

    /**
     * Generate a purchase code
     *
     * @return string
     */
    public static function generatePurchaseCode()
    {
        // Current date
        $dateCode = date('Ymd');

        // Get the latest purchase with this date code
        $lastPurchase = DB::table('pembelians')
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

        // Format: PB-YYYYMMDD-XXXX
        return "PB-{$dateCode}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
