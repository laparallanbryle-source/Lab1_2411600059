<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Dashboard') }}
            </h2>
            <span class="text-xs text-gray-500" id="lastUpdated">
                Last updated: {{ now()->format('h:i:s A') }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-brand-600">
                    <div class="text-sm text-gray-500">Total Products</div>
                    <div class="text-3xl font-bold text-brand-600 mt-1" id="stat-total">{{ $totalProducts }}</div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-yellow-500">
                    <div class="text-sm text-gray-500">Low Stock</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-1" id="stat-low">{{ $lowStock }}</div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-red-500">
                    <div class="text-sm text-gray-500">Out of Stock</div>
                    <div class="text-3xl font-bold text-red-600 mt-1" id="stat-out">{{ $outOfStock }}</div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-green-500">
                    <div class="text-sm text-gray-500">Inventory Value</div>
                    <div class="text-3xl font-bold text-green-600 mt-1" id="stat-value">
                        ₱{{ number_format($inventoryValue, 2) }}
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Quick Links</h3>
                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('products.index') }}" class="btn-brand">View All Products</a>
                    <a href="{{ route('products.create') }}" class="btn-brand-outline">+ Add Product</a>
                    <a href="{{ route('transactions.index') }}" class="btn-brand-outline">Transactions</a>
                    <a href="{{ route('purchase-orders.index') }}" class="btn-brand-outline">Purchase Orders</a>
                </div>
            </div>

            {{-- PROCUREMENT WIDGETS (Bago para sa Lab 7) --}}
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 text-lg">Procurement Overview</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- Widget 1: Pending Orders Count --}}
                    <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                        <div class="bg-yellow-100 text-yellow-600 rounded-full h-12 w-12 flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Pending Purchase Orders</div>
                            <div class="text-2xl font-bold text-gray-800">{{ $widgetPendingOrders ?? 0 }}</div>
                        </div>
                    </div>

                    {{-- Widget 3: Reorder Alerts --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h4 class="font-semibold text-gray-800 mb-3 text-sm">Reorder Alerts (Low Stock)</h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse($widgetLowStock ?? [] as $product)
                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded">
                                    {{ $product->name }} ({{ $product->quantity }} left)
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">All stock levels are healthy.</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Widget 2: Recent Orders --}}
                    <div class="bg-white shadow-sm rounded-lg p-6 md:col-span-2">
                        <h4 class="font-semibold text-gray-800 mb-3 text-sm">Recent Purchase Orders</h4>
                        <ul class="divide-y divide-gray-100">
                            @forelse($widgetRecentOrders ?? [] as $order)
                                <li class="py-2 flex justify-between items-center text-sm">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-brand-600">{{ $order->reference_number }}</span>
                                        <span class="text-xs text-gray-500">{{ $order->supplier->name ?? 'N/A' }}</span>
                                    </div>
                                    <span class="text-xs font-semibold px-2 py-1 rounded 
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $order->status === 'received' ? 'bg-brand-100 text-brand-800' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </li>
                            @empty
                                <li class="py-2 text-sm text-gray-500">No recent orders.</li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            </div>
            {{-- END OF PROCUREMENT WIDGETS --}}

            {{-- Recent Products --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg text-gray-800">Recent Products</h3>
                    <a href="{{ route('products.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-semibold">
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
                                            @if($product->isOutOfStock())
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
                    <p class="text-gray-500 text-sm py-4 text-center">No products yet.</p>
                @endif
            </div>

        </div>
    </div>

    {{-- AJAX Polling Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const POLL_INTERVAL = 30000; // 30 seconds

            async function fetchStats() {
                try {
                    const res = await fetch('/api/dashboard/stats', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    animateNumber('stat-total', data.total_products);
                    animateNumber('stat-low', data.low_stock);
                    animateNumber('stat-out', data.out_of_stock);

                    const valueEl = document.getElementById('stat-value');
                    if (valueEl) {
                        valueEl.textContent = '₱' + Number(data.inventory_value).toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }

                    const updatedEl = document.getElementById('lastUpdated');
                    if (updatedEl) updatedEl.textContent = 'Last updated: ' + data.last_updated;

                } catch (err) {
                    console.warn('Stats fetch failed:', err);
                }
            }

            function animateNumber(id, newValue) {
                const el = document.getElementById(id);
                if (!el) return;
                const oldValue = parseInt(el.textContent) || 0;
                if (oldValue === newValue) return;

                el.style.transition = 'color 0.3s';
                el.style.color = '#6D57D6';
                el.textContent = newValue;
                setTimeout(() => { el.style.color = ''; }, 500);
            }

            setInterval(fetchStats, POLL_INTERVAL);
        });
    </script>
</x-app-layout>