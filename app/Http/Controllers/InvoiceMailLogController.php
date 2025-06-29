<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Jobs\SendInvoiceMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceMailLogController extends Controller
{
    public function index()
    {
        // Sent: latest successful log per invoice
        $logs = \App\Models\InvoiceLog::with(['customer', 'invoice.pdf'])
            ->where('status', 1)
            ->latest('sent_at')
            ->get()
            ->unique('invoice_id');

        // Failed: latest failed log per invoice
        $failedLogs = \App\Models\InvoiceLog::with(['customer', 'invoice.pdf'])
            ->where('status', 0)
            ->latest('sent_at')
            ->get()
            ->unique('invoice_id');

        // Pending: invoices with mail_sent = 0 and NO log entry
        $pendingInvoices = \App\Models\Invoice::with(['customer', 'pdf'])
            ->where('mail_sent', 0)
            ->whereDoesntHave('logs')
            ->get();

        return view('invoice.mail_logs', compact('logs', 'pendingInvoices', 'failedLogs'));
    }

    public function sendPendingMails(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('invoice.mail_logs');
        }
        $pendingInvoices = Invoice::where('mail_sent', 0)->whereDoesntHave('logs')->get();
        if ($pendingInvoices->isEmpty()) {
            return response()->json(['message' => 'No pending invoices to send.']);
        }
        foreach ($pendingInvoices as $invoice) {
            SendInvoiceMailJob::dispatch($invoice);
        }
        return response()->json(['message' => 'Pending invoices are being sent.']);
    }

    public function resendFailedMails(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('invoice.mail_logs');
        }
        $failedLogs = InvoiceLog::where('status', 0)->get();
        if ($failedLogs->isEmpty()) {
            return response()->json(['message' => 'No failed invoices to resend.']);
        }
        foreach ($failedLogs as $log) {
            $invoice = $log->invoice;
            if ($invoice) {
                SendInvoiceMailJob::dispatch($invoice);
            }
        }
        return response()->json(['message' => 'Failed invoices are being resent.']);
    }

    public function partialTables()
    {
        // Get latest data
        $logs = \App\Models\InvoiceLog::with(['customer', 'invoice.pdf'])
            ->where('status', 1)
            ->latest('sent_at')
            ->get()
            ->unique('invoice_id');
        $failedLogs = \App\Models\InvoiceLog::with(['customer', 'invoice.pdf'])
            ->where('status', 0)
            ->latest('sent_at')
            ->get()
            ->unique('invoice_id');
        $pendingInvoices = \App\Models\Invoice::with(['customer', 'pdf'])
            ->where('mail_sent', 0)
            ->whereDoesntHave('logs')
            ->get();
        $html = view('invoice.partials.tables', compact('logs', 'pendingInvoices', 'failedLogs'))->render();
        return response()->json(['html' => $html]);
    }
}
