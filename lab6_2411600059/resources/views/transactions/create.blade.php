<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Inventory Transaction') }}
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

                <form method="POST" action="{{ route('transactions.store') }}">
                    @csrf

                    {{-- Product --}}
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
                                        data-sku="{{ $product->sku }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }}) — Qty: {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>
                        <p id="currentStock" class="text-xs text-gray-500 mt-1"></p>
                    </div>

                    {{-- Type --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Transaction Type <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="stock_in" {{ old('type') === 'stock_in' ? 'checked' : '' }} required
                                       class="text-brand-600 focus:ring-brand-500">
                                <span class="ml-2 text-sm">Stock In</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="stock_out" {{ old('type') === 'stock_out' ? 'checked' : '' }}
                                       class="text-brand-600 focus:ring-brand-500">
                                <span class="ml-2 text-sm">Stock Out</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="adjustment" {{ old('type') === 'adjustment' ? 'checked' : '' }}
                                       class="text-brand-600 focus:ring-brand-500">
                                <span class="ml-2 text-sm">Adjustment</span>
                            </label>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <p id="quantityHint" class="text-xs text-gray-500 mt-1"></p>
                    </div>

                    {{-- Reference --}}
                    <div class="mb-4">
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Reference Number
                        </label>
                        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                               placeholder="e.g., PO-2026-001"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>

                    {{-- Notes --}}
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500"
                                  placeholder="Optional additional context...">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('transactions.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                            Record Transaction
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- JS: Show current stock and set max for stock out --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('product_id');
            const quantityInput = document.getElementById('quantity');
            const currentStock = document.getElementById('currentStock');
            const quantityHint = document.getElementById('quantityHint');

            function updateStockInfo() {
                const selected = productSelect.options[productSelect.selectedIndex];
                const qty = selected.getAttribute('data-quantity');

                if (qty !== null && qty !== '') {
                    currentStock.textContent = 'Current stock: ' + qty + ' units';
                    const type = document.querySelector('input[name="type"]:checked');
                    if (type && type.value === 'stock_out') {
                        quantityInput.max = qty;
                        quantityHint.textContent = 'Maximum: ' + qty + ' (available stock)';
                    } else {
                        quantityInput.removeAttribute('max');
                        quantityHint.textContent = '';
                    }
                } else {
                    currentStock.textContent = '';
                    quantityHint.textContent = '';
                }
            }

            productSelect.addEventListener('change', updateStockInfo);

            document.querySelectorAll('input[name="type"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'stock_out') {
                        quantityHint.textContent = 'Cannot exceed available stock.';
                    } else {
                        quantityHint.textContent = '';
                        quantityInput.removeAttribute('max');
                    }
                });
            });

            updateStockInfo();
        });
    </script>
</x-app-layout>