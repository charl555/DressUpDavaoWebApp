@php
    $colorClasses = [
        'perfect' => 'bg-gradient-to-r from-purple-50 to-violet-50 border-purple-200',
        'excellent' => 'bg-gradient-to-r from-violet-50 to-purple-50 border-violet-200',
        'good' => 'bg-gradient-to-r from-indigo-50 to-purple-50 border-indigo-200',
        'fair' => 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200',
        'poor' => 'bg-gradient-to-r from-red-50 to-orange-50 border-red-200'
    ];

    $colorClass = $colorClasses[$recommendation['level']] ?? 'bg-gray-50 border-gray-200';
@endphp

<div class="border {{ $colorClass }} rounded-lg p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            {{-- <span class="text-2xl">{{ $recommendation['icon'] }}</span> --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-900">{{ $recommendation['label'] }}</h4>
                <p class="text-sm text-gray-600">{{ $recommendation['description'] }}</p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-2xl font-bold text-purple-600">{{ round($fitScore) }}%</span>
            <div class="text-sm text-gray-500">Match</div>
        </div>
    </div>

    <div class="mt-3 pt-3 border-t border-gray-200">
        <div class="text-xs text-gray-500 text-center">
            Based on your body measurements comparison
        </div>
    </div>
</div>