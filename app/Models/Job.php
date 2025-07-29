<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'Jobs';

    // Primary key field name
    protected $primaryKey = 'JobNo';

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
        'Job',
        'CustID',
        'SiteID',
        'Salesperson',
        'SalespersonID',
        'TermsofContract',
        'JobDescription',
        'JobType',
        'DateofInstallation',
        'PickUpCheck',
        'AmountToCollect',
        'ProposalSigned',
        'LoanPapersSigned',
        'LoanThrough',
        'FinTakenCareOf',
        'IssuersName',
        'JobNotes',
        'SpecialInstructions',
        'OriginalContractAmount',
        'LeadInstaller',
        'JobQuickBooksID',
        'SpiffTo',
        'SpiffAmount',
        'JobClosed',
        'Department',
        'LoanCompletedBy',
        'LoanCompletedTime',
        'IssuerID',
        'RebatesAvailable',
        'RebatesComplete',
        'RebatesCompletedBy',
        'RebatesCompletedTime',
        'UseTaxRate',
        'VisaMasterRate',
        'DiscoveredRate',
        'AmexRate',
        'JobTypeID',
        'created_by_app',
        'qb_setup_msg',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'TimeCreated' => 'datetime',
        'last_modified' => 'datetime',
        'DateofInstallation' => 'datetime',
        'LoanCompletedTime' => 'datetime',
        'RebatesCompletedTime' => 'datetime',
        'PickUpCheck' => 'boolean',
        'ProposalSigned' => 'boolean',
        'LoanPapersSigned' => 'boolean',
        'FinTakenCareOf' => 'boolean',
        'JobClosed' => 'boolean',
        'RebatesAvailable' => 'boolean',
        'RebatesComplete' => 'boolean',
        'created_by_app' => 'boolean',
        'AmountToCollect' => 'decimal:2',
        'OriginalContractAmount' => 'decimal:2',
        'SpiffAmount' => 'decimal:2',
        'UseTaxRate' => 'decimal:4',
        'VisaMasterRate' => 'decimal:4',
        'DiscoveredRate' => 'decimal:4',
        'AmexRate' => 'decimal:4',
        'CustID' => 'integer',
        'SiteID' => 'integer',
        'SalespersonID' => 'integer',
        'SpiffTo' => 'integer',
        'Department' => 'integer',
        'LoanCompletedBy' => 'integer',
        'IssuerID' => 'integer',
        'RebatesCompletedBy' => 'integer',
        'JobTypeID' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the job display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->Job ?: "Job #{$this->JobNo}";
    }

    /**
     * Get the formatted job number.
     */
    public function getFormattedJobNumberAttribute()
    {
        return 'J-' . str_pad($this->JobNo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the job status based on various flags.
     */
    public function getStatusAttribute()
    {
        if ($this->JobClosed) {
            return 'Closed';
        } elseif ($this->DateofInstallation && $this->DateofInstallation->isPast()) {
            return 'Installed';
        } elseif ($this->ProposalSigned) {
            return 'Sold';
        } else {
            return 'Proposal';
        }
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentageAttribute()
    {
        $steps = [
            'ProposalSigned' => 25,
            'LoanPapersSigned' => 15,
            'FinTakenCareOf' => 10,
            'DateofInstallation' => 30,
            'RebatesComplete' => 10,
            'JobClosed' => 10,
        ];

        $completed = 0;
        foreach ($steps as $step => $percentage) {
            if ($step === 'DateofInstallation') {
                if ($this->DateofInstallation && $this->DateofInstallation->isPast()) {
                    $completed += $percentage;
                }
            } else {
                if ($this->{$step}) {
                    $completed += $percentage;
                }
            }
        }

        return $completed;
    }

    /**
     * Check if job needs attention.
     */
    public function getNeedsAttentionAttribute()
    {
        if ($this->JobClosed) {
            return false;
        }

        // Check for various attention items
        if ($this->ProposalSigned && !$this->FinTakenCareOf) {
            return true; // Financing not handled
        }

        if ($this->DateofInstallation && $this->DateofInstallation->isPast() && !$this->JobClosed) {
            return true; // Installation date passed but job not closed
        }

        if ($this->RebatesAvailable && !$this->RebatesComplete) {
            return true; // Rebates available but not complete
        }

        return false;
    }

    /**
     * Get the contract balance (if any).
     */
    public function getContractBalanceAttribute()
    {
        return max(0, $this->OriginalContractAmount - $this->AmountToCollect);
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get only open jobs.
     */
    public function scopeOpen($query)
    {
        return $query->where('JobClosed', false);
    }

    /**
     * Scope to get closed jobs.
     */
    public function scopeClosed($query)
    {
        return $query->where('JobClosed', true);
    }

    /**
     * Scope to get sold jobs (proposal signed).
     */
    public function scopeSold($query)
    {
        return $query->where('ProposalSigned', true);
    }

    /**
     * Scope to get jobs needing attention.
     */
    public function scopeNeedsAttention($query)
    {
        return $query->where('JobClosed', false)
                    ->where(function($q) {
                        $q->where(function($subq) {
                            // Sold but financing not handled
                            $subq->where('ProposalSigned', true)
                                 ->where('FinTakenCareOf', false);
                        })
                        ->orWhere(function($subq) {
                            // Installation overdue
                            $subq->where('DateofInstallation', '<', now())
                                 ->whereNotNull('DateofInstallation');
                        })
                        ->orWhere(function($subq) {
                            // Rebates available but not complete
                            $subq->where('RebatesAvailable', true)
                                 ->where('RebatesComplete', false);
                        });
                    });
    }

    /**
     * Scope to get jobs by salesperson.
     */
    public function scopeBySalesperson($query, $salespersonId)
    {
        return $query->where('SalespersonID', $salespersonId);
    }

    /**
     * Scope to get jobs by customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('CustID', $customerId);
    }

    /**
     * Scope to get jobs scheduled for installation.
     */
    public function scopeScheduledForInstallation($query, $startDate = null, $endDate = null)
    {
        $query = $query->whereNotNull('DateofInstallation');
        
        if ($startDate) {
            $query->where('DateofInstallation', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('DateofInstallation', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Scope to search jobs by name or description.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('Job', 'LIKE', "%{$searchTerm}%")
              ->orWhere('JobDescription', 'LIKE', "%{$searchTerm}%")
              ->orWhere('JobNo', 'LIKE', "%{$searchTerm}%");
        });
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get the customer this job belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustID', 'ID');
    }

    /**
     * Get the site/address for this job.
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'SiteID', 'ID');
    }

    /**
     * Get the salesperson who sold this job.
     */
    public function salesperson()
    {
        return $this->belongsTo(Employee::class, 'SalespersonID', 'EmployeeID');
    }

    /**
     * Get all work orders for this job.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'JobNo', 'JobNo');
    }

    /**
     * Get all purchase orders for this job.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'JobNo', 'JobNo');
    }

    /**
     * Get all time entries for this job.
     */
    public function timeEntries()
    {
        return $this->hasMany(TblTime::class, 'TimeJobNo', 'JobNo');
    }

    /**
     * Get all MRO records for this job.
     */
    public function mroRecords()
    {
        return $this->hasMany(MRO::class, 'Job Number', 'JobNo');
    }

    /**
     * Get the employee who completed the loan.
     */
    public function loanCompletedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'LoanCompletedBy', 'EmployeeID');
    }

    /**
     * Get the employee who completed rebates.
     */
    public function rebatesCompletedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'RebatesCompletedBy', 'EmployeeID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Mark the job as sold (proposal signed).
     */
    public function markAsSold()
    {
        $this->update(['ProposalSigned' => true]);
    }

    /**
     * Mark financing as complete.
     */
    public function markFinancingComplete($completedBy = null)
    {
        $this->update([
            'FinTakenCareOf' => true,
            'LoanCompletedBy' => $completedBy,
            'LoanCompletedTime' => now(),
        ]);
    }

    /**
     * Mark rebates as complete.
     */
    public function markRebatesComplete($completedBy = null)
    {
        $this->update([
            'RebatesComplete' => true,
            'RebatesCompletedBy' => $completedBy,
            'RebatesCompletedTime' => now(),
        ]);
    }

    /**
     * Close the job.
     */
    public function close()
    {
        $this->update(['JobClosed' => true]);
    }

    /**
     * Reopen the job.
     */
    public function reopen()
    {
        $this->update(['JobClosed' => false]);
    }

    /**
     * Schedule installation.
     */
    public function scheduleInstallation($installationDate)
    {
        $this->update(['DateofInstallation' => $installationDate]);
    }

    /**
     * Get the total contract value including tax.
     */
    public function getTotalContractValueAttribute()
    {
        $base = $this->OriginalContractAmount;
        return $base + ($base * $this->UseTaxRate);
    }

    /**
     * Calculate commission based on contract amount.
     */
    public function calculateCommission($commissionRate = 0.05)
    {
        return $this->OriginalContractAmount * $commissionRate;
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Get the next available job number.
     */
    public static function getNextJobNumber()
    {
        $lastJob = static::max('JobNo');
        return ($lastJob ?: 0) + 1;
    }

    /**
     * Create a new job with proper defaults.
     */
    public static function createNew($data)
    {
        $data['JobNo'] = $data['JobNo'] ?? static::getNextJobNumber();
        $data['UseTaxRate'] = $data['UseTaxRate'] ?? 0.0775; // Default from schema
        
        return static::create($data);
    }

    /**
     * Get jobs dashboard data.
     */
    public static function getDashboardStats()
    {
        return [
            'total_open' => static::open()->count(),
            'total_sold' => static::sold()->count(),
            'needs_attention' => static::needsAttention()->count(),
            'scheduled_this_week' => static::scheduledForInstallation(
                now()->startOfWeek(),
                now()->endOfWeek()
            )->count(),
            'total_contract_value' => static::open()->sum('OriginalContractAmount'),
        ];
    }
}
