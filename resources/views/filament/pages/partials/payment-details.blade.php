<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="text-sm font-medium text-gray-500">Payment ID</span>
            <p class="text-sm text-gray-900 dark:text-white">PAY-{{ $record->payment_id }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Rental ID</span>
            <p class="text-sm text-gray-900 dark:text-white">RNT-{{ $record->rental_id }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Customer</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->rental?->customer?->first_name ?? 'N/A' }} {{ $record->rental?->customer?->last_name ?? '' }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Shop Owner</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->rental?->user?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Amount Paid</span>
            <p class="text-sm font-bold text-success-600">₱{{ number_format($record->amount_paid, 2) }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Payment Method</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->payment_method }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Payment Status</span>
            <p class="text-sm">
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full 
                    @if($record->payment_status === 'Paid') bg-success-100 text-success-800
                    @elseif($record->payment_status === 'Pending') bg-warning-100 text-warning-800
                    @else bg-danger-100 text-danger-800 @endif">
                    {{ $record->payment_status }}
                </span>
            </p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Payment Type</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ ucfirst($record->payment_type) }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Payment Date</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($record->payment_date)->format('M j, Y g:i A') }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Recorded At</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('M j, Y g:i A') }}</p>
        </div>
    </div>
</div>

