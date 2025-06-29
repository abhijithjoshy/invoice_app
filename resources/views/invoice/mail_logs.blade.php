<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Email Logs & Actions') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="alert alert-info mb-4">
                    Invoice mails are automatically sent every month on the 28th. If you want to send them manually, you can do it from this page.
                </div>
                <div class="mb-4 d-flex gap-2">
                    <button id="send-pending" class="btn btn-success">Send Pending Invoices</button>
                    <button id="resend-failed" class="btn btn-warning">Resend Failed Invoices</button>
                </div>
                <div id="mail-notification"></div>
                <div id="progress-bar-container" style="height: 24px; margin-bottom: 10px; display: none; position: relative;">
                    <div id="progress-bar" class="bg-primary" style="width: 0%; height: 100%; transition: width 0.5s;"></div>
                    <span id="progress-text" style="position: absolute; left: 50%; top: 0; transform: translateX(-50%); color: #fff; font-weight: bold; line-height: 24px;">0%</span>
                </div>
                <div id="invoice-tables">
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
                </div>
            </div>
        </div>
    </div>
    <script>
        function showProgressBar() {
            const bar = document.getElementById('progress-bar');
            const container = document.getElementById('progress-bar-container');
            const text = document.getElementById('progress-text');
            container.style.display = 'block';
            bar.style.width = '0%';
            text.textContent = '0%';
            let percent = 0;
            const interval = setInterval(() => {
                if (percent < 100) {
                    percent += 10;
                    bar.style.width = percent + '%';
                    text.textContent = percent + '%';
                } else {
                    clearInterval(interval);
                }
            }, 50);
        }
        function hideProgressBar() {
            const bar = document.getElementById('progress-bar');
            const container = document.getElementById('progress-bar-container');
            const text = document.getElementById('progress-text');
            bar.style.width = '0%';
            text.textContent = '0%';
            setTimeout(() => { container.style.display = 'none'; }, 500);
        }
        function refreshTables() {
            fetch("{{ route('invoice.mail_logs.partials.tables') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById('invoice-tables').innerHTML = data.html;
                });
        }
        document.getElementById('send-pending').addEventListener('click', function() {
            showProgressBar();
            fetch("{{ route('invoice.mail_logs.send_pending') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            })
            .then(response => response.json())
            .then(data => {
                hideProgressBar();
                const notif = document.getElementById('mail-notification');
                notif.innerHTML = `<div class='alert alert-success mt-2'>${data.message ?? 'Pending invoices are being sent.'}</div>`;
                refreshTables();
            })
            .catch(() => {
                hideProgressBar();
                const notif = document.getElementById('mail-notification');
                notif.innerHTML = `<div class='alert alert-danger mt-2'>Error sending pending invoices.</div>`;
            });
        });
        document.getElementById('resend-failed').addEventListener('click', function() {
            showProgressBar();
            fetch("{{ route('invoice.mail_logs.resend_failed') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            })
            .then(response => response.json())
            .then(data => {
                hideProgressBar();
                const notif = document.getElementById('mail-notification');
                notif.innerHTML = `<div class='alert alert-success mt-2'>${data.message ?? 'Failed invoices are being resent.'}</div>`;
                refreshTables();
            })
            .catch(() => {
                hideProgressBar();
                const notif = document.getElementById('mail-notification');
                notif.innerHTML = `<div class='alert alert-danger mt-2'>Error resending failed invoices.</div>`;
            });
        });
    </script>
</x-app-layout>
