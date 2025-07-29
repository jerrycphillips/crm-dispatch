<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'PurchaseOrder';

    // Primary key field name
    protected $primaryKey = 'PurchaseOrder';

    // Standard auto-incrementing integer primary key
    public $incrementing = true;
    protected $keyType = 'int';

    // Custom timestamp column
    const CREATED_AT = null;
    const UPDATED_AT = 'last_modified';

    // Enable Laravel's timestamp handling
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'RefNumber',
        'PurchaseOrderNo',
        'Date',
        'POType',
        'Type',
        'JobNo',
        'Customer',
        'JobQBID',
        'WorkOrderNum',
        'VendorQBID',
        'VendorName',
        'Delivery',
        'DueDate',
        'ExpectedDate',
        'TakenBy',
        'Employee',
        'IssuedBy',
        'AuthorizedBy',
        'Freight',
        'TaxRate',
        'Note',
        'MsgToVendor',
        'ClassQBID',
        'ShipMethodQBID',
        'IsToBePrinted',
        'ReadyToPost',
        'PostedToQB',
        'QBTransID',
        'Warehouse',
        'Closed',
        'PostedBy',
        'PostedDate',
        'PostedDirectlyToBill',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'Date' => 'datetime',
        'DueDate' => 'datetime',
        'ExpectedDate' => 'datetime',
        'PostedDate' => 'datetime',
        'last_modified' => 'datetime',
        'Freight' => 'decimal:2',
        'TaxRate' => 'decimal:3',
        'IsToBePrinted' => 'boolean',
        'ReadyToPost' => 'boolean',
        'PostedToQB' => 'boolean',
        'Closed' => 'boolean',
        'PostedDirectlyToBill' => 'boolean',
        'PurchaseOrderNo' => 'integer',
        'JobNo' => 'integer',
        'Customer' => 'integer',
        'WorkOrderNum' => 'integer',
        'Employee' => 'integer',
        'IssuedBy' => 'integer',
        'AuthorizedBy' => 'integer',
        'Warehouse' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the display number for this PO.
     */
    public function getDisplayNumberAttribute()
    {
        return $this->PurchaseOrderNo ?: $this->PurchaseOrder;
    }

    /**
     * Get the formatted PO number with prefix.
     */
    public function getFormattedNumberAttribute()
    {
        return 'PO-' . str_pad($this->display_number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the total amount from all line items.
     */
    public function getTotalAmountAttribute()
    {
        return $this->details->sum('Amount') + $this->Freight;
    }

    /**
     * Get the total with tax.
     */
    public function getTotalWithTaxAttribute()
    {
        $subtotal = $this->total_amount;
        return $subtotal + ($subtotal * $this->TaxRate);
    }

    /**
     * Get the status based on various flags.
     */
    public function getStatusAttribute()
    {
        if ($this->Closed) {
            return 'Closed';
        } elseif ($this->PostedToQB) {
            return 'Posted';
        } elseif ($this->ReadyToPost) {
            return 'Ready to Post';
        } elseif ($this->IsToBePrinted) {
            return 'Ready to Print';
        } else {
            return 'Draft';
        }
    }

    /**
     * Check if PO is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->DueDate && $this->DueDate->isPast() && !$this->Closed;
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get only open purchase orders.
     */
    public function scopeOpen($query)
    {
        return $query->where('Closed', false);
    }

    /**
     * Scope to get closed purchase orders.
     */
    public function scopeClosed($query)
    {
        return $query->where('Closed', true);
    }

    /**
     * Scope to get posted purchase orders.
     */
    public function scopePosted($query)
    {
        return $query->where('PostedToQB', true);
    }

    /**
     * Scope to get ready to post purchase orders.
     */
    public function scopeReadyToPost($query)
    {
        return $query->where('ReadyToPost', true)->where('PostedToQB', false);
    }

    /**
     * Scope to get overdue purchase orders.
     */
    public function scopeOverdue($query)
    {
        return $query->where('DueDate', '<', now())
                    ->where('Closed', false);
    }

    /**
     * Scope to search by vendor name.
     */
    public function scopeByVendor($query, $vendorName)
    {
        return $query->where('VendorName', 'LIKE', "%{$vendorName}%");
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('Date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by employee.
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where(function($q) use ($employeeId) {
            $q->where('Employee', $employeeId)
              ->orWhere('IssuedBy', $employeeId)
              ->orWhere('AuthorizedBy', $employeeId);
        });
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get the purchase order details (line items).
     */
    public function details()
    {
        return $this->hasMany(PODetail::class, 'PurchaseOrder', 'PurchaseOrder');
    }

    /**
     * Get the PO type.
     */
    public function poType()
    {
        return $this->belongsTo(POType::class, 'POType', 'ID');
    }

    /**
     * Get the employee who created this PO.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'Employee', 'EmployeeID');
    }

    /**
     * Get the employee who issued this PO.
     */
    public function issuedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'IssuedBy', 'EmployeeID');
    }

    /**
     * Get the employee who authorized this PO.
     */
    public function authorizedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'AuthorizedBy', 'EmployeeID');
    }

    /**
     * Get the related work order.
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'WorkOrderNum', 'ID');
    }

    /**
     * Get the related job.
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'JobNo', 'JobNo');
    }

    /**
     * Get the customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer', 'ID');
    }

    /**
     * Get purchase requests that reference this PO.
     */
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'PurchaseOrder', 'PurchaseOrder');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Add a line item to this purchase order.
     */
    public function addLineItem($itemData)
    {
        return $this->details()->create($itemData);
    }

    /**
     * Mark the PO as ready to post.
     */
    public function markReadyToPost()
    {
        $this->update(['ReadyToPost' => true]);
    }

    /**
     * Mark the PO as posted.
     */
    public function markAsPosted($qbTransId = null, $postedBy = null)
    {
        $this->update([
            'PostedToQB' => true,
            'QBTransID' => $qbTransId,
            'PostedBy' => $postedBy,
            'PostedDate' => now(),
        ]);
    }

    /**
     * Close the purchase order.
     */
    public function close()
    {
        $this->update(['Closed' => true]);
    }

    /**
     * Check if PO can be modified.
     */
    public function canBeModified()
    {
        return !$this->PostedToQB && !$this->Closed;
    }

    /**
     * Get the next available PO number.
     */
    public static function getNextPONumber()
    {
        $lastPO = static::max('PurchaseOrderNo');
        return ($lastPO ?: 0) + 1;
    }

    /**
     * Create a new purchase order with proper defaults.
     */
    public static function createNew($data)
    {
        $data['PurchaseOrderNo'] = $data['PurchaseOrderNo'] ?? static::getNextPONumber();
        $data['Date'] = $data['Date'] ?? now();
        $data['TaxRate'] = $data['TaxRate'] ?? 0.063; // Default from schema
        
        return static::create($data);
    }
}
