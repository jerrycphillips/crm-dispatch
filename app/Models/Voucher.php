<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'APVouchers'; // Actual SQL Server table name

    // Primary key field name
    protected $primaryKey = 'VoucherId';

    // Disable auto-incrementing if you don't want Laravel to expect it
    public $incrementing = true;

    // Enable Laravel's timestamp handling
    public $timestamps = false;

    // Optional: If your primary key isn't a UUID or string
    protected $keyType = 'int';

    // Optional: Mass assignable fields (customize this later as needed)
    protected $fillable = [
    'PurchaseOrderId',
    'PackingSlip',
    'PSFiledBy',
    'PSFiledTime',
    'PSSignatureName',
    'PSPosted',
    'PSPostedBy',
    'PsPostedTime',
    'NoPS',
    'Vendor',
    'Invoice',
    'InvoiceFiledBy',
    'InvoiceFiledTime',
    'InvoicePosted',
    'InvoicePostedBy',
    'InvoicePostedTime',
    'InvoiceDate',
    'InvoiceNo',
    'InvoiceTotal',
    'PaymentPosted',
    'PaymentPostedBy',
    'PaymentPostedTime',
    'PaidDate',
    'PaidBy',
    'PaidTime',
    'CheckNo',
    'ReAuthorizeNotes',
    'ReAuthoriseDate',
    'ReAuthoriseMeans',
    'OriginalModifiedby',
    'NoInv',
    'vendorRequestID',
];

    /**
     * Get the purchase order that this voucher belongs to.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'PurchaseOrderId', 'PurchaseOrder');
    }

    /**
     * Get the vendor that this voucher belongs to.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'Vendor', 'ListID');
    }

    /**
     * Get the packing slip URL from the local file path.
     */
    public function getPackingSlipUrlAttribute()
    {
        if (empty($this->PackingSlip)) {
            return null;
        }

        // Extract filename from path like "F:\AP\PS\ps32190.pdf"
        $filename = basename($this->PackingSlip);
        
        return "https://dcservice.designcomfortco.com/packingslips/{$filename}";
    }

    /**
     * Get the invoice URL from the local file path.
     */
    public function getInvoiceUrlAttribute()
    {
        if (empty($this->Invoice)) {
            return null;
        }

        // Extract filename from path like "F:\AP\Invoice\14c71b61-2b70-4867-a5ee-e916f8d15e46.pdf"
        $filename = basename($this->Invoice);
        
        return "https://dcservice.designcomfortco.com/bills/{$filename}";
    }
}