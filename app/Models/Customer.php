<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'CustTbl';

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
        'Customer',
        'FirstName',
        'LastName',
        'Salutation',
        'CustTyp',
        'CustomerStanding',
        'Comments',
        'Birthdate',
        'Nickname',
        'SpouseFirstName',
        'SpouseLastName',
        'SpouseSalutation',
        'SpouseComments',
        'SpouseBirthdate',
        'SpouseNickName',
        'Business',
        'BillingContact',
        'PrimaryContact',
        'BillingAddress',
        'BillingCity',
        'BillingState',
        'BillingZip',
        'BillingPhone',
        'BillingPhoneLabel',
        'BillingPhoneOwner',
        'BillingPhone2',
        'BillingPhone2Label',
        'BillingPhone2Owner',
        'BillingPhone3',
        'BillingPhone3Label',
        'BillingPhone3Owner',
        'BillingPhone4',
        'BillingPhone4Label',
        'BillingPhone4Owner',
        'BillingFax',
        'Lead',
        'MarketingSource',
        'Notes',
        'CustQuickBooksID',
        'Email',
        'Email2',
        'SendMail',
        'RecordCreatedBy',
        'Duplicate',
        'AgreementID',
        'Shaunas Notes',
        'AddressID',
        'Company',
        'BadDebt',
        'SMS1',
        'SMS1OptOut',
        'SMS2',
        'SMS2OptOut',
        'SMS3',
        'SMS3OptOut',
        'SMS4',
        'SMS4OptOut',
        'Domain',
        'EmergencyContactID',
        'point_of_contact',
        'poc_relationship',
        'SMSOptInDate',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'TimeCreated' => 'datetime',
        'last_modified' => 'datetime',
        'Birthdate' => 'datetime',
        'SpouseBirthdate' => 'datetime',
        'SMSOptInDate' => 'datetime',
        'Business' => 'boolean',
        'Lead' => 'boolean',
        'SendMail' => 'boolean',
        'Duplicate' => 'boolean',
        'SMS1OptOut' => 'boolean',
        'SMS2OptOut' => 'boolean',
        'SMS3OptOut' => 'boolean',
        'SMS4OptOut' => 'boolean',
        'BadDebt' => 'decimal:2',
        'PrimaryContact' => 'integer',
        'BillingPhoneOwner' => 'integer',
        'BillingPhone2Owner' => 'integer',
        'BillingPhone3Owner' => 'integer',
        'BillingPhone4Owner' => 'integer',
        'AgreementID' => 'integer',
        'AddressID' => 'integer',
        'Company' => 'integer',
        'EmergencyContactID' => 'integer',
        'point_of_contact' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->Business) {
            return $this->Customer ?: $this->BillingContact;
        }
        
        return trim($this->FirstName . ' ' . $this->LastName) ?: $this->Customer;
    }

    /**
     * Get the customer's display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->full_name;
    }

    /**
     * Get the spouse's full name.
     */
    public function getSpouseFullNameAttribute()
    {
        if ($this->SpouseFirstName || $this->SpouseLastName) {
            return trim($this->SpouseFirstName . ' ' . $this->SpouseLastName);
        }
        return null;
    }

    /**
     * Get the complete billing address.
     */
    public function getBillingAddressFullAttribute()
    {
        $addressParts = array_filter([
            $this->BillingAddress,
            $this->BillingCity,
            $this->BillingState,
            $this->BillingZip,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get the primary phone number.
     */
    public function getPrimaryPhoneAttribute()
    {
        return $this->BillingPhone ?: $this->BillingPhone2 ?: $this->BillingPhone3 ?: $this->BillingPhone4;
    }

    /**
     * Get all phone numbers with labels.
     */
    public function getAllPhonesAttribute()
    {
        $phones = [];
        
        if ($this->BillingPhone) {
            $phones[] = [
                'number' => $this->BillingPhone,
                'label' => $this->BillingPhoneLabel ?: 'Primary',
                'owner' => $this->BillingPhoneOwner,
            ];
        }
        
        if ($this->BillingPhone2) {
            $phones[] = [
                'number' => $this->BillingPhone2,
                'label' => $this->BillingPhone2Label ?: 'Secondary',
                'owner' => $this->BillingPhone2Owner,
            ];
        }
        
        if ($this->BillingPhone3) {
            $phones[] = [
                'number' => $this->BillingPhone3,
                'label' => $this->BillingPhone3Label ?: 'Phone 3',
                'owner' => $this->BillingPhone3Owner,
            ];
        }
        
        if ($this->BillingPhone4) {
            $phones[] = [
                'number' => $this->BillingPhone4,
                'label' => $this->BillingPhone4Label ?: 'Phone 4',
                'owner' => $this->BillingPhone4Owner,
            ];
        }

        return $phones;
    }

    /**
     * Get the primary email address.
     */
    public function getPrimaryEmailAttribute()
    {
        return $this->Email ?: $this->Email2;
    }

    /**
     * Get all SMS numbers that are opted in.
     */
    public function getOptedInSmsNumbersAttribute()
    {
        $smsNumbers = [];
        
        if ($this->SMS1 && !$this->SMS1OptOut) {
            $smsNumbers[] = $this->SMS1;
        }
        
        if ($this->SMS2 && !$this->SMS2OptOut) {
            $smsNumbers[] = $this->SMS2;
        }
        
        if ($this->SMS3 && !$this->SMS3OptOut) {
            $smsNumbers[] = $this->SMS3;
        }
        
        if ($this->SMS4 && !$this->SMS4OptOut) {
            $smsNumbers[] = $this->SMS4;
        }

        return $smsNumbers;
    }

    /**
     * Check if customer is a business.
     */
    public function getIsBusinessAttribute()
    {
        return $this->Business;
    }

    /**
     * Check if customer is a lead (prospect).
     */
    public function getIsLeadAttribute()
    {
        return $this->Lead;
    }

    /**
     * Check if customer has bad debt.
     */
    public function getHasBadDebtAttribute()
    {
        return $this->BadDebt > 0;
    }

    /**
     * Check if customer allows email marketing.
     */
    public function getAllowsEmailAttribute()
    {
        return $this->SendMail && $this->primary_email;
    }

    /**
     * Check if customer allows SMS marketing.
     */
    public function getAllowsSmsAttribute()
    {
        return count($this->opted_in_sms_numbers) > 0;
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get only business customers.
     */
    public function scopeBusiness($query)
    {
        return $query->where('Business', true);
    }

    /**
     * Scope to get only residential customers.
     */
    public function scopeResidential($query)
    {
        return $query->where('Business', false);
    }

    /**
     * Scope to get only leads (prospects).
     */
    public function scopeLeads($query)
    {
        return $query->where('Lead', true);
    }

    /**
     * Scope to get only active customers (not leads).
     */
    public function scopeActive($query)
    {
        return $query->where('Lead', false);
    }

    /**
     * Scope to get customers by company.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('Company', $companyId);
    }

    /**
     * Scope to get customers with bad debt.
     */
    public function scopeWithBadDebt($query)
    {
        return $query->where('BadDebt', '>', 0);
    }

    /**
     * Scope to get customers who allow email marketing.
     */
    public function scopeAllowsEmail($query)
    {
        return $query->where('SendMail', true)
                    ->where(function($q) {
                        $q->whereNotNull('Email')
                          ->orWhereNotNull('Email2');
                    });
    }

    /**
     * Scope to get customers who allow SMS marketing.
     */
    public function scopeAllowsSms($query)
    {
        return $query->where(function($q) {
            $q->where(function($subq) {
                $subq->whereNotNull('SMS1')->where('SMS1OptOut', false);
            })
            ->orWhere(function($subq) {
                $subq->whereNotNull('SMS2')->where('SMS2OptOut', false);
            })
            ->orWhere(function($subq) {
                $subq->whereNotNull('SMS3')->where('SMS3OptOut', false);
            })
            ->orWhere(function($subq) {
                $subq->whereNotNull('SMS4')->where('SMS4OptOut', false);
            });
        });
    }

    /**
     * Scope to search customers by name, email, or phone.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('FirstName', 'LIKE', "%{$searchTerm}%")
              ->orWhere('LastName', 'LIKE', "%{$searchTerm}%")
              ->orWhere('Customer', 'LIKE', "%{$searchTerm}%")
              ->orWhere('BillingContact', 'LIKE', "%{$searchTerm}%")
              ->orWhere('Email', 'LIKE', "%{$searchTerm}%")
              ->orWhere('Email2', 'LIKE', "%{$searchTerm}%")
              ->orWhere('BillingPhone', 'LIKE', "%{$searchTerm}%")
              ->orWhere('BillingPhone2', 'LIKE', "%{$searchTerm}%")
              ->orWhere('BillingPhone3', 'LIKE', "%{$searchTerm}%")
              ->orWhere('BillingPhone4', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Scope to filter by customer standing.
     */
    public function scopeByStanding($query, $standing)
    {
        return $query->where('CustomerStanding', $standing);
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get the company this customer belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'Company', 'ID');
    }

    /**
     * Get all work orders for this customer.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'CustID', 'ID');
    }

    /**
     * Get all jobs for this customer.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'CustID', 'ID');
    }

    /**
     * Get all sites/addresses for this customer.
     */
    public function sites()
    {
        return $this->hasMany(Site::class, 'BillTo', 'ID');
    }

    /**
     * Get the primary address/site.
     */
    public function primaryAddress()
    {
        return $this->belongsTo(Site::class, 'AddressID', 'ID');
    }

    /**
     * Get the emergency contact.
     */
    public function emergencyContact()
    {
        return $this->belongsTo(Customer::class, 'EmergencyContactID', 'ID');
    }

    /**
     * Get the point of contact.
     */
    public function pointOfContact()
    {
        return $this->belongsTo(Customer::class, 'point_of_contact', 'ID');
    }

    /**
     * Get all service tickets for this customer.
     */
    public function serviceTickets()
    {
        return $this->hasMany(ServiceTicket::class, 'CustomerID', 'ID');
    }

    /**
     * Get all agreement records for this customer.
     */
    public function agreements()
    {
        return $this->hasMany(AgreementTbl::class, 'CustID', 'ID');
    }

    /**
     * Get work orders where this customer is the contact.
     */
    public function contactWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'ContactID', 'ID');
    }

    /**
     * Get site manager records where this customer is the owner.
     */
    public function ownedSites()
    {
        return $this->hasMany(SiteManager::class, 'OwnerID', 'ID');
    }

    /**
     * Get site manager records where this customer is the manager.
     */
    public function managedSites()
    {
        return $this->hasMany(SiteManager::class, 'ManagerID', 'ID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Convert lead to customer.
     */
    public function convertToCustomer()
    {
        $this->update(['Lead' => false]);
    }

    /**
     * Add SMS opt-out for a specific number.
     */
    public function optOutSms($smsNumber)
    {
        if ($this->SMS1 === $smsNumber) {
            $this->update(['SMS1OptOut' => true]);
        } elseif ($this->SMS2 === $smsNumber) {
            $this->update(['SMS2OptOut' => true]);
        } elseif ($this->SMS3 === $smsNumber) {
            $this->update(['SMS3OptOut' => true]);
        } elseif ($this->SMS4 === $smsNumber) {
            $this->update(['SMS4OptOut' => true]);
        }
    }

    /**
     * Add SMS opt-in for all numbers.
     */
    public function optInSms()
    {
        $this->update([
            'SMS1OptOut' => false,
            'SMS2OptOut' => false,
            'SMS3OptOut' => false,
            'SMS4OptOut' => false,
            'SMSOptInDate' => now(),
        ]);
    }

    /**
     * Get customer's total contract value.
     */
    public function getTotalContractValueAttribute()
    {
        return $this->jobs()->sum('OriginalContractAmount');
    }

    /**
     * Get customer's outstanding balance.
     */
    public function getOutstandingBalanceAttribute()
    {
        return $this->jobs()->sum('AmountToCollect') + $this->BadDebt;
    }

    /**
     * Check if customer needs attention.
     */
    public function getNeedsAttentionAttribute()
    {
        return $this->has_bad_debt || 
               $this->workOrders()->overdue()->exists() ||
               $this->jobs()->needsAttention()->exists();
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Create a new customer with proper defaults.
     */
    public static function createNew($data, $companyId = null)
    {
        $data['Company'] = $data['Company'] ?? $companyId;
        $data['Lead'] = $data['Lead'] ?? true; // Default to lead
        $data['RecordCreatedBy'] = $data['RecordCreatedBy'] ?? auth()->user()?->full_name;
        
        return static::create($data);
    }

    /**
     * Get customer statistics for dashboard.
     */
    public static function getCustomerStats($companyId = null)
    {
        $query = $companyId ? static::byCompany($companyId) : static::query();
        
        return [
            'total_customers' => $query->active()->count(),
            'total_leads' => $query->leads()->count(),
            'business_customers' => $query->business()->count(),
            'residential_customers' => $query->residential()->count(),
            'customers_with_bad_debt' => $query->withBadDebt()->count(),
            'total_bad_debt' => $query->sum('BadDebt'),
            'email_subscribers' => $query->allowsEmail()->count(),
            'sms_subscribers' => $query->allowsSms()->count(),
        ];
    }

    /**
     * Get customers for marketing campaigns.
     */
    public static function getMarketingList($type = 'email', $companyId = null)
    {
        $query = $companyId ? static::byCompany($companyId) : static::query();
        
        if ($type === 'email') {
            return $query->allowsEmail()->get();
        } elseif ($type === 'sms') {
            return $query->allowsSms()->get();
        }
        
        return $query->get();
    }
}