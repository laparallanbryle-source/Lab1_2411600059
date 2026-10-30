<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Orders') }}
            </h2>
            <a href="{{ route('purchase-orders.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                + Create Purchase Order
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Status</label>
                        <select name="status" class="border-gray-300 rounded-md text-sm">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Supplier</label>
                        <select name="supplier_id" class="border-gray-300 rounded-md text-sm">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-brand-600 text-white text-sm py-2 px-4 rounded">Filter</button>
                    <a href="{{ route('purchase-orders.index') }}" class="text-sm text-gray-500 hover:underline py-2">Reset</a>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-4 py-3 border text-sm">Reference</th>
                                <th class="px-4 py-3 border text-sm">Supplier</th>
                                <th class="px-4 py-3 border text-sm">Date</th>
                                <th class="px-4 py-3 border text-sm text-right">Total</th>
                                <th class="px-4 py-3 border text-sm text-center">Status</th>
                                <th class="px-4 py-3 border text-sm text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseOrders as $po)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border text-sm font-semibold">{{ $po->reference_number }}</td>
                                    <td class="px-4 py-3 border text-sm">{{ $po->supplier->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 border text-sm">{{ $po->order_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 border text-sm text-right font-semibold">₱{{ number_format($po->total, 2) }}</td>
                                    <td class="px-4 py-3 border text-center">
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
                                        <span class="text-xs font-semibold px-2 py-1 rounded {{ $colors[$po->status] ?? 'bg-gray-100' }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 border text-center space-x-2 whitespace-nowrap">
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="text-brand-600 hover:underline">View</a>
                                        @if($po->status === 'draft')
                                            <a href="{{ route('purchase-orders.edit', $po) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No purchase orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $purchaseOrders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>