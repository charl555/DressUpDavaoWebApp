@php
    $colorClasses = [
        'perfect' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'excellent' => 'bg-green-100 text-green-800 border-green-200',
        'good' => 'bg-blue-100 text-blue-800 border-blue-200',
        'fair' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'poor' => 'bg-red-100 text-red-800 border-red-200'
    ];

    $colorClass = $colorClasses[$recommendation['level']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
@endphp

<div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $colorClass }} shadow-sm">
    {{-- <span class="mr-1">{{ $recommendation['icon'] }}</span> --}}
    {{ $recommendation['label'] }}
</div>