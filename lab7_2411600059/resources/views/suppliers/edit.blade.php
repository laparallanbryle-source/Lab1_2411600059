<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Supplier: ') }} {{ $supplier->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person <span class="text-red-500">*</span></label>
                            <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $supplier->email) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="2" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('address', $supplier->address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                            <input type="text" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lead Time (Days) <span class="text-red-500">*</span></label>
                            <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $supplier->lead_time_days) }}" required min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Amount (₱) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="minimum_order_amount" value="{{ old('minimum_order_amount', $supplier->minimum_order_amount) }}" required min="0" class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="flex items-center mt-6">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                            <label class="ml-2 block text-sm text-gray-900">Active Supplier</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('suppliers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded">Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>