<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Purchase Order') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('purchase-orders.store') }}">
                    @csrf
                    
                    {{-- Header Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} (Lead time: {{ $supplier->lead_time_days }} days)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Date <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional notes..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    {{-- Line Items --}}
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-semibold text-gray-800">Order Items</h3>
                            <button type="button" id="add-item" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold py-1 px-3 rounded">
                                + Add Item
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto border-collapse" id="items-table">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-2 py-2 border text-sm w-1/2">Product</th>
                                        <th class="px-2 py-2 border text-sm w-1/6">Qty</th>
                                        <th class="px-2 py-2 border text-sm w-1/6">Unit Price (₱)</th>
                                        <th class="px-2 py-2 border text-sm w-1/6 text-right">Total</th>
                                        <th class="px-2 py-2 border"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body">
                                    {{-- Default Row --}}
                                    <tr class="item-row">
                                        <td class="px-2 py-2 border">
                                            <select name="items[0][product_id]" required class="w-full border-gray-300 rounded-md text-sm product-select">
                                                <option value="">-- Select Product --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                                                        {{ $product->name }} ({{ $product->sku }}) - Stock: {{ $product->quantity }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-2 py-2 border">
                                            <input type="number" name="items[0][quantity_ordered]" required min="1" value="1" class="w-full border-gray-300 rounded-md text-sm qty-input">
                                        </td>
                                        <td class="px-2 py-2 border">
                                            <input type="number" step="0.01" name="items[0][unit_price]" required min="0" value="0.00" class="w-full border-gray-300 rounded-md text-sm price-input">
                                        </td>
                                        <td class="px-2 py-2 border text-right font-semibold line-total">₱0.00</td>
                                        <td class="px-2 py-2 border text-center">
                                            <button type="button" class="text-red-500 hover:text-red-700 remove-item text-sm">✕</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="flex justify-end mb-6">
                        <div class="w-64 bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-semibold" id="subtotal">₱0.00</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">VAT (12%):</span>
                                <span class="font-semibold" id="tax">₱0.00</span>
                            </div>
                            <div class="flex justify-between text-base border-t pt-2">
                                <span class="font-bold text-gray-800">Total:</span>
                                <span class="font-bold text-brand-600" id="grand-total">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('purchase-orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">Cancel</a>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded">Save Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript for Dynamic Items --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = 1;
            const itemsBody = document.getElementById('items-body');
            const addBtn = document.getElementById('add-item');

            function formatCurrency(value) {
                return '₱' + parseFloat(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const lineTotal = qty * price;
                    row.querySelector('.line-total').textContent = formatCurrency(lineTotal);
                    subtotal += lineTotal;
                });
                const tax = subtotal * 0.12;
                const total = subtotal + tax;

                document.getElementById('subtotal').textContent = formatCurrency(subtotal);
                document.getElementById('tax').textContent = formatCurrency(tax);
                document.getElementById('grand-total').textContent = formatCurrency(total);
            }

            // Add Row
            addBtn.addEventListener('click', function () {
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.innerHTML = `
                    <td class="px-2 py-2 border">
                        <select name="items[${itemIndex}][product_id]" required class="w-full border-gray-300 rounded-md text-sm product-select">
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-2 py-2 border"><input type="number" name="items[${itemIndex}][quantity_ordered]" required min="1" value="1" class="w-full border-gray-300 rounded-md text-sm qty-input"></td>
                    <td class="px-2 py-2 border"><input type="number" step="0.01" name="items[${itemIndex}][unit_price]" required min="0" value="0.00" class="w-full border-gray-300 rounded-md text-sm price-input"></td>
                    <td class="px-2 py-2 border text-right font-semibold line-total">₱0.00</td>
                    <td class="px-2 py-2 border text-center"><button type="button" class="text-red-500 hover:text-red-700 remove-item text-sm">✕</button></td>
                `;
                itemsBody.appendChild(newRow);
                itemIndex++;
            });

            // Remove Row & Calculate
            itemsBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item')) {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        e.target.closest('tr').remove();
                        calculateTotals();
                    } else {
                        alert('At least one item is required.');
                    }
                }
            });

            // Auto-fill Price & Calculate
            itemsBody.addEventListener('change', function (e) {
                if (e.target.classList.contains('product-select')) {
                    const price = e.target.options[e.target.selectedIndex].getAttribute('data-price');
                    if (price) {
                        e.target.closest('tr').querySelector('.price-input').value = price;
                    }
                    calculateTotals();
                }
            });

            itemsBody.addEventListener('input', function (e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                    calculateTotals();
                }
            });
        });
    </script>
</x-app-layout>