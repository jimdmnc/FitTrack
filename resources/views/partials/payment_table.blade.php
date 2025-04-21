<tbody class="divide-y divide-black">
    @foreach ($payments as $payment)
        <tr class="bg-[#1e1e1e]">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">{{ $loop->iteration }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="text-sm font-medium text-gray-200">
                        {{ optional($payment->user)->first_name . ' ' . optional($payment->user)->last_name ?? 'Unknown User' }}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    @if(strtolower(optional($payment->user)->membership_type_name ?? '') == 'annual')
                        bg-purple-900 text-purple-200
                    @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'week')
                        bg-green-900 text-green-200
                    @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'month')
                        bg-blue-900 text-blue-200
                    @elseif(strtolower(optional($payment->user)->membership_type_name ?? '') == 'session')
                        bg-yellow-900 text-yellow-200
                    @endif
                ">
                    {{ optional($payment->user)->membership_type_name ?? 'N/A' }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-200">₱{{ number_format($payment->amount, 2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    @if(strtolower($payment->payment_method) == 'cash')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                    @elseif(strtolower($payment->payment_method) == 'credit card')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    @endif
                    <span class="text-sm text-gray-200">{{ $payment->payment_method }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                @if(optional($payment->user)->end_date)
                    <span class="@if(\Carbon\Carbon::parse($payment->user->end_date)->isPast()) text-red-500 @endif">
                        {{ \Carbon\Carbon::parse($payment->user->end_date)->format('M d, Y') }}
                    </span>
                @else
                    <span class="text-gray-400">N/A</span>
                @endif
            </td>
        </tr>
    @endforeach
</tbody>
