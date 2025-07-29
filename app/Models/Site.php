<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'AddressTbl';

    // Primary key field name
    protected $primaryKey = 'ID';

    // Standard auto-incrementing integer primary key
    public $incrementing = true;
    protected $keyType = 'int';

    // Custom timestamp columns
    const CREATED_AT = 'TimeCreated';
    const UPDATED_AT = 'last_modified';

    // Enable Laravel's timestamp handling
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'BillTo',
        'Address',
        'City',
        'State',
        'Zip',
        'lat',
        'lng',
        'Notes',
        'RecordCreatedBy',
        'Coordinate',
        'Equipment',
        'Duplicate',
        'CRRT',
        'RooftopGPS',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'TimeCreated' => 'datetime',
        'last_modified' => 'datetime',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'Duplicate' => 'boolean',
        'RooftopGPS' => 'boolean',
        'BillTo' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the complete formatted address.
     */
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->Address,
            $this->City,
            $this->State,
            $this->Zip,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get the address for mapping purposes.
     */
    public function getMapAddressAttribute()
    {
        return $this->full_address;
    }

    /**
     * Get coordinates as an array.
     */
    public function getCoordinatesAttribute()
    {
        if ($this->lat && $this->lng) {
            return [
                'lat' => (float) $this->lat,
                'lng' => (float) $this->lng,
            ];
        }
        return null;
    }

    /**
     * Check if site has GPS coordinates.
     */
    public function getHasCoordinatesAttribute()
    {
        return $this->lat && $this->lng;
    }

    /**
     * Check if GPS coordinates are from rooftop.
     */
    public function getIsRooftopGpsAttribute()
    {
        return $this->RooftopGPS;
    }

    /**
     * Get the city and state combination.
     */
    public function getCityStateAttribute()
    {
        $parts = array_filter([$this->City, $this->State]);
        return implode(', ', $parts);
    }

    /**
     * Get state and zip combination.
     */
    public function getStateZipAttribute()
    {
        $parts = array_filter([$this->State, $this->Zip]);
        return implode(' ', $parts);
    }

    /**
     * Check if this is the primary address for the customer.
     */
    public function getIsPrimaryAttribute()
    {
        if (!$this->customer) {
            return false;
        }
        return $this->customer->AddressID === $this->ID;
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get sites by customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('BillTo', $customerId);
    }

    /**
     * Scope to get sites with GPS coordinates.
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('lat')
                    ->whereNotNull('lng');
    }

    /**
     * Scope to get sites without GPS coordinates.
     */
    public function scopeWithoutCoordinates($query)
    {
        return $query->where(function($q) {
            $q->whereNull('lat')
              ->orWhereNull('lng');
        });
    }

    /**
     * Scope to get sites with rooftop GPS accuracy.
     */
    public function scopeRooftopGps($query)
    {
        return $query->where('RooftopGPS', true);
    }

    /**
     * Scope to get sites by city.
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('City', 'LIKE', "%{$city}%");
    }

    /**
     * Scope to get sites by state.
     */
    public function scopeByState($query, $state)
    {
        return $query->where('State', $state);
    }

    /**
     * Scope to get sites by zip code.
     */
    public function scopeByZip($query, $zip)
    {
        return $query->where('Zip', 'LIKE', "%{$zip}%");
    }

    /**
     * Scope to search sites by address components.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('Address', 'LIKE', "%{$searchTerm}%")
              ->orWhere('City', 'LIKE', "%{$searchTerm}%")
              ->orWhere('State', 'LIKE', "%{$searchTerm}%")
              ->orWhere('Zip', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Scope to get sites within a radius of coordinates.
     */
    public function scopeWithinRadius($query, $lat, $lng, $radiusMiles = 10)
    {
        // Using Haversine formula for distance calculation
        $query->selectRaw("
            *, 
            (3959 * acos(
                cos(radians(?)) * 
                cos(radians(lat)) * 
                cos(radians(lng) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(lat))
            )) AS distance
        ", [$lat, $lng, $lat])
        ->havingRaw('distance <= ?', [$radiusMiles])
        ->orderBy('distance');

        return $query;
    }

    /**
     * Scope to get duplicate sites.
     */
    public function scopeDuplicates($query)
    {
        return $query->where('Duplicate', true);
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get the customer this site belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'BillTo', 'ID');
    }

    /**
     * Get all work orders for this site.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'SiteID', 'ID');
    }

    /**
     * Get all jobs for this site.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'SiteID', 'ID');
    }

    /**
     * Get all job parts for this site.
     */
    public function jobParts()
    {
        return $this->hasMany(JobParts::class, 'SiteID', 'ID');
    }

    /**
     * Get all call log entries for this site.
     */
    public function callLogs()
    {
        return $this->hasMany(CallLogTbl::class, 'siteid', 'ID');
    }

    /**
     * Get all service tickets for this site.
     */
    public function serviceTickets()
    {
        return $this->hasMany(ServiceTicket::class, 'SiteID', 'ID');
    }

    /**
     * Get site manager records for this site.
     */
    public function siteManagers()
    {
        return $this->hasMany(SiteManager::class, 'AddressID', 'ID');
    }

    /**
     * Get customers who use this as their primary address.
     */
    public function primaryForCustomers()
    {
        return $this->hasMany(Customer::class, 'AddressID', 'ID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Geocode the address and update coordinates.
     */
    public function geocode($forceUpdate = false)
    {
        if (!$forceUpdate && $this->has_coordinates) {
            return $this->coordinates;
        }

        // This would integrate with your preferred geocoding service
        // Example: Google Maps, Mapbox, etc.
        $address = $this->full_address;
        
        // Placeholder for geocoding logic
        // You would implement actual geocoding service integration here
        $coordinates = $this->performGeocode($address);
        
        if ($coordinates) {
            $this->update([
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
                'RooftopGPS' => $coordinates['accuracy'] === 'ROOFTOP',
            ]);
        }

        return $coordinates;
    }

    /**
     * Calculate distance to another site.
     */
    public function distanceTo(Site $otherSite)
    {
        if (!$this->has_coordinates || !$otherSite->has_coordinates) {
            return null;
        }

        return $this->calculateDistance(
            $this->lat, 
            $this->lng, 
            $otherSite->lat, 
            $otherSite->lng
        );
    }

    /**
     * Get nearby sites within radius.
     */
    public function nearbySites($radiusMiles = 5)
    {
        if (!$this->has_coordinates) {
            return collect();
        }

        return static::withinRadius($this->lat, $this->lng, $radiusMiles)
                    ->where('ID', '!=', $this->ID)
                    ->get();
    }

    /**
     * Mark as duplicate.
     */
    public function markAsDuplicate()
    {
        $this->update(['Duplicate' => true]);
    }

    /**
     * Unmark as duplicate.
     */
    public function unmarkAsDuplicate()
    {
        $this->update(['Duplicate' => false]);
    }

    /**
     * Set as primary address for customer.
     */
    public function setAsPrimary()
    {
        if ($this->customer) {
            $this->customer->update(['AddressID' => $this->ID]);
        }
    }

    /**
     * Get all active work orders for this site.
     */
    public function getActiveWorkOrdersAttribute()
    {
        return $this->workOrders()->active()->get();
    }

    /**
     * Get all open jobs for this site.
     */
    public function getOpenJobsAttribute()
    {
        return $this->jobs()->open()->get();
    }

    /**
     * Check if site needs service attention.
     */
    public function getNeedsAttentionAttribute()
    {
        return $this->workOrders()->overdue()->exists() ||
               $this->jobs()->needsAttention()->exists();
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Perform actual geocoding (placeholder).
     */
    private function performGeocode($address)
    {
        // Implement your geocoding service integration here
        // Return format: ['lat' => float, 'lng' => float, 'accuracy' => string]
        return null;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 3959; // miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Create a new site with proper defaults.
     */
    public static function createNew($data, $customerId = null)
    {
        $data['BillTo'] = $data['BillTo'] ?? $customerId;
        $data['RecordCreatedBy'] = $data['RecordCreatedBy'] ?? auth()->user()?->full_name;
        
        $site = static::create($data);
        
        // Auto-geocode if address is provided
        if ($site->Address && $site->City) {
            $site->geocode();
        }
        
        return $site;
    }

    /**
     * Find or create a site by address.
     */
    public static function findOrCreateByAddress($address, $city, $state, $zip, $customerId = null)
    {
        $site = static::where('Address', $address)
                     ->where('City', $city)
                     ->where('State', $state)
                     ->where('Zip', $zip)
                     ->first();

        if (!$site) {
            $site = static::createNew([
                'Address' => $address,
                'City' => $city,
                'State' => $state,
                'Zip' => $zip,
            ], $customerId);
        }

        return $site;
    }

    /**
     * Get sites needing geocoding.
     */
    public static function needingGeocode()
    {
        return static::withoutCoordinates()
                    ->whereNotNull('Address')
                    ->whereNotNull('City')
                    ->get();
    }

    /**
     * Get site statistics for dashboard.
     */
    public static function getSiteStats($customerId = null)
    {
        $query = $customerId ? static::byCustomer($customerId) : static::query();
        
        return [
            'total_sites' => $query->count(),
            'with_coordinates' => $query->withCoordinates()->count(),
            'rooftop_accuracy' => $query->rooftopGps()->count(),
            'duplicate_sites' => $query->duplicates()->count(),
            'sites_needing_geocoding' => $query->withoutCoordinates()
                                              ->whereNotNull('Address')
                                              ->whereNotNull('City')
                                              ->count(),
        ];
    }

    /**
     * Bulk geocode sites.
     */
    public static function bulkGeocode($limit = 100)
    {
        $sites = static::needingGeocode()->limit($limit)->get();
        $geocoded = 0;

        foreach ($sites as $site) {
            if ($site->geocode()) {
                $geocoded++;
            }
            
            // Rate limiting for geocoding APIs
            usleep(100000); // 100ms delay
        }

        return $geocoded;
    }
}