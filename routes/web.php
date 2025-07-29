<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\EmployeeAuthController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VoucherController;

// Employee Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [EmployeeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [EmployeeAuthController::class, 'login']);
});

/*
possible routes:
index, show, create, store, edit, update, destroy

I could list all the routes individually like this:
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
But it's easier to use a resource controller like this, excluding destroy if I don't want to allow deletions:
*/
Route::resource('customers', CustomerController::class)->except(['destroy']);

Route::get('/', function () {
    return redirect()->route('login');
});

// Development-only auto-login route
if (!app()->environment('production')) {
    Route::get('/dev-login', function () {
        $developmentUser = \App\Models\Employee::where('loginEmail', config('app.dev_user_email', 'dev@example.com'))->first();
        
        if ($developmentUser) {
            Auth::login($developmentUser);
            
            // Store employee data in session
            session([
                'employee_id' => 1, // Your specific employee ID
                'last_name' => 'Phillips',
                'first_name' => 'Jerry',
                'login_email' => $developmentUser->loginEmail,
                'user_role' => 'admin',
            ]);
            
            return redirect()->route('dashboard');
        }
        
        return redirect()->route('login')->with('error', 'Development user not found');
    })->name('dev.login');
}

Route::middleware('auth')->group(function () {
    Route::post('logout', [EmployeeAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Voucher routes
    Route::resource('vouchers', VoucherController::class)->except(['destroy']);
    
    // API routes
    Route::get('/api/vouchers', [VoucherController::class, 'apiIndex'])->name('api.vouchers');
});

// require __DIR__.'/auth.php'; // Commented out to use custom Employee authentication
