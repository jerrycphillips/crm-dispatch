<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'Work Order';

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
        'Description',
        'ScheduledDate',
        'ScheduledTime',
        'Dispatched',
        'ServiceTech',
        'EnteredBy',
        'Status',
        'TargetTime',
        'TargetMinutes',
        'Category',
        'EndDate',
        'CustID',
        'SiteID',
        'CallerName',
        'AltPhone',
        'DateEntered',
        'TimeEntered',
        'MSource',
        'MCampaign',
        'Problem',
        'SpecialInstructions',
        'Note',
        'JobNo',
        'ServiceLocation',
        'ParentWorkOrder',
        'PartsNeeded',
        'TimeArrivedOnSite',
        'TimeDepartedFromSite',
        'estimatedDriveSeconds',
        'timeMarkedInProgress',
        'RequestedFeedback',
        'ReasonForNotRequestingFeedback',
        'company',
        'debrief_passcode',
        'under_observation',
        'ContactID',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'ScheduledDate' => 'datetime',
        'TargetTime' => 'datetime',
        'EndDate' => 'datetime',
        'DateEntered' => 'datetime',
        'TimeEntered' => 'datetime',
        'TimeCreated' => 'datetime',
        'last_modified' => 'datetime',
        'TimeArrivedOnSite' => 'datetime',
        'TimeDepartedFromSite' => 'datetime',
        'timeMarkedInProgress' => 'datetime',
        'Dispatched' => 'boolean',
        'PartsNeeded' => 'boolean',
        'RequestedFeedback' => 'boolean',
        'under_observation' => 'boolean',
        'TargetMinutes' => 'decimal:2', // NOTE: Despite the name, this field stores HOURS
        'ServiceTech' => 'integer',
        'Status' => 'integer',
        'Category' => 'integer',
        'CustID' => 'integer',
        'SiteID' => 'integer',
        'MSource' => 'integer',
        'MCampaign' => 'integer',
        'JobNo' => 'integer',
        'ParentWorkOrder' => 'integer',
        'estimatedDriveSeconds' => 'integer',
        'company' => 'integer',
        'ContactID' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the work order display number.
     */
    public function getDisplayNumberAttribute()
    {
        return 'WO-' . str_pad($this->ID, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the scheduled date and time combined.
     */
    public function getScheduledDateTimeAttribute()
    {
        if ($this->ScheduledDate && $this->ScheduledTime) {
            return $this->ScheduledDate->format('Y-m-d') . ' ' . $this->ScheduledTime;
        }
        return $this->ScheduledDate;
    }

    /**
     * Get the time spent on site in minutes.
     */
    public function getTimeOnSiteMinutesAttribute()
    {
        if ($this->TimeArrivedOnSite && $this->TimeDepartedFromSite) {
            return $this->TimeArrivedOnSite->diffInMinutes($this->TimeDepartedFromSite);
        }
        return null;
    }

    /**
     * Get the estimated drive time in minutes.
     */
    public function getEstimatedDriveMinutesAttribute()
    {
        return $this->estimatedDriveSeconds ? round($this->estimatedDriveSeconds / 60) : null;
    }

    /**
     * Get the target duration in hours (despite column name being TargetMinutes).
     * NOTE: The database column is named TargetMinutes but actually stores hours.
     */
    public function getTargetHoursAttribute()
    {
        return $this->TargetMinutes;
    }

    /**
     * Set the target duration in hours.
     * NOTE: This sets the TargetMinutes column which actually stores hours.
     */
    public function setTargetHoursAttribute($hours)
    {
        $this->attributes['TargetMinutes'] = $hours;
    }

    /**
     * Get the target duration in minutes (calculated from the hours stored in TargetMinutes).
     */
    public function getTargetMinutesCalculatedAttribute()
    {
        return $this->TargetMinutes ? $this->TargetMinutes * 60 : null;
    }

    /**
     * Get formatted target duration display.
     */
    public function getTargetDurationDisplayAttribute()
    {
        if (!$this->TargetMinutes) {
            return 'Not set';
        }

        $hours = floor($this->TargetMinutes);
        $minutes = ($this->TargetMinutes - $hours) * 60;

        if ($minutes > 0) {
            return sprintf('%dh %dm', $hours, round($minutes));
        }

        return sprintf('%dh', $hours);
    }

    /**
     * Check if work order is overdue.
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->ScheduledDate || $this->status?->isFinalStatus()) {
            return false;
        }

        $scheduledDateTime = $this->ScheduledDate;
        if ($this->ScheduledTime) {
            $scheduledDateTime = $scheduledDateTime->setTimeFromTimeString($this->ScheduledTime);
        }

        return $scheduledDateTime->isPast();
    }

    /**
     * Check if work order is in progress.
     */
    public function getIsInProgressAttribute()
    {
        return $this->timeMarkedInProgress && !$this->EndDate;
    }

    /**
     * Get the current phase of the work order.
     */
    public function getCurrentPhaseAttribute()
    {
        if ($this->EndDate) {
            return 'Completed';
        } elseif ($this->TimeDepartedFromSite) {
            return 'Departed Site';
        } elseif ($this->TimeArrivedOnSite) {
            return 'On Site';
        } elseif ($this->timeMarkedInProgress) {
            return 'In Progress';
        } elseif ($this->Dispatched) {
            return 'Dispatched';
        } else {
            return 'Scheduled';
        }
    }

    /**
     * Get priority level based on problem description.
     */
    public function getPriorityLevelAttribute()
    {
        $problem = strtoupper($this->Problem ?: '');
        
        if (str_contains($problem, 'EMERGENCY') || str_contains($problem, 'URGENT') || str_contains($problem, 'NO HEAT') || str_contains($problem, 'NO AC')) {
            return 1; // High priority
        } elseif (str_contains($problem, 'LEAK') || str_contains($problem, 'NOISE')) {
            return 2; // Medium priority
        } else {
            return 3; // Normal priority
        }
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get dispatched work orders.
     */
    public function scopeDispatched($query)
    {
        return $query->where('Dispatched', true);
    }

    /**
     * Scope to get non-dispatched work orders.
     */
    public function scopeNotDispatched($query)
    {
        return $query->where('Dispatched', false);
    }

    /**
     * Scope to get work orders in progress.
     */
    public function scopeInProgress($query)
    {
        return $query->whereNotNull('timeMarkedInProgress')
                    ->whereNull('EndDate');
    }

    /**
     * Scope to get completed work orders.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('EndDate');
    }

    /**
     * Scope to get overdue work orders.
     */
    public function scopeOverdue($query)
    {
        return $query->where('ScheduledDate', '<', now())
                    ->whereNull('EndDate');
    }

    /**
     * Scope to get work orders by technician.
     */
    public function scopeByTechnician($query, $techId)
    {
        return $query->where('ServiceTech', $techId);
    }

    /**
     * Scope to get work orders by customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('CustID', $customerId);
    }

    /**
     * Scope to get work orders scheduled for today.
     */
    public function scopeScheduledToday($query)
    {
        return $query->whereDate('ScheduledDate', today());
    }

    /**
     * Scope to get work orders scheduled for a date range.
     */
    public function scopeScheduledBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('ScheduledDate', [$startDate, $endDate]);
    }

    /**
     * Scope to get high priority work orders.
     */
    public function scopeHighPriority($query)
    {
        return $query->where(function($q) {
            $q->where('Problem', 'LIKE', '%emergency%')
              ->orWhere('Problem', 'LIKE', '%urgent%')
              ->orWhere('Problem', 'LIKE', '%no heat%')
              ->orWhere('Problem', 'LIKE', '%no ac%');
        });
    }

    /**
     * Scope to search work orders.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('Description', 'LIKE', "%{$searchTerm}%")
              ->orWhere('Problem', 'LIKE', "%{$searchTerm}%")
              ->orWhere('CallerName', 'LIKE', "%{$searchTerm}%")
              ->orWhere('ID', 'LIKE', "%{$searchTerm}%");
        });
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get the customer this work order belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustID', 'ID');
    }

    /**
     * Get the site/address for this work order.
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'SiteID', 'ID');
    }

    /**
     * Get the assigned service technician.
     */
    public function serviceTechnician()
    {
        return $this->belongsTo(Employee::class, 'ServiceTech', 'EmployeeID');
    }

    /**
     * Get the work order status.
     */
    public function status()
    {
        return $this->belongsTo(WOStatus::class, 'Status', 'CallStatusID');
    }

    /**
     * Get the related job.
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'JobNo', 'JobNo');
    }

    /**
     * Get the parent work order (if this is a child work order).
     */
    public function parentWorkOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'ParentWorkOrder', 'ID');
    }

    /**
     * Get child work orders.
     */
    public function childWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'ParentWorkOrder', 'ID');
    }

    /**
     * Get purchase orders related to this work order.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'WorkOrderNum', 'ID');
    }

    /**
     * Get time entries for this work order.
     */
    public function timeEntries()
    {
        return $this->hasMany(TblTime::class, 'TimeWO', 'ID');
    }

    /**
     * Get service tickets for this work order.
     */
    public function serviceTickets()
    {
        return $this->hasMany(ServiceTicket::class, 'WoID', 'ID');
    }

    /**
     * Get invoices for this work order.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'InvoiceWOID', 'ID');
    }

    /**
     * Get the contact person for this work order.
     */
    public function contact()
    {
        return $this->belongsTo(Customer::class, 'ContactID', 'ID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Dispatch the work order to a technician.
     */
    public function dispatch($technicianId = null, $scheduledDate = null, $scheduledTime = null)
    {
        $this->update([
            'Dispatched' => true,
            'ServiceTech' => $technicianId ?: $this->ServiceTech,
            'ScheduledDate' => $scheduledDate ?: $this->ScheduledDate,
            'ScheduledTime' => $scheduledTime ?: $this->ScheduledTime,
        ]);
    }

    /**
     * Mark as in progress.
     */
    public function markInProgress()
    {
        $this->update(['timeMarkedInProgress' => now()]);
    }

    /**
     * Mark arrival on site.
     */
    public function markArrivalOnSite()
    {
        $this->update(['TimeArrivedOnSite' => now()]);
    }

    /**
     * Mark departure from site.
     */
    public function markDepartureFromSite()
    {
        $this->update(['TimeDepartedFromSite' => now()]);
    }

    /**
     * Complete the work order.
     */
    public function complete($statusId = null)
    {
        $updateData = ['EndDate' => now()];
        
        if ($statusId) {
            $updateData['Status'] = $statusId;
        }
        
        $this->update($updateData);
    }

    /**
     * Check if technician can start this work order.
     */
    public function canStart()
    {
        return $this->Dispatched && !$this->timeMarkedInProgress && !$this->EndDate;
    }

    /**
     * Get estimated completion time.
     */
    public function getEstimatedCompletionAttribute()
    {
        if ($this->TargetTime) {
            return $this->TargetTime;
        }
        
        if ($this->TargetMinutes && $this->timeMarkedInProgress) {
            return $this->timeMarkedInProgress->addMinutes($this->TargetMinutes);
        }
        
        return null;
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Create a new work order with proper defaults.
     */
    public static function createNew($data)
    {
        $data['DateEntered'] = $data['DateEntered'] ?? now();
        $data['TimeEntered'] = $data['TimeEntered'] ?? now();
        $data['Status'] = $data['Status'] ?? WOStatus::getDefaultNewStatusId();
        
        return static::create($data);
    }

    /**
     * Get dispatch board data for today.
     */
    public static function getDispatchBoardData($date = null)
    {
        $date = $date ?: today();
        
        return static::with(['customer', 'site', 'serviceTechnician', 'status'])
                    ->whereDate('ScheduledDate', $date)
                    ->orderBy('ScheduledTime')
                    ->orderBy('priority_level')
                    ->get()
                    ->groupBy('ServiceTech');
    }

    /**
     * Get dashboard statistics.
     */
    public static function getDashboardStats()
    {
        return [
            'scheduled_today' => static::scheduledToday()->count(),
            'in_progress' => static::inProgress()->count(),
            'overdue' => static::overdue()->count(),
            'high_priority' => static::highPriority()->notDispatched()->count(),
            'completed_today' => static::completed()->whereDate('EndDate', today())->count(),
        ];
    }
}
