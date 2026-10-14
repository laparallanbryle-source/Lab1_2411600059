<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Reports') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Report 1: Inventory Valuation by Category --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg text-gray-800">Inventory Valuation by Category</h3>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total Inventory Value</div>
                        <div class="text-xl font-bold text-green-600">₱{{ number_format($totalInventoryValue, 2) }}</div>
                    </div>
                </div>

                @if($valuationByCategory->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-3 border text-sm">Category</th>
                                    <th class="px-4 py-3 border text-sm text-center">Products</th>
                                    <th class="px-4 py-3 border text-sm text-center">Total Units</th>
                                    <th class="px-4 py-3 border text-sm text-right">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($valuationByCategory as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border text-sm font-semibold">{{ $row->category }}</td>
                                        <td class="px-4 py-3 border text-sm text-center">{{ $row->product_count }}</td>
                                        <td class="px-4 py-3 border text-sm text-center">{{ $row->total_units }}</td>
                                        <td class="px-4 py-3 border text-sm text-right font-semibold text-green-600">
                                            ₱{{ number_format($row->total_value, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm py-4 text-center">No data.</p>
                @endif
            </div>

            {{-- Report 2: Low Stock Report --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">
                    Low Stock Report
                    <span class="text-sm font-normal text-gray-500">({{ $lowStockProducts->count() }} products)</span>
                </h3>

                @if($lowStockProducts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-3 border text-sm">SKU</th>
                                    <th class="px-4 py-3 border text-sm">Product</th>
                                    <th class="px-4 py-3 border text-sm">Category</th>
                                    <th class="px-4 py-3 border text-sm text-center">Qty</th>
                                    <th class="px-4 py-3 border text-sm text-center">Reorder Level</th>
                                    <th class="px-4 py-3 border text-sm text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border text-sm"><code class="text-brand-600">{{ $product->sku }}</code></td>
                                        <td class="px-4 py-3 border text-sm font-semibold">
                                            <a href="{{ route('products.show', $product) }}" class="text-brand-600 hover:underline">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 border text-sm">{{ $product->category }}</td>
                                        <td class="px-4 py-3 border text-sm text-center font-bold text-red-600">{{ $product->quantity }}</td>
                                        <td class="px-4 py-3 border text-sm text-center">{{ $product->reorder_level }}</td>
                                        <td class="px-4 py-3 border text-center">
                                            @if($product->isOutOfStock())
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Out of Stock</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Low Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm py-4 text-center">No low stock products.</p>
                @endif
            </div>

            {{-- Report 3: Stock Movement Report --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Stock Movement Report</h3>

                <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap gap-3 items-end mb-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                               class="border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                               class="border-gray-300 rounded-md text-sm">
                    </div>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm py-2 px-4 rounded">
                        Filter
                    </button>
                </form>

                @if($movements->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-3 border text-sm">Date</th>
                                    <th class="px-4 py-3 border text-sm">Product</th>
                                    <th class="px-4 py-3 border text-sm text-center">Type</th>
                                    <th class="px-4 py-3 border text-sm text-center">Quantity</th>
                                    <th class="px-4 py-3 border text-sm">Reference</th>
                                    <th class="px-4 py-3 border text-sm">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $txn)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border text-sm">
                                            {{ $txn->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->product->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 border text-center">
                                            @if($txn->type === 'stock_in')
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Stock In</span>
                                            @elseif($txn->type === 'stock_out')
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Stock Out</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Adjustment</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border text-center font-semibold">
                                            {{ $txn->type === 'stock_out' ? '-' : '+' }}{{ $txn->quantity }}
                                        </td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->reference_number ?? '—' }}</td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm py-4 text-center">No movements in this date range.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>