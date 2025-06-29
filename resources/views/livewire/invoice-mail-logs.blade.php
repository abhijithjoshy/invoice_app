<div>
    <div class="mb-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Email Logs & Actions') }}
        </h2>
    </div>
    <div class="mb-4 d-flex gap-2">
        <button wire:click="sendPendingMails" class="btn btn-success" @if($sending) disabled @endif>
            @if($sending)
                <span class="spinner-border spinner-border-sm"></span> Sending...
            @else
                Send Pending Invoices
            @endif
        </button>
        <button wire:click="resendFailedMails" class="btn btn-warning" @if($sending) disabled @endif>
            @if($sending)
                <span class="spinner-border spinner-border-sm"></span> Sending...
            @else
                Resend Failed Invoices
            @endif
        </button>
    </div>
    <div id="mail-notification"></div>
    <div id="progress-bar-container" style="height: 6px; margin-bottom: 10px; display: none;">
        <div id="progress-bar" class="bg-primary" style="width: 0%; height: 100%; transition: width 0.5s;"></div>
    </div>
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
                        <td><span class="badge bg-success">Sent</span></td>
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
    <script>
        function showProgressBar() {
            const bar = document.getElementById('progress-bar');
            const container = document.getElementById('progress-bar-container');
            container.style.display = 'block';
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = '100%'; }, 100);
        }
        function hideProgressBar() {
            const bar = document.getElementById('progress-bar');
            const container = document.getElementById('progress-bar-container');
            bar.style.width = '0%';
            setTimeout(() => { container.style.display = 'none'; }, 500);
        }
        document.addEventListener('livewire:init', () => {
            window.livewire.hook('message.sent', () => {
                showProgressBar();
            });
            window.livewire.on('mail-sent', data => {
                hideProgressBar();
                const notif = document.getElementById('mail-notification');
                notif.innerHTML = `<div class='alert alert-success mt-2'>${data.message}</div>`;
                setTimeout(() => notif.innerHTML = '', 4000);
            });
            window.livewire.on('mail-none', data => {
                hideProgressBar();
                const notif = document.getElementById('mail-notification');
                notif.innerHTML = `<div class='alert alert-warning mt-2'>${data.message}</div>`;
                setTimeout(() => notif.innerHTML = '', 4000);
            });
        });
    </script>
</div>