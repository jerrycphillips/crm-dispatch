<?php

namespace App\Providers;

use App\Models\Employee;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class EmployeeUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById($identifier)
    {
        return Employee::where('EmployeeID', $identifier)
            ->where('access_granted', 1)
            ->where(function($query) {
                $query->whereNull('NoLongerEmployed')
                      ->orWhere('NoLongerEmployed', 0);
            })
            ->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     */
    public function retrieveByToken($identifier, $token)
    {
        return Employee::where('EmployeeID', $identifier)
            ->where('remember_token', $token)
            ->where('access_granted', 1)
            ->where(function($query) {
                $query->whereNull('NoLongerEmployed')
                      ->orWhere('NoLongerEmployed', 0);
            })
            ->first();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) || !isset($credentials['loginEmail'])) {
            return null;
        }

        return Employee::where('loginEmail', $credentials['loginEmail'])
            ->where('access_granted', 1)
            ->where(function($query) {
                $query->whereNull('NoLongerEmployed')
                      ->orWhere('NoLongerEmployed', 0);
            })
            ->first();
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (!isset($credentials['password']) || !isset($credentials['loginEmail'])) {
            return false;
        }

        // Email validation like your original function
        if (!filter_var($credentials['loginEmail'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Password complexity validation like your original validatePwd function
        $passwordErrors = $this->validatePassword($credentials['password']);
        if (!empty($passwordErrors)) {
            // Password doesn't meet complexity requirements
            return false;
        }

        // Custom hash validation using your algorithm
        $prependSalt = env('SALT_PREPEND', '!ays$');
        $appendSalt = env('SALT_APPEND', '#csi');
        $token = hash('ripemd128', $prependSalt . $credentials['password'] . $appendSalt);

        return $user->token === $token;
    }

    /**
     * Validate password complexity (matching your original validatePwd function)
     */
    private function validatePassword($candidate)
    {
        $errors = array();
        
        if (strlen($candidate) < 8 || strlen($candidate) > 16) {
            $errors[] = "Password should be min 8 characters and max 16 characters";
        }
        if (!preg_match("/\d/", $candidate)) {
            $errors[] = "Password should contain at least one digit";
        }
        if (!preg_match("/[A-Z]/", $candidate)) {
            $errors[] = "Password should contain at least one Capital Letter";
        }
        if (!preg_match("/[a-z]/", $candidate)) {
            $errors[] = "Password should contain at least one small Letter";
        }
        if (!preg_match("/\W/", $candidate)) {
            $errors[] = "Password should contain at least one special character";
        }
        if (preg_match("/\s/", $candidate)) {
            $errors[] = "Password should not contain any white space";
        }

        return $errors;
    }

    /**
     * Rehash the user's password if required and supported.
     */
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // Not applicable for this custom hash scheme
        return false;
    }
}
