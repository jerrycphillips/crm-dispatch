<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmployeeLoginRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Response as InertiaResponse;

class EmployeeAuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLoginForm(): View
    {
        return view('layouts.signin');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(EmployeeLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated employee
        $employee = Auth::user();

        // Store employee data in session
        $request->session()->put([
            'employee_id' => $employee->ID,
            'last_name' => $employee->LastName,
            'first_name' => $employee->FirstName,
            'login_email' => $employee->loginEmail,
            'user_role' => $employee->user_role,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Force a complete page navigation instead of an Inertia redirect
        return response('', 409, ['X-Inertia-Location' => route('login')]);
    }

    /**
     * Show the dashboard (example protected route).
     */
    public function dashboard(): View
    {
        $employee = Auth::user();
        
        return view('layouts.index', [
            'employee' => $employee,
            'employeeData' => [
                'id' => session('employee_id'),
                'firstName' => session('first_name'),
                'lastName' => session('last_name'),
                'email' => session('login_email'),
                'role' => session('user_role'),
            ]
        ]);
    }
}
