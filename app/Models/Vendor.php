<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'Vendor';

    // Primary key field name
    protected $primaryKey = 'ListID';

    // Primary key is varchar(50), not auto-incrementing
    public $incrementing = false;
    protected $keyType = 'string';

    // Custom timestamp columns
    const CREATED_AT = 'TimeCreated';
    const UPDATED_AT = 'TimeModified';

    // Enable Laravel's timestamp handling
    public $timestamps = true;

    // Mass assignable fields
    protected $fillable = [
        'ListID',
        'EditSequence',
        'Name',
        'IsActive',
        'CompanyName',
        'Salutation',
        'FirstName',
        'MiddleName',
        'LastName',
        'VendorAddressAddr1',
        'VendorAddressAddr2',
        'VendorAddressAddr3',
        'VendorAddressAddr4',
        'VendorAddressAddr5',
        'VendorAddressCity',
        'VendorAddressState',
        'VendorAddressPostalCode',
        'VendorAddressCountry',
        'VendorAddressNote',
        'VendorAddressBlockAddr1',
        'VendorAddressBlockAddr2',
        'VendorAddressBlockAddr3',
        'VendorAddressBlockAddr4',
        'VendorAddressBlockAddr5',
        'Phone',
        'AltPhone',
        'Fax',
        'Email',
        'Contact',
        'AltContact',
        'NameOnCheck',
        'AccountNumber',
        'Notes',
        'VendorTypeRefListID',
        'VendorTypeRefFullName',
        'TermsRefListID',
        'TermsRefFullName',
        'CreditLimit',
        'VendorTaxIdent',
        'IsVendorEligibleFor1099',
        'OpenBalance',
        'OpenBalanceDate',
        'BillingRateRefListID',
        'BillingRateRefFullName',
        'Balance',
        'OnMobile',
        'CustID',
        'invoiceFrequencyDays',
        'ChargesTax',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'TimeCreated' => 'datetime',
        'TimeModified' => 'datetime',
        'IsActive' => 'boolean',
        'CreditLimit' => 'decimal:2',
        'IsVendorEligibleFor1099' => 'boolean',
        'OpenBalance' => 'decimal:2',
        'OpenBalanceDate' => 'datetime',
        'Balance' => 'decimal:2',
        'OnMobile' => 'boolean',
        'CustID' => 'integer',
        'invoiceFrequencyDays' => 'integer',
        'ChargesTax' => 'boolean',
    ];

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the vendor's full name (combines FirstName and LastName if available).
     */
    public function getFullNameAttribute()
    {
        if ($this->FirstName || $this->LastName) {
            return trim($this->FirstName . ' ' . $this->LastName);
        }
        return $this->Name;
    }

    /**
     * Get the vendor's display name (Company or Full Name).
     */
    public function getDisplayNameAttribute()
    {
        return $this->CompanyName ?: $this->full_name;
    }

    /**
     * Get the complete vendor address as a formatted string.
     */
    public function getFormattedAddressAttribute()
    {
        $addressParts = array_filter([
            $this->VendorAddressAddr1,
            $this->VendorAddressAddr2,
            $this->VendorAddressAddr3,
            $this->VendorAddressAddr4,
            $this->VendorAddressAddr5,
        ]);

        $cityStateZip = trim(implode(' ', array_filter([
            $this->VendorAddressCity,
            $this->VendorAddressState,
            $this->VendorAddressPostalCode,
        ])));

        if ($cityStateZip) {
            $addressParts[] = $cityStateZip;
        }

        if ($this->VendorAddressCountry) {
            $addressParts[] = $this->VendorAddressCountry;
        }

        return implode("\n", $addressParts);
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get only active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('IsActive', true);
    }

    /**
     * Scope to get vendors eligible for 1099.
     */
    public function scopeEligibleFor1099($query)
    {
        return $query->where('IsVendorEligibleFor1099', true);
    }

    /**
     * Scope to search vendors by name or company.
     */
    public function scopeSearchByName($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('Name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('CompanyName', 'LIKE', "%{$searchTerm}%")
              ->orWhere('FirstName', 'LIKE', "%{$searchTerm}%")
              ->orWhere('LastName', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Scope to get vendors with outstanding balance.
     */
    public function scopeWithBalance($query)
    {
        return $query->where('Balance', '>', 0);
    }

    // ========================================
    // RELATIONSHIP DEFINITIONS
    // ========================================

    /**
     * Get all vouchers for this vendor.
     * Assuming the Voucher table has a 'Vendor' field that matches this vendor's name/identifier.
     */
    public function vouchers()
    {
        // This will depend on how vouchers link to vendors
        // You may need to adjust this based on your Voucher table structure
        return $this->hasMany(Voucher::class, 'Vendor', 'Name');
    }

    /**
     * Get the customer record if this vendor is also a customer.
     * Based on the CustID field.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustID', 'ID');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if vendor has any outstanding balance.
     */
    public function hasOutstandingBalance()
    {
        return $this->Balance > 0;
    }

    /**
     * Get the primary contact information.
     */
    public function getPrimaryContactAttribute()
    {
        return $this->Contact ?: $this->full_name;
    }

    /**
     * Get the vendor's tax ID for 1099 purposes.
     */
    public function getTaxIdAttribute()
    {
        return $this->VendorTaxIdent;
    }
}
