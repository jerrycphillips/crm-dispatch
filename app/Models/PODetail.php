<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PODetail extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'PODetails';

    // Primary key field name
    protected $primaryKey = 'PODetailID';

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
        'PurchaseOrder',
        'ItemName',
        'QBListID',
        'Description',
        'Quantity',
        'UnitCost',
        'UOM',
        'QBTransID',
        'QtyOH',
        'QtyOO',
        'Amount',
        'MRODetailsID',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'last_modified' => 'datetime',
        'Quantity' => 'decimal:2',
        'UnitCost' => 'decimal:2',
        'QtyOH' => 'decimal:2',
        'QtyOO' => 'decimal:2',
        'Amount' => 'decimal:2',
        'PurchaseOrder' => 'integer',
        'MRODetailsID' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the calculated line total (Quantity * UnitCost).
     */
    public function getLineTotalAttribute()
    {
        return $this->Quantity * $this->UnitCost;
    }

    /**
     * Get the display description (use Description if available, otherwise ItemName).
     */
    public function getDisplayDescriptionAttribute()
    {
        return $this->Description ?: $this->ItemName;
    }

    /**
     * Get formatted unit cost.
     */
    public function getFormattedUnitCostAttribute()
    {
        return '$' . number_format($this->UnitCost, 2);
    }

    /**
     * Get formatted line total.
     */
    public function getFormattedLineTotalAttribute()
    {
        return '$' . number_format($this->line_total, 2);
    }

    /**
     * Get the unit of measure display.
     */
    public function getUomDisplayAttribute()
    {
        return $this->UOM ?: 'EA';
    }

    /**
     * Check if item is backordered.
     */
    public function getIsBackorderedAttribute()
    {
        return $this->QtyOH < $this->Quantity;
    }

    /**
     * Get the quantity short.
     */
    public function getQuantityShortAttribute()
    {
        return max(0, $this->Quantity - $this->QtyOH);
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get items that are backordered.
     */
    public function scopeBackordered($query)
    {
        return $query->whereColumn('QtyOH', '<', 'Quantity');
    }

    /**
     * Scope to get items by purchase order.
     */
    public function scopeForPurchaseOrder($query, $purchaseOrderId)
    {
        return $query->where('PurchaseOrder', $purchaseOrderId);
    }

    /**
     * Scope to search by item name.
     */
    public function scopeSearchByItem($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('ItemName', 'LIKE', "%{$searchTerm}%")
              ->orWhere('Description', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Scope to order by line number/ID.
     */
    public function scopeOrderByLine($query, $direction = 'asc')
    {
        return $query->orderBy('PODetailID', $direction);
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get the parent purchase order.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'PurchaseOrder', 'PurchaseOrder');
    }

    /**
     * Get the related MRO detail if applicable.
     */
    public function mroDetail()
    {
        return $this->belongsTo(MRODetail::class, 'MRODetailsID', 'Primary');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Recalculate the Amount field based on Quantity and UnitCost.
     */
    public function recalculateAmount()
    {
        $this->Amount = $this->line_total;
        $this->save();
        return $this;
    }

    /**
     * Update quantity and recalculate amount.
     */
    public function updateQuantity($newQuantity)
    {
        $this->Quantity = $newQuantity;
        $this->recalculateAmount();
        return $this;
    }

    /**
     * Update unit cost and recalculate amount.
     */
    public function updateUnitCost($newUnitCost)
    {
        $this->UnitCost = $newUnitCost;
        $this->recalculateAmount();
        return $this;
    }

    /**
     * Check if this line item has sufficient inventory.
     */
    public function hasSufficientInventory()
    {
        return $this->QtyOH >= $this->Quantity;
    }

    /**
     * Get inventory status text.
     */
    public function getInventoryStatusAttribute()
    {
        if ($this->hasSufficientInventory()) {
            return 'In Stock';
        } elseif ($this->QtyOH > 0) {
            return 'Partial Stock';
        } else {
            return 'Out of Stock';
        }
    }

    /**
     * Get CSS class for inventory status.
     */
    public function getInventoryStatusCssAttribute()
    {
        switch ($this->inventory_status) {
            case 'In Stock':
                return 'status-in-stock';
            case 'Partial Stock':
                return 'status-partial-stock';
            case 'Out of Stock':
                return 'status-out-of-stock';
            default:
                return 'status-unknown';
        }
    }

    // ========================================
    // MODEL EVENTS
    // ========================================

    /**
     * Boot method to set up model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate Amount when creating/updating
        static::saving(function ($model) {
            if ($model->isDirty(['Quantity', 'UnitCost']) || !$model->Amount) {
                $model->Amount = $model->Quantity * $model->UnitCost;
            }
        });
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Create a new line item with proper calculations.
     */
    public static function createLineItem($purchaseOrderId, $itemData)
    {
        $itemData['PurchaseOrder'] = $purchaseOrderId;
        
        // Ensure Amount is calculated
        if (!isset($itemData['Amount']) && isset($itemData['Quantity'], $itemData['UnitCost'])) {
            $itemData['Amount'] = $itemData['Quantity'] * $itemData['UnitCost'];
        }

        return static::create($itemData);
    }

    /**
     * Get summary statistics for a purchase order.
     */
    public static function getSummaryForPO($purchaseOrderId)
    {
        $details = static::where('PurchaseOrder', $purchaseOrderId)->get();
        
        return [
            'total_lines' => $details->count(),
            'total_quantity' => $details->sum('Quantity'),
            'total_amount' => $details->sum('Amount'),
            'backordered_lines' => $details->where('is_backordered', true)->count(),
            'total_backordered_qty' => $details->sum('quantity_short'),
        ];
    }

    /**
     * Get items that need to be reordered based on low inventory.
     */
    public static function getItemsNeedingReorder($threshold = 5)
    {
        return static::where('QtyOH', '<', $threshold)
                    ->where('QtyOH', '>', 0)
                    ->get()
                    ->groupBy('ItemName');
    }
}
