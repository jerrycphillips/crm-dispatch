<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'FirstName' => 'nullable|string|max:50',
            'LastName' => 'nullable|string|max:50',
            'Email' => 'nullable|email|max:50',
            // Add other fields and validation rules as needed
        ]);

        $customer = Customer::create($validated);

        return redirect()->route('customers.edit', $customer->ID)->with('success', 'Customer created!');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'FirstName' => 'nullable|string|max:50',
            'LastName' => 'nullable|string|max:50',
            'Email' => 'nullable|email|max:50',
            // Add other fields
        ]);

        $customer->update($validated);

        return redirect()->back()->with('success', 'Customer updated!');
    }
}