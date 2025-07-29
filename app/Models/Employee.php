<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Employee extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $table = 'Employees'; // explicitly specify your table name

    protected $primaryKey = 'EmployeeID'; // matching your database structure

    const UPDATED_AT = 'last_modified';
    const CREATED_AT = null;

    public $timestamps = true; // disable Laravel's default timestamp behavior

    protected $fillable = [
        // Add your actual column names here, for example:
        'QBID',
        'Wage',
        'LastName',
        'FirstName',
        'PrimaryPosition',
        'BirthDate',
        'HireDate',
        'Address',
        'City',
        'ZipCode',
        'HomePhone',
        'AlternatePhone',
        'Extension',
        'MobilePhone',
        'RadioID',
        'Email',
        'DispatchEmail',
        'NoLongerEmployed',
        'DatabaseLoginName1',
        'DatabaseLoginName2',
        'TruckNo',
        'MapColor',
        'LoggedInRemotely',
        'Company',
        'DriversLicenseNo',
        'last_modified',
        'upsize_ts',
        'CustID',
        'OnCallStatus',
        'OnCallLastAlertTime',
        'NearByNowUserName',
        'pin',
        'loginEmail',
        'email_validated',
        'token',
        'access_granted',
        'user_role',
        'windows_username',
        'last_activity',
        'max_idle_minuites',
        'FacilityAccess',
        'observed_by'
        // etc.
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'EmployeeID';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute('EmployeeID');
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->getAttribute('token');
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->getAttribute('remember_token');
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->setAttribute('remember_token', $value);
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}