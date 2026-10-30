<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Supplier Details') }}</h2>
            <a href="{{ route('suppliers.index') }}" class="text-brand-600 hover:underline text-sm font-semibold">← Back to List</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $supplier->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $supplier->is_active ? 'Active Supplier' : 'Inactive Supplier' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold py-2 px-4 rounded">Edit</a>
                    </div>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mt-6">
                    <div><dt class="text-gray-500">Contact Person</dt><dd class="font-semibold">{{ $supplier->contact_person }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-semibold">{{ $supplier->email }}</dd></div>
                    <div><dt class="text-gray-500">Phone</dt><dd class="font-semibold">{{ $supplier->phone }}</dd></div>
                    <div><dt class="text-gray-500">Payment Terms</dt><dd class="font-semibold">{{ $supplier->payment_terms ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Lead Time</dt><dd class="font-semibold">{{ $supplier->lead_time_days }} days</dd></div>
                    <div><dt class="text-gray-500">Minimum Order Amount</dt><dd class="font-semibold">₱{{ number_format($supplier->minimum_order_amount, 2) }}</dd></div>
                    <div class="md:col-span-2"><dt class="text-gray-500">Address</dt><dd class="font-semibold">{{ $supplier->address }}</dd></div>
                </dl>
            </div>

            {{-- Placeholder for Purchase Orders --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Recent Purchase Orders</h3>
                <p class="text-gray-500 text-sm">No purchase orders yet. (Feature coming in Part 2)</p>
            </div>
        </div>
    </div>
</x-app-layout>