<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <span class="text-sm font-medium text-gray-500">Date/Time</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->created_at->format('M j, Y g:i:s A') }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">User</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->user?->name ?? 'System' }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Email</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->user?->email ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Action</span>
            <p class="text-sm">
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full 
                    @if(in_array($record->action, ['login', 'approve', 'unblock'])) bg-success-100 text-success-800
                    @elseif(in_array($record->action, ['delete', 'reject', 'block'])) bg-danger-100 text-danger-800
                    @elseif($record->action === 'update') bg-warning-100 text-warning-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($record->action) }}
                </span>
            </p>
        </div>
        <div class="col-span-2">
            <span class="text-sm font-medium text-gray-500">Description</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->description }}</p>
        </div>
        @if($record->model_type)
        <div>
            <span class="text-sm font-medium text-gray-500">Entity Type</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->model_type }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">Entity ID</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->model_id ?? 'N/A' }}</p>
        </div>
        @endif
        <div>
            <span class="text-sm font-medium text-gray-500">IP Address</span>
            <p class="text-sm text-gray-900 dark:text-white">{{ $record->ip_address ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-sm font-medium text-gray-500">User Agent</span>
            <p class="text-sm text-gray-900 dark:text-white truncate" title="{{ $record->user_agent }}">
                {{ \Illuminate\Support\Str::limit($record->user_agent, 50) ?? 'N/A' }}
            </p>
        </div>
    </div>

    @if($record->old_values || $record->new_values)
    <div class="border-t pt-4 mt-4">
        <h4 class="text-sm font-medium text-gray-500 mb-2">Changes</h4>
        <div class="grid grid-cols-2 gap-4">
            @if($record->old_values)
            <div>
                <span class="text-xs font-medium text-danger-600">Old Values</span>
                <pre class="text-xs bg-gray-100 dark:bg-gray-800 p-2 rounded mt-1 overflow-auto max-h-32">{{ json_encode($record->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
            @if($record->new_values)
            <div>
                <span class="text-xs font-medium text-success-600">New Values</span>
                <pre class="text-xs bg-gray-100 dark:bg-gray-800 p-2 rounded mt-1 overflow-auto max-h-32">{{ json_encode($record->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

