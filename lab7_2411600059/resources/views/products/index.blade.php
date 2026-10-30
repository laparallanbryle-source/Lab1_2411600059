<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Products') }}
            </h2>
            <a href="{{ route('products.create') }}"
               class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                + Add Product
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-3 border">SKU</th>
                                    <th class="px-4 py-3 border">Name</th>
                                    <th class="px-4 py-3 border">Category</th>
                                    <th class="px-4 py-3 border text-right">Unit Price</th>
                                    <th class="px-4 py-3 border text-center">Quantity</th>
                                    <th class="px-4 py-3 border text-center">Status</th>
                                    <th class="px-4 py-3 border text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border"><code class="text-brand-600">{{ $product->sku }}</code></td>
                                        <td class="px-4 py-3 border">{{ $product->name }}</td>
                                        <td class="px-4 py-3 border">
                                            <span class="inline-block bg-brand-50 text-brand-700 text-xs px-2 py-1 rounded">
                                                {{ $product->category }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 border text-right">₱{{ number_format($product->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 border text-center">{{ $product->quantity }}</td>
                                        <td class="px-4 py-3 border text-center">
                                            @if($product->quantity == 0)
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Out of Stock</span>
                                            @elseif($product->isLowStock())
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Low Stock</span>
                                            @else
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">In Stock</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border text-center space-x-2 whitespace-nowrap">
                                            <a href="{{ route('products.show', $product) }}" class="text-brand-600 hover:underline">View</a>
                                            <a href="{{ route('products.edit', $product) }}" class="text-green-600 hover:underline">Edit</a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $product->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                            Walang products pa. <a href="{{ route('products.create') }}" class="text-brand-600 hover:underline">Mag-add ka na!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>