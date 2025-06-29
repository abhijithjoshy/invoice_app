<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Jobs\SendInvoiceMailJob;
use Carbon\Carbon;

class SendMonthlyInvoices extends Command
{
    protected $signature = 'invoices:send-monthly';
    protected $description = 'Send all unsent invoices as PDF via email at month end';

    public function handle()
    {
        $invoices = Invoice::where('mail_sent', 0)->get();
        foreach ($invoices as $invoice) {
            SendInvoiceMailJob::dispatch($invoice);
        }
        $this->info('Dispatched jobs for all unsent invoices.');
    }
}
