<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($payments as $payment)
                <tr>
                    <td class="px-4 py-2">
                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M j, Y') }}
                    </td>
                    <td class="px-4 py-2">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-2 text-right">₱{{ number_format($payment->amount_paid, 2) }}</td>
                    <td class="px-4 py-2">
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $payment->payment_status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>