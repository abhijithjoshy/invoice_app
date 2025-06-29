<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index()
    {
        try {
            $invoices = Invoice::with('customer')->latest()->get();
            return view('invoice.index', compact('invoices'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@index: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error loading invoices.');
        }
    }

    public function create(Request $request)
    {
        try {
            $customer = null;
            if ($request->has('customer_id')) {
                $customer = Customer::find($request->customer_id);
            }
            return view('invoice.create', compact('customer'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@create: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error loading create invoice form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);
            $invoice = Invoice::create($validated);
            return redirect()->route('customers.index')->with('success', 'Invoice generated successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@store: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error creating invoice.');
        }
    }

    public function edit($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $customer = $invoice->customer ?? Customer::find($invoice->customer_id);
            return view('invoice.edit', compact('invoice', 'customer'));
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@edit: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error loading invoice for edit.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);
            $invoice->update($validated);
            return redirect()->route('customers.index')->with('success', 'Invoice updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in InvoiceController@update: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors('Error updating invoice.');
        }
    }
}
