<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Product Name</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded px-3 py-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full border rounded px-3 py-2" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Category</label>
                            <select name="category" class="w-full border rounded px-3 py-2" required>
                                <option value="Lumber" {{ old('category', $product->category) == 'Lumber' ? 'selected' : '' }}>Lumber</option>
                                <option value="Hardware" {{ old('category', $product->category) == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="Tools" {{ old('category', $product->category) == 'Tools' ? 'selected' : '' }}>Tools</option>
                                <option value="Electrical" {{ old('category', $product->category) == 'Electrical' ? 'selected' : '' }}>Electrical</option>
                                <option value="Plumbing" {{ old('category', $product->category) == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                                <option value="Paint" {{ old('category', $product->category) == 'Paint' ? 'selected' : '' }}>Paint</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Quantity</label>
                                <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}" min="0" class="w-full border rounded px-3 py-2" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Reorder Level</label>
                                <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" min="0" class="w-full border rounded px-3 py-2" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Unit Price (₱)</label>
                            <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" min="0" class="w-full border rounded px-3 py-2" required>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-bold mb-2">Supplier</label>
                            <input type="text" name="supplier" value="{{ old('supplier', $product->supplier) }}" class="w-full border rounded px-3 py-2">
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                Update Product
                            </button>
                            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>