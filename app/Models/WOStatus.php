<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WOStatus extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'WOStatus';

    // Primary key field name
    protected $primaryKey = 'CallStatusID';

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
        'CallStatus',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'last_modified' => 'datetime',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the display name for this work order status.
     */
    public function getDisplayNameAttribute()
    {
        return $this->CallStatus;
    }

    /**
     * Get a formatted version of the status for display.
     */
    public function getFormattedStatusAttribute()
    {
        return ucwords(strtolower($this->CallStatus));
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to search WO statuses by name.
     */
    public function scopeSearchByName($query, $searchTerm)
    {
        return $query->where('CallStatus', 'LIKE', "%{$searchTerm}%");
    }

    /**
     * Scope to order by status name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('CallStatus', $direction);
    }

    /**
     * Scope to get active/open statuses (customize based on your business logic).
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('CallStatus', ['COMPLETED', 'CANCELLED', 'CLOSED']);
    }

    /**
     * Scope to get completed statuses.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('CallStatus', ['COMPLETED', 'FINISHED', 'DONE', 'CLOSED']);
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get all work orders that have this status.
     * Based on the foreign key relationship shown in your schema.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'Status', 'CallStatusID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if this status is currently in use by any work orders.
     */
    public function isInUse()
    {
        return $this->workOrders()->exists();
    }

    /**
     * Get the count of work orders with this status.
     */
    public function getWorkOrderCountAttribute()
    {
        return $this->workOrders()->count();
    }

    /**
     * Check if this is a "final" status (work order is complete).
     */
    public function isFinalStatus()
    {
        $finalStatuses = ['COMPLETED', 'CANCELLED', 'CLOSED', 'FINISHED', 'DONE'];
        return in_array(strtoupper($this->CallStatus), $finalStatuses);
    }

    /**
     * Check if this is an "active" status (work order is still in progress).
     */
    public function isActiveStatus()
    {
        return !$this->isFinalStatus();
    }

    /**
     * Get the CSS class for this status (useful for UI styling).
     */
    public function getStatusCssClassAttribute()
    {
        $status = strtoupper($this->CallStatus);
        
        switch ($status) {
            case 'COMPLETED':
            case 'FINISHED':
            case 'DONE':
            case 'CLOSED':
                return 'status-completed';
            case 'IN PROGRESS':
            case 'STARTED':
            case 'WORKING':
                return 'status-in-progress';
            case 'PENDING':
            case 'WAITING':
                return 'status-pending';
            case 'CANCELLED':
            case 'CANCELED':
                return 'status-cancelled';
            case 'NEW':
            case 'OPEN':
                return 'status-new';
            default:
                return 'status-unknown';
        }
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Get all WO statuses as a key-value array for dropdowns.
     */
    public static function getDropdownOptions()
    {
        return static::orderByName()->pluck('CallStatus', 'CallStatusID')->toArray();
    }

    /**
     * Find a WO status by name.
     */
    public static function findByName($name)
    {
        return static::where('CallStatus', $name)->first();
    }

    /**
     * Get all active status options for dropdowns.
     */
    public static function getActiveStatusOptions()
    {
        return static::active()->orderByName()->pluck('CallStatus', 'CallStatusID')->toArray();
    }

    /**
     * Get the default "new" status ID.
     */
    public static function getDefaultNewStatusId()
    {
        $newStatus = static::whereIn('CallStatus', ['NEW', 'OPEN', 'PENDING'])->first();
        return $newStatus ? $newStatus->CallStatusID : null;
    }

    /**
     * Get statistics about work order statuses.
     */
    public static function getStatusStatistics()
    {
        return static::with('workOrders')
            ->get()
            ->map(function($status) {
                return [
                    'id' => $status->CallStatusID,
                    'name' => $status->CallStatus,
                    'count' => $status->work_order_count,
                    'is_final' => $status->isFinalStatus(),
                    'css_class' => $status->status_css_class,
                ];
            });
    }
}
