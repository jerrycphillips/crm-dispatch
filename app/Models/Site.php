<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'AddressTbl'; // Actual SQL Server table name
    protected $primaryKey = 'ID';     // Your PK
    public $incrementing = true;
    protected $keyType = 'int';
    // Custom timestamp columns
    const CREATED_AT = 'TimeCreated';
    const UPDATED_AT = 'last_modified';
    public $timestamps = true;

    protected $fillable = [
        // Add actual column names from AddressTbl if you want to use mass assignment
        // e.g., 'Street', 'City', 'State', 'Zip'
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
        'TimeCreated',
        'last_modified',
        'RooftopGPS'
    ];
}