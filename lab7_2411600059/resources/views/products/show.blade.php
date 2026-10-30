<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Product Name</h3>
                            <p class="text-gray-900">{{ $product->name }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">SKU</h3>
                            <p class="text-gray-900">{{ $product->sku }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Category</h3>
                            <p class="text-gray-900">{{ $product->category }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Supplier</h3>
                            <p class="text-gray-900">{{ $product->supplier ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Quantity</h3>
                            <p class="text-gray-900">{{ $product->quantity }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Reorder Level</h3>
                            <p class="text-gray-900">{{ $product->reorder_level }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Unit Price</h3>
                            <p class="text-gray-900">₱{{ number_format($product->unit_price, 2) }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Status</h3>
                            <p>
                                @if($product->quantity == 0)
                                    <span class="bg-red-100 text-red-800 text-sm font-semibold px-3 py-1 rounded">Out of Stock</span>
                                @elseif($product->isLowStock())
                                    <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded">Low Stock</span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded">In Stock</span>
                                @endif
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-lg font-bold text-gray-700 mb-1">Description</h3>
                            <p class="text-gray-900">{{ $product->description ?? 'No description available.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>