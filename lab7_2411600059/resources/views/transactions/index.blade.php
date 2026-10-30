<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Transactions') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('transactions.adjustment') }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition">
                    Stock Adjustment
                </a>
                <a href="{{ route('transactions.create') }}"
                   class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                    + New Transaction
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Filter --}}
            <div class="bg-white shadow-sm rounded-lg p-4 mb-4">
                <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Type</label>
                        <select name="type" class="border-gray-300 rounded-md text-sm">
                            <option value="">All Types</option>
                            <option value="stock_in" {{ request('type') === 'stock_in' ? 'selected' : '' }}>Stock In</option>
                            <option value="stock_out" {{ request('type') === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Product</label>
                        <select name="product_id" class="border-gray-300 rounded-md text-sm">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm py-2 px-4 rounded">
                        Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:underline py-2">Reset</a>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="px-4 py-3 border">Date</th>
                                    <th class="px-4 py-3 border">Product</th>
                                    <th class="px-4 py-3 border text-center">Type</th>
                                    <th class="px-4 py-3 border text-center">Qty</th>
                                    <th class="px-4 py-3 border">Reference</th>
                                    <th class="px-4 py-3 border">User</th>
                                    <th class="px-4 py-3 border text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $txn)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border text-sm">
                                            {{ $txn->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-4 py-3 border text-sm">
                                            {{ $txn->product->name ?? 'N/A' }}
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
                                            {{ $txn->type === 'stock_out' ? '-' : '+' }}{{ $txn->quantity }}
                                        </td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->reference_number ?? '—' }}</td>
                                        <td class="px-4 py-3 border text-sm">{{ $txn->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 border text-center space-x-2 whitespace-nowrap">
                                            <a href="{{ route('transactions.show', $txn) }}" class="text-brand-600 hover:underline">View</a>
                                            <form action="{{ route('transactions.destroy', $txn) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this transaction? Stock will be reversed.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                            No transactions yet. <a href="{{ route('transactions.create') }}" class="text-brand-600 hover:underline">Create one!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>