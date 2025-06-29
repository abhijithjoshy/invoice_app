<h4>Sent Invoice Emails</h4>
<div class="table-responsive mb-5">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Invoice ID</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Sent At</th>
                <th>PDF</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->invoice_id }}</td>
                    <td>{{ $log->customer ? $log->customer->name : '-' }}</td>
                    <td>
                        @if($log->status)
                            <span class="badge bg-success">Sent</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                    <td>{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>
                        @if($log->invoice && $log->invoice->pdf)
                            <a href="{{ asset($log->invoice->pdf->pdf_path) }}" target="_blank" class="btn btn-sm btn-primary">View PDF</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $log->message }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No sent invoice emails found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<h4>Pending Invoices</h4>
<div class="table-responsive mb-5">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Invoice ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Description</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendingInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->customer ? $invoice->customer->name : '-' }}</td>
                    <td>{{ $invoice->amount }}</td>
                    <td>{{ $invoice->description }}</td>
                    <td>
                        @if($invoice->pdf)
                            <a href="{{ asset($invoice->pdf->pdf_path) }}" target="_blank" class="btn btn-sm btn-primary">View PDF</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No pending invoices found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<h4>Failed Invoice Emails</h4>
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Invoice ID</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Sent At</th>
                <th>PDF</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @forelse($failedLogs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->invoice_id }}</td>
                    <td>{{ $log->customer ? $log->customer->name : '-' }}</td>
                    <td><span class="badge bg-danger">Failed</span></td>
                    <td>{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>
                        @if($log->invoice && $log->invoice->pdf)
                            <a href="{{ asset($log->invoice->pdf->pdf_path) }}" target="_blank" class="btn btn-sm btn-primary">View PDF</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $log->message }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No failed invoice emails found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
