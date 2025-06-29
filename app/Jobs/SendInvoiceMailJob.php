<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\InvoicePdf;
use App\Mail\InvoiceMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use PDF;

class SendInvoiceMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invoice;
    public $tries = 5; 
    public $maxExceptions = 3;
    public $timeout = 30; 
    public $memory = 128; 

    /**
     * Create a new job instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Rate limiting: max 50 jobs per minute per worker
        RateLimiter::attempt(
            'send-invoice-mail',
            50,
            function () {
                $this->processInvoice();
            },
            60 // decay in seconds
        );
    }

    private function processInvoice()
    {
        $start = microtime(true);
        DB::beginTransaction();
        try {
            $customer = $this->invoice->customer()->with('invoice')->first();
            // PDF generation (timed)
            $pdf = PDF::loadView('invoice.pdf', ['invoice' => $this->invoice, 'customer' => $customer]);
            $pdfPath = 'invoices/' . $this->invoice->id . '_' . now()->format('Ymd') . '.pdf';
            Storage::put('public/' . $pdfPath, $pdf->output());
            $fullPath = storage_path('app/public/' . $pdfPath);
            InvoicePdf::updateOrCreate([
                'invoice_id' => $this->invoice->id,
            ], [
                'pdf_path' => 'storage/' . $pdfPath,
            ]);
            // Email send (timed)
            Mail::to($customer->email)->send(new InvoiceMail($this->invoice, $fullPath));
            $this->invoice->update(['mail_sent' => 1]);
            \App\Models\InvoiceLog::create([
                'invoice_id' => $this->invoice->id,
                'customer_id' => $customer->id,
                'status' => 1,
                'sent_at' => now(),
                'message' => 'Invoice sent successfully',
            ]);
            DB::commit();
            $duration = (microtime(true) - $start) * 1000;
            Log::info('Invoice processed', [
                'invoice_id' => $this->invoice->id,
                'pdf_ms' => $duration,
                'memory_usage_mb' => round(memory_get_usage(true) / 1048576, 2),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \App\Models\InvoiceLog::create([
                'invoice_id' => $this->invoice->id,
                'customer_id' => $this->invoice->customer_id,
                'status' => 0,
                'sent_at' => now(),
                'message' => $e->getMessage(),
            ]);
            Log::error('Invoice job failed', [
                'invoice_id' => $this->invoice->id,
                'error' => $e->getMessage(),
            ]);
            throw $e; // Let Laravel handle retry
        }
    }
}
