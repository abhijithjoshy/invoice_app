<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $customers = Customer::all();
            return view('customer.index', compact('customers'));
        } catch (\Throwable $e) {
            Log::error('Error in CustomerController@index: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error loading customers.');
        }
    }

    public function create()
    {
        try {
            return view('customer.create');
        } catch (\Throwable $e) {
            Log::error('Error in CustomerController@create: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error loading create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'nullable|string|max:20',
            ]);
            Customer::create($validated);
            return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in CustomerController@store: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error adding customer.');
        }
    }

    public function edit($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return view('customer.edit', compact('customer'));
        } catch (\Throwable $e) {
            Log::error('Error in CustomerController@edit: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error loading customer for edit.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email,' . $id,
                'phone' => 'nullable|string|max:20',
            ]);
            $customer->update($validated);
            return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in CustomerController@update: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error updating customer.');
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in CustomerController@destroy: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error deleting customer.');
        }
    }
}