<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">Invoice #{{ $invoice->id }}</div>
    <div class="section">
        <span class="label">Customer:</span> {{ $customer->name }}<br>
        <span class="label">Email:</span> {{ $customer->email }}<br>
        <span class="label">Date:</span> {{ $invoice->created_at->format('Y-m-d') }}
    </div>
    <div class="section">
        <span class="label">Amount:</span> {{ $invoice->amount }}<br>
        <span class="label">Description:</span> {{ $invoice->description }}
    </div>
</body>
</html>
