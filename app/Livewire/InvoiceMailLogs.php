<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Jobs\SendInvoiceMailJob;

class InvoiceMailLogs extends Component
{
    public $logs = [];
    public $pendingInvoices = [];
    public $failedLogs = [];
    public $sending = false;
    public $message = '';

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->logs = InvoiceLog::with(['customer', 'invoice.pdf'])
            ->where('status', 1)
            ->latest('sent_at')
            ->get()
            ->unique('invoice_id');

        $this->failedLogs = InvoiceLog::with(['customer', 'invoice.pdf'])
            ->where('status', 0)
            ->latest('sent_at')
            ->get()
            ->unique('invoice_id');

        $this->pendingInvoices = Invoice::with(['customer', 'pdf'])
            ->where('mail_sent', 0)
            ->whereDoesntHave('logs')
            ->get();
    }

    public function sendPendingMails()
    {
        $this->sending = true;
        $pendingInvoices = Invoice::where('mail_sent', 0)->whereDoesntHave('logs')->get();
        if ($pendingInvoices->isEmpty()) {
            $this->sending = false;
            $this->dispatch('mail-none', ['message' => 'No pending invoices to send.']);
            return;
        }
        foreach ($pendingInvoices as $invoice) {
            SendInvoiceMailJob::dispatch($invoice);
        }
        $this->sending = false;
        $this->message = 'Pending invoices are being sent.';
        $this->refreshData();
        $this->dispatch('mail-sent', ['message' => $this->message]);
    }

    public function resendFailedMails()
    {
        $this->sending = true;
        $failedLogs = InvoiceLog::where('status', 0)->get();
        if ($failedLogs->isEmpty()) {
            $this->sending = false;
            $this->dispatch('mail-none', ['message' => 'No failed invoices to resend.']);
            return;
        }
        foreach ($failedLogs as $log) {
            $invoice = $log->invoice;
            if ($invoice) {
                SendInvoiceMailJob::dispatch($invoice);
            }
        }
        $this->sending = false;
        $this->message = 'Failed invoices are being resent.';
        $this->refreshData();
        $this->dispatch('mail-sent', ['message' => $this->message]);
    }

    public function render()
    {
        return view('livewire.invoice-mail-logs');
    }
}
