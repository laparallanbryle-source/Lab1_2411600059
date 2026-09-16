<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-brand-600">
                    <div class="text-sm text-gray-500">Total Products</div>
                    <div class="text-3xl font-bold text-brand-600 mt-1">{{ $totalProducts }}</div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-yellow-500">
                    <div class="text-sm text-gray-500">Low Stock</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-1">{{ $lowStock }}</div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-red-500">
                    <div class="text-sm text-gray-500">Out of Stock</div>
                    <div class="text-3xl font-bold text-red-600 mt-1">{{ $outOfStock }}</div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-green-500">
                    <div class="text-sm text-gray-500">Inventory Value</div>
                    <div class="text-3xl font-bold text-green-600 mt-1">
                        ₱{{ number_format($inventoryValue, 2) }}
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Quick Links</h3>
                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('products.index') }}"
                       class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                        View All Products
                    </a>
                    <a href="{{ route('products.create') }}"
                       class="bg-white border border-brand-600 text-brand-600 hover:bg-brand-50 font-semibold py-2 px-4 rounded transition">
                        + Add Product
                    </a>
                </div>
            </div>

            {{-- Recent Products (NEW!) --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg text-gray-800">Recent Products</h3>
                    <a href="{{ route('products.index') }}"
                       class="text-sm text-brand-600 hover:text-brand-700 font-semibold">
                        View All →
                    </a>
                </div>

                @if($recentProducts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">SKU</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Price</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentProducts as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm"><code class="text-brand-600">{{ $product->sku }}</code></td>
                                        <td class="px-4 py-2 text-sm">{{ $product->name }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="inline-block bg-brand-50 text-brand-700 text-xs px-2 py-1 rounded">
                                                {{ $product->category }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right">₱{{ number_format($product->unit_price, 2) }}</td>
                                        <td class="px-4 py-2 text-sm text-center">
                                            @if($product->quantity == 0)
                                                <span class="text-red-600 font-semibold">{{ $product->quantity }}</span>
                                            @elseif($product->isLowStock())
                                                <span class="text-yellow-600 font-semibold">{{ $product->quantity }}</span>
                                            @else
                                                <span class="text-green-600 font-semibold">{{ $product->quantity }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm py-4 text-center">
                        Wala pang products. <a href="{{ route('products.create') }}" class="text-brand-600 hover:underline">Mag-add ka na!</a>
                    </p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>