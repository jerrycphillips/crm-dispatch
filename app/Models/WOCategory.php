<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WOCategory extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'WOCategoryTbl';

    // Primary key field name
    protected $primaryKey = 'WOCatID';

    // Standard auto-incrementing integer primary key
    public $incrementing = true;
    protected $keyType = 'int';

    // Custom timestamp column (has default getdate())
    const CREATED_AT = null;
    const UPDATED_AT = 'last_modified';

    // Enable Laravel's timestamp handling
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'WOCategory',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'last_modified' => 'datetime',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the display name for this work order category.
     */
    public function getDisplayNameAttribute()
    {
        return $this->WOCategory;
    }

    /**
     * Get a formatted version of the category for display.
     */
    public function getFormattedCategoryAttribute()
    {
        return ucwords(strtolower($this->WOCategory));
    }

    /**
     * Get a short code/abbreviation for the category.
     */
    public function getShortCodeAttribute()
    {
        // Create abbreviation from category name
        $words = explode(' ', $this->WOCategory);
        if (count($words) > 1) {
            return strtoupper(substr($words[0], 0, 2) . substr($words[1], 0, 2));
        }
        return strtoupper(substr($this->WOCategory, 0, 4));
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to search WO categories by name.
     */
    public function scopeSearchByName($query, $searchTerm)
    {
        return $query->where('WOCategory', 'LIKE', "%{$searchTerm}%");
    }

    /**
     * Scope to order by category name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('WOCategory', $direction);
    }

    /**
     * Scope to get recently modified categories.
     */
    public function scopeRecentlyModified($query, $days = 30)
    {
        return $query->where('last_modified', '>=', now()->subDays($days));
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get all maintenance types that belong to this category.
     * Based on the foreign key relationship shown in your schema.
     */
    public function maintenanceTypes()
    {
        return $this->hasMany(MaintenanceType::class, 'WoCatID', 'WOCatID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if this category is currently in use by any maintenance types.
     */
    public function isInUse()
    {
        return $this->maintenanceTypes()->exists();
    }

    /**
     * Get the count of maintenance types in this category.
     */
    public function getMaintenanceTypeCountAttribute()
    {
        return $this->maintenanceTypes()->count();
    }

    /**
     * Get CSS class for this category (useful for UI styling).
     */
    public function getCategoryCssClassAttribute()
    {
        $category = strtoupper($this->WOCategory);
        
        // Common category types in HVAC/service industry
        if (str_contains($category, 'EMERGENCY') || str_contains($category, 'URGENT')) {
            return 'category-emergency';
        } elseif (str_contains($category, 'PREVENTIVE') || str_contains($category, 'MAINTENANCE')) {
            return 'category-preventive';
        } elseif (str_contains($category, 'REPAIR') || str_contains($category, 'FIX')) {
            return 'category-repair';
        } elseif (str_contains($category, 'INSTALL') || str_contains($category, 'NEW')) {
            return 'category-installation';
        } elseif (str_contains($category, 'INSPECTION') || str_contains($category, 'CHECK')) {
            return 'category-inspection';
        } else {
            return 'category-general';
        }
    }

    /**
     * Get priority level based on category type.
     */
    public function getPriorityLevelAttribute()
    {
        $category = strtoupper($this->WOCategory);
        
        if (str_contains($category, 'EMERGENCY') || str_contains($category, 'URGENT')) {
            return 1; // High priority
        } elseif (str_contains($category, 'REPAIR')) {
            return 2; // Medium priority
        } elseif (str_contains($category, 'PREVENTIVE') || str_contains($category, 'MAINTENANCE')) {
            return 3; // Normal priority
        } else {
            return 4; // Low priority
        }
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Get all WO categories as a key-value array for dropdowns.
     */
    public static function getDropdownOptions()
    {
        return static::orderByName()->pluck('WOCategory', 'WOCatID')->toArray();
    }

    /**
     * Find a WO category by name.
     */
    public static function findByName($name)
    {
        return static::where('WOCategory', $name)->first();
    }

    /**
     * Get categories grouped by priority for organized dropdowns.
     */
    public static function getGroupedByPriority()
    {
        return static::orderByName()
            ->get()
            ->groupBy('priority_level')
            ->map(function($categories) {
                return $categories->pluck('WOCategory', 'WOCatID');
            });
    }

    /**
     * Get emergency/urgent categories for quick access.
     */
    public static function getEmergencyCategories()
    {
        return static::where('WOCategory', 'LIKE', '%emergency%')
            ->orWhere('WOCategory', 'LIKE', '%urgent%')
            ->orderByName()
            ->get();
    }

    /**
     * Get statistics about work order categories.
     */
    public static function getCategoryStatistics()
    {
        return static::with('maintenanceTypes')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->WOCatID,
                    'name' => $category->WOCategory,
                    'maintenance_type_count' => $category->maintenance_type_count,
                    'priority_level' => $category->priority_level,
                    'css_class' => $category->category_css_class,
                    'short_code' => $category->short_code,
                ];
            })
            ->sortBy('priority_level');
    }

    /**
     * Create a new category with proper defaults.
     */
    public static function createCategory($name)
    {
        return static::create([
            'WOCategory' => $name,
            // last_modified will be set automatically by database default
        ]);
    }
}
