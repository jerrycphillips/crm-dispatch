<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Employee;

class BypassAuthInDevelopment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only bypass auth in non-production environments
        if (!app()->environment('production')) {
            // Check if user is already authenticated
            if (!Auth::check()) {
                // Auto-login with a default development user
                $developmentUser = Employee::where('loginEmail', config('app.dev_user_email', 'dev@example.com'))->first();
                
                if ($developmentUser) {
                    Auth::login($developmentUser);
                    
                    // Store employee data in session (matching your existing login logic)
                    $request->session()->put([
                        'employee_id' => 1, // Your specific employee ID
                        'last_name' => 'Phillips',
                        'first_name' => 'Jerry',
                        'login_email' => $developmentUser->loginEmail,
                        'user_role' => 'admin',
                    ]);
                }
            }
        }

        return $next($request);
    }
}
