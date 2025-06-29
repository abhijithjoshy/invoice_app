# Your Invoice

Dear {{ $invoice->customer->name }},

Please find your invoice attached as a PDF.

Thanks,<br>
{{ config('app.name') }}
