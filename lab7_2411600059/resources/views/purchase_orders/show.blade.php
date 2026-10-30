<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Order: ') }} {{ $purchaseOrder->reference_number }}
            </h2>
            <a href="{{ route('purchase-orders.index') }}" class="text-brand-600 hover:underline text-sm font-semibold">← Back to List</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ $errors->first() }}</div>
            @endif

            {{-- Action Bar --}}
            <div class="bg-white shadow-sm rounded-lg p-4 flex justify-between items-center">
                <div>
                    @php
                        $colors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'sent' => 'bg-blue-100 text-blue-800',
                            'received' => 'bg-brand-100 text-brand-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="text-sm text-gray-500">Current Status:</span>
                    <span class="ml-2 text-sm font-bold px-3 py-1 rounded {{ $colors[$purchaseOrder->status] ?? '' }}">
                        {{ strtoupper($purchaseOrder->status) }}
                    </span>
                </div>

                <div class="flex gap-2">
                    @if($purchaseOrder->status === 'draft')
                        <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded">Submit for Approval</button>
                        </form>
                    @endif

                    @if($purchaseOrder->status === 'pending')
                        <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder) }}">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">Approve Order</button>
                        </form>

                        {{-- REJECT BUTTON BAGO --}}
                        <form method="POST" action="{{ route('purchase-orders.reject', $purchaseOrder) }}" 
                              onsubmit="let reason = prompt('Please enter the reason for rejection:'); if(reason){ this.rejection_reason.value = reason; return true; } else { return false; }">
                            @csrf
                            <input type="hidden" name="rejection_reason" value="">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded">Reject</button>
                        </form>
                    @endif

                    @if($purchaseOrder->status === 'approved')
                        <form method="POST" action="{{ route('purchase-orders.send', $purchaseOrder) }}">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Mark as Sent</button>
                        </form>
                    @endif

                    @if(in_array($purchaseOrder->status, ['sent', 'approved']))
                        <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" onsubmit="return confirm('Receive items? This will update product stock.');">
                            @csrf
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded">Receive Items</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- PO Details --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h3 class="font-bold text-gray-800 mb-2 border-b pb-1">Supplier Info</h3>
                        <p class="font-semibold text-brand-600">{{ $purchaseOrder->supplier->name }}</p>
                        <p>{{ $purchaseOrder->supplier->contact_person }}</p>
                        <p>{{ $purchaseOrder->supplier->email }} | {{ $purchaseOrder->supplier->phone }}</p>
                        <p class="text-gray-500 mt-2">{{ $purchaseOrder->supplier->address }}</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-2 border-b pb-1">Order Details</h3>
                        <p><span class="text-gray-500">Order Date:</span> {{ $purchaseOrder->order_date->format('M d, Y') }}</p>
                        <p><span class="text-gray-500">Expected Delivery:</span> {{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : 'N/A' }}</p>
                        <p><span class="text-gray-500">Created By:</span> {{ $purchaseOrder->createdBy->name }}</p>
                        @if($purchaseOrder->approvedBy)
                            <p><span class="text-gray-500">Approved By:</span> {{ $purchaseOrder->approvedBy->name }}</p>
                        @endif
                        @if($purchaseOrder->notes)
                            <p class="mt-2"><span class="text-gray-500">Notes:</span> {{ $purchaseOrder->notes }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-4 py-3 border text-sm">Product</th>
                                <th class="px-4 py-3 border text-sm text-center">Qty Ordered</th>
                                <th class="px-4 py-3 border text-sm text-right">Unit Price</th>
                                <th class="px-4 py-3 border text-sm text-right">Line Total</th>
                                <th class="px-4 py-3 border text-sm text-center">Qty Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td class="px-4 py-3 border text-sm">
                                        <span class="font-semibold">{{ $item->product->name ?? 'N/A' }}</span><br>
                                        <span class="text-xs text-gray-500">{{ $item->product->sku ?? '' }}</span>
                                    </td>
                                    <td class="px-4 py-3 border text-sm text-center">{{ $item->quantity_ordered }}</td>
                                    <td class="px-4 py-3 border text-sm text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 border text-sm text-right font-semibold">₱{{ number_format($item->line_total, 2) }}</td>
                                    <td class="px-4 py-3 border text-sm text-center font-semibold {{ $item->quantity_received >= $item->quantity_ordered ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $item->quantity_received }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold text-sm">Subtotal:</td>
                                <td class="px-4 py-2 text-right text-sm">₱{{ number_format($purchaseOrder->subtotal, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold text-sm">VAT (12%):</td>
                                <td class="px-4 py-2 text-right text-sm">₱{{ number_format($purchaseOrder->tax, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-bold text-gray-800">Total:</td>
                                <td class="px-4 py-2 text-right font-bold text-brand-600 text-lg">₱{{ number_format($purchaseOrder->total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>