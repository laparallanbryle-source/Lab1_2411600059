<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Adjustment') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-4">
                    Use this form when the physical count doesn't match the system. A difference will be calculated automatically.
                </p>

                <form method="POST" action="{{ route('transactions.adjustment.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Product <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="product_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-quantity="{{ $product->quantity }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }}) — System: {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>
                        <p id="systemQty" class="text-xs text-gray-500 mt-1"></p>
                    </div>

                    <div class="mb-4">
                        <label for="physical_count" class="block text-sm font-medium text-gray-700 mb-1">
                            Physical Count <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="physical_count" id="physical_count" min="0" required
                               value="{{ old('physical_count') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <p id="differenceInfo" class="text-xs mt-1"></p>
                    </div>

                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                            Reason for Adjustment <span class="text-red-500">*</span>
                        </label>
                        <select name="reason" id="reason" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">-- Select Reason --</option>
                            <option value="Physical count discrepancy">Physical count discrepancy</option>
                            <option value="Damaged goods">Damaged goods</option>
                            <option value="Theft / loss">Theft / loss</option>
                            <option value="System error correction">System error correction</option>
                            <option value="Return to supplier">Return to supplier</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Additional Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                  placeholder="Optional...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('transactions.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                            Process Adjustment
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('product_id');
            const physicalCount = document.getElementById('physical_count');
            const systemQty = document.getElementById('systemQty');
            const differenceInfo = document.getElementById('differenceInfo');

            function updateInfo() {
                const selected = productSelect.options[productSelect.selectedIndex];
                const sysQty = parseInt(selected.getAttribute('data-quantity'));

                if (!isNaN(sysQty)) {
                    systemQty.textContent = 'System quantity: ' + sysQty;
                    if (physicalCount.value !== '') {
                        const diff = parseInt(physicalCount.value) - sysQty;
                        if (diff > 0) {
                            differenceInfo.textContent = 'Difference: +' + diff + ' (will add to stock)';
                            differenceInfo.className = 'text-xs mt-1 text-green-600';
                        } else if (diff < 0) {
                            differenceInfo.textContent = 'Difference: ' + diff + ' (will deduct from stock)';
                            differenceInfo.className = 'text-xs mt-1 text-red-600';
                        } else {
                            differenceInfo.textContent = 'No difference — no adjustment needed.';
                            differenceInfo.className = 'text-xs mt-1 text-gray-500';
                        }
                    }
                } else {
                    systemQty.textContent = '';
                    differenceInfo.textContent = '';
                }
            }

            productSelect.addEventListener('change', updateInfo);
            physicalCount.addEventListener('input', updateInfo);
        });
    </script>
</x-app-layout>