<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'Company';

    // Primary key field name
    protected $primaryKey = 'ID';

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
        'Company',
        'CustTypeRefListID',
        'DispatchEmail',
        'MaintenanceAgreementEmail',
        'OtherEmail',
        'Phone',
        'Address',
        'CSZ',
        'SMSCarrier',
        'WebsiteEmail',
        'domain',
        'dkim_file',
        'unique_spokesperson',
        'EmailDomain',
        'CustID',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'last_modified' => 'datetime',
        'unique_spokesperson' => 'integer',
        'CustID' => 'integer',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the company display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->Company;
    }

    /**
     * Get the full address formatted.
     */
    public function getFormattedAddressAttribute()
    {
        $addressParts = array_filter([
            $this->Address,
            $this->CSZ, // City, State, Zip
        ]);

        return implode("\n", $addressParts);
    }

    /**
     * Get the primary email for the company.
     */
    public function getPrimaryEmailAttribute()
    {
        return $this->DispatchEmail ?: $this->WebsiteEmail ?: $this->OtherEmail;
    }

    /**
     * Get all email addresses for the company.
     */
    public function getAllEmailsAttribute()
    {
        return array_filter([
            'dispatch' => $this->DispatchEmail,
            'maintenance' => $this->MaintenanceAgreementEmail,
            'website' => $this->WebsiteEmail,
            'other' => $this->OtherEmail,
        ]);
    }

    /**
     * Get the company's domain for email purposes.
     */
    public function getEmailDomainDisplayAttribute()
    {
        return $this->EmailDomain ?: $this->domain;
    }

    /**
     * Check if company has DKIM configured.
     */
    public function getHasDkimAttribute()
    {
        return !empty($this->dkim_file);
    }

    /**
     * Get the company phone formatted.
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->Phone);
        
        if (strlen($phone) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($phone, 0, 3),
                substr($phone, 3, 3),
                substr($phone, 6, 4)
            );
        }
        
        return $this->Phone;
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to search companies by name.
     */
    public function scopeSearchByName($query, $searchTerm)
    {
        return $query->where('Company', 'LIKE', "%{$searchTerm}%");
    }

    /**
     * Scope to get companies with dispatch email configured.
     */
    public function scopeWithDispatchEmail($query)
    {
        return $query->whereNotNull('DispatchEmail')
                    ->where('DispatchEmail', '!=', '');
    }

    /**
     * Scope to get companies with DKIM configured.
     */
    public function scopeWithDkim($query)
    {
        return $query->whereNotNull('dkim_file')
                    ->where('dkim_file', '!=', '');
    }

    /**
     * Scope to order by company name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('Company', $direction);
    }

    /**
     * Scope to get companies with active customers.
     */
    public function scopeWithActiveCustomers($query)
    {
        return $query->whereHas('customers');
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get all customers associated with this company.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'Company', 'ID');
    }

    /**
     * Get all employees associated with this company.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'Company', 'ID');
    }

    /**
     * Get the unique spokesperson for this company.
     */
    public function spokesperson()
    {
        return $this->belongsTo(Employee::class, 'unique_spokesperson', 'EmployeeID');
    }

    /**
     * Get all marketing campaigns for this company.
     */
    public function marketingCampaigns()
    {
        return $this->hasMany(MCampaign::class, 'Company', 'ID');
    }

    /**
     * Get all marketing assets for this company.
     */
    public function marketingAssets()
    {
        return $this->hasMany(MAsset::class, 'CompanyID', 'ID');
    }

    /**
     * Get Avaya post dial configurations for this company.
     */
    public function avayaPostDials()
    {
        return $this->hasMany(AvayaPostDial::class, 'CompanyID', 'ID');
    }

    /**
     * Get the customer record if this company is also a customer.
     */
    public function customerRecord()
    {
        return $this->belongsTo(Customer::class, 'CustID', 'ID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get the total number of customers for this company.
     */
    public function getCustomerCountAttribute()
    {
        return $this->customers()->count();
    }

    /**
     * Get the total number of employees for this company.
     */
    public function getEmployeeCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * Check if this company has any active operations.
     */
    public function hasActiveOperations()
    {
        return $this->customers()->exists() || $this->employees()->exists();
    }

    /**
     * Get the company's branding configuration.
     */
    public function getBrandingConfigAttribute()
    {
        return [
            'company_name' => $this->Company,
            'phone' => $this->formatted_phone,
            'email' => $this->primary_email,
            'address' => $this->formatted_address,
            'domain' => $this->email_domain_display,
            'has_dkim' => $this->has_dkim,
        ];
    }

    /**
     * Get email configuration for this company.
     */
    public function getEmailConfigAttribute()
    {
        return [
            'dispatch_email' => $this->DispatchEmail,
            'maintenance_email' => $this->MaintenanceAgreementEmail,
            'website_email' => $this->WebsiteEmail,
            'other_email' => $this->OtherEmail,
            'domain' => $this->EmailDomain ?: $this->domain,
            'dkim_file' => $this->dkim_file,
            'sms_carrier' => $this->SMSCarrier,
        ];
    }

    /**
     * Send email using company's configuration.
     */
    public function sendEmail($to, $subject, $content, $emailType = 'dispatch')
    {
        $fromEmail = match($emailType) {
            'maintenance' => $this->MaintenanceAgreementEmail,
            'website' => $this->WebsiteEmail,
            'other' => $this->OtherEmail,
            default => $this->DispatchEmail,
        };

        if (!$fromEmail) {
            $fromEmail = $this->primary_email;
        }

        // This would integrate with your email service
        // Implementation depends on your email provider (SendGrid, SES, etc.)
        return [
            'from' => $fromEmail,
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'company' => $this->Company,
            'dkim_enabled' => $this->has_dkim,
        ];
    }

    /**
     * Get work orders for all customers of this company.
     */
    public function getAllWorkOrders()
    {
        return WorkOrder::whereHas('customer', function($query) {
            $query->where('Company', $this->ID);
        });
    }

    /**
     * Get jobs for all customers of this company.
     */
    public function getAllJobs()
    {
        return Job::whereHas('customer', function($query) {
            $query->where('Company', $this->ID);
        });
    }

    // ========================================
    // STATIC HELPER METHODS
    // ========================================

    /**
     * Get all companies as dropdown options.
     */
    public static function getDropdownOptions()
    {
        return static::orderByName()->pluck('Company', 'ID')->toArray();
    }

    /**
     * Find a company by name.
     */
    public static function findByName($name)
    {
        return static::where('Company', $name)->first();
    }

    /**
     * Get companies with their customer counts.
     */
    public static function getCompanyStats()
    {
        return static::withCount(['customers', 'employees'])
                    ->orderByName()
                    ->get()
                    ->map(function($company) {
                        return [
                            'id' => $company->ID,
                            'name' => $company->Company,
                            'customer_count' => $company->customers_count,
                            'employee_count' => $company->employees_count,
                            'primary_email' => $company->primary_email,
                            'phone' => $company->formatted_phone,
                            'has_active_operations' => $company->hasActiveOperations(),
                        ];
                    });
    }

    /**
     * Create a new company with proper defaults.
     */
    public static function createNew($data)
    {
        // Set up basic email domain if not provided
        if (!isset($data['EmailDomain']) && isset($data['Company'])) {
            $domain = strtolower(str_replace(' ', '', $data['Company'])) . '.com';
            $data['EmailDomain'] = $domain;
        }

        return static::create($data);
    }

    /**
     * Get multi-tenant context for current company.
     */
    public static function getMultiTenantContext($companyId)
    {
        $company = static::with(['spokesperson'])->find($companyId);
        
        if (!$company) {
            return null;
        }

        return [
            'company_id' => $company->ID,
            'company_name' => $company->Company,
            'branding' => $company->branding_config,
            'email_config' => $company->email_config,
            'spokesperson' => $company->spokesperson,
            'customer_count' => $company->customer_count,
            'employee_count' => $company->employee_count,
        ];
    }

    /**
     * Filter data by company context (for multi-tenant operations).
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('ID', $companyId);
    }
}
