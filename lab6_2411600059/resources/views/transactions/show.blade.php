<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Details') }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-brand-600 hover:underline text-sm font-semibold">
                ← Back to Products
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Product Info --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500"><code class="text-brand-600">{{ $product->sku }}</code></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('products.edit', $product) }}"
                           class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold py-2 px-4 rounded">
                            Edit
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 px-4 rounded">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Category</dt>
                        <dd class="font-semibold">{{ $product->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Supplier</dt>
                        <dd class="font-semibold">{{ $product->supplier ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Unit Price</dt>
                        <dd class="font-semibold">₱{{ number_format($product->unit_price, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Stock Status</dt>
                        <dd>
                            @if($product->isOutOfStock())
                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Out of Stock</span>
                            @elseif($product->isLowStock())
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">Low Stock</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">In Stock</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Current Quantity</dt>
                        <dd class="text-2xl font-bold text-brand-600">{{ $product->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Reorder Level</dt>
                        <dd class="font-semibold">{{ $product->reorder_level }}</dd>
                    </div>
                    @if($product->description)
                    <div class="md:col-span-2">
                        <dt class="text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-700">{{ $product->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Transaction History --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Transaction History</h3>

                @if($product->transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-3 border text-sm">Date</th>
                                    <th class="px-4 py-3 border text-sm text-center">Type</th>
                                    <th class="px-4 py-3 border text-sm text-center">Change</th>
                                    <th class="px-4 py-3 border text-sm text-center">Running Balance</th>
                                    <th class="px-4 py-3 border text-sm">Reference</th>
                                    <th class="px-4 py-3 border text-sm">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningBalance = $product->quantity;
                                    $sortedTransactions = $product->transactions()->recent()->get();
                                @endphp

                                @foreach($sortedTransactions as $txn)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border text-sm">
                                            {{ $txn->created_at->format('M d, Y h:i A') }}
                                        </td>
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
                                            @if($txn->type === 'stock_in')
                                                <span class="text-green-600">+{{ $txn->quantity }}</span>
                                            @elseif($txn->type === 'stock_out')
                                                <span class="text-red-600">-{{ $txn->quantity }}</span>
                                            @else
                                                <span class="text-yellow-600">±{{ $txn->quantity }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border text-center font-semibold text-brand-600">
                                            {{ $runningBalance }}
                                        </td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->reference_number ?? '—' }}</td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->user->name ?? 'N/A' }}</td>
                                    </tr>

                                    @php
                                        // Compute running balance BEFORE this transaction (moving backwards)
                                        if ($txn->type === 'stock_in') {
                                            $runningBalance -= $txn->quantity;
                                        } elseif ($txn->type === 'stock_out') {
                                            $runningBalance += $txn->quantity;
                                        }
                                        // Note: adjustments are tricky — for simplicity, we'll leave them
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm py-4 text-center">No transactions yet for this product.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>