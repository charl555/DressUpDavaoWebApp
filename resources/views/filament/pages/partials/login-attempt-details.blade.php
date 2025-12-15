<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="text-sm font-medium text-gray-500">Email</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->email }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">IP Address</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->ip_address }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Status</span>
            <p class="text-sm">
                @if($record->success)
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-success-100 text-success-800">
                        Successful
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-danger-100 text-danger-800">
                        Failed
                    </span>
                @endif
            </p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Attempted At</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->attempted_at->format('M j, Y g:i:s A') }}</p>
        </div>
    </div>
</div>

