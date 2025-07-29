<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POType extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'POType';

    // Primary key field name
    protected $primaryKey = 'ID';

    // Standard auto-incrementing integer primary key
    public $incrementing = true;
    protected $keyType = 'int';

    // No timestamp columns in this table
    public $timestamps = false;

    // Mass assignable fields
    protected $fillable = [
        'POType',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the display name for this PO type.
     */
    public function getDisplayNameAttribute()
    {
        return $this->POType;
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to search PO types by name.
     */
    public function scopeSearchByName($query, $searchTerm)
    {
        return $query->where('POType', 'LIKE', "%{$searchTerm}%");
    }

    /**
     * Scope to order by PO type name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('POType', $direction);
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get all purchase requests that use this PO type.
     * Based on the foreign key relationship shown in your schema.
     */
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'POType', 'ID');
    }

    /**
     * Get all purchase orders that use this PO type.
     * Based on the foreign key relationship shown in your schema.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'POType', 'ID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if this PO type is currently in use.
     */
    public function isInUse()
    {
        return $this->purchaseRequests()->exists() || $this->purchaseOrders()->exists();
    }

    /**
     * Get the count of purchase requests using this type.
     */
    public function getPurchaseRequestCountAttribute()
    {
        return $this->purchaseRequests()->count();
    }

    /**
     * Get the count of purchase orders using this type.
     */
    public function getPurchaseOrderCountAttribute()
    {
        return $this->purchaseOrders()->count();
    }

    /**
     * Get the total count of all records using this PO type.
     */
    public function getTotalUsageCountAttribute()
    {
        return $this->purchase_request_count + $this->purchase_order_count;
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Get all PO types as a key-value array for dropdowns.
     */
    public static function getDropdownOptions()
    {
        return static::orderByName()->pluck('POType', 'ID')->toArray();
    }

    /**
     * Find a PO type by name.
     */
    public static function findByName($name)
    {
        return static::where('POType', $name)->first();
    }
}
