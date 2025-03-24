@extends('layouts.app')

@section('content')
<section class="pt-10 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-lg shadow-gray-400 border border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-center gap-y-4 md:gap-y-0">
            <h2 class="font-extrabold text-lg sm:text-3xl text-gray-800">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700 leading-snug">Payment Tracking</span>
            </h2>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto bg-white p-6 shadow-lg rounded-lg">
    <!-- <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Payments</h2>
        <div class="flex gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Export</button>
            <button class="border-2 text-gray-800 px-4 py-2 rounded-lg">+ New Payment</button>
        </div>
    </div> -->



    <table class="w-full bg-white border rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="p-3">#</th>
                <th class="p-3">Customer</th>
                <th class="p-3">Status</th>
                <th class="p-3">Amount</th>
                <th class="p-3">Payment Method</th>
                <th class="p-3">Activation Date</th>
                <th class="p-3">Expiry Date</th>
                <!-- <th class="p-3">Actions</th> -->
            </tr>
        </thead>
        <tbody class="text-sm text-gray-500">
        @foreach ($payments as $payment)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">#{{ $payment->id }}</td>
                <td class="p-3">
            {{ optional($payment->user)->first_name . ' ' . optional($payment->user)->last_name ?? 'Unknown User' }}
        </td>
                        <td class="p-3">{{ $payment->rfid_uid }}</td>
                        <td class="p-3">${{ number_format($payment->amount, 2) }}</td>
                        <td class="p-3">{{ $payment->payment_method }}</td>
                        <td class="p-3">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('F, d Y') }}
                        </td>
                        <td class="p-3">
                            {{ optional($payment->user)->end_date ? \Carbon\Carbon::parse($payment->user->end_date)->format('F, d Y') : 'N/A' }}
                        </td>


                    </tr>
                @endforeach
        </tbody>

    </table>

    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">3</span> results
            </div>
            <div class="flex space-x-2">
                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Previous</button>
                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>
</section>
@endsection
