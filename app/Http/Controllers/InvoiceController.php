<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->get();
        return view('invoice.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $customer = null;
        if ($request->has('customer_id')) {
            $customer = Customer::find($request->customer_id);
        }
        return view('invoice.create', compact('customer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $invoice = Invoice::create($validated);
        return redirect()->route('customers.index')->with('success', 'Invoice generated successfully.');
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $customer = $invoice->customer ?? Customer::find($invoice->customer_id);
        return view('invoice.edit', compact('invoice', 'customer'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $invoice->update($validated);
        return redirect()->route('customers.index')->with('success', 'Invoice updated successfully.');
    }
}
