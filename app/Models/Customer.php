<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Explicitly map to your existing table name
    protected $table = 'CustTbl';

    // Primary key field name
    protected $primaryKey = 'ID';

    // Disable auto-incrementing if you don't want Laravel to expect it
    public $incrementing = true;

    // Custom timestamp columns
    const CREATED_AT = 'TimeCreated';
    const UPDATED_AT = 'last_modified';

    // Enable Laravel's timestamp handling
    public $timestamps = true;

    // Optional: If your primary key isn't a UUID or string
    protected $keyType = 'int';

    // Optional: Mass assignable fields (customize this later as needed)
    protected $fillable = [
        'Customer', 'FirstName', 'LastName', 'Salutation',
        'Email', 'Email2', 'Business', 'BillingAddress', 'BillingCity', 'BillingZip',
        'BillingPhone', 'BillingPhone2'
        // Add more as needed
    ];
}