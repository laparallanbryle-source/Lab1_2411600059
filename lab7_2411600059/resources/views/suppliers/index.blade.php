<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Management') }}
            </h2>
            <a href="{{ route('suppliers.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded transition">
                + Add Supplier
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Search & Filter --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-600 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, contact, or email..." class="w-full border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Status</label>
                        <select name="status" class="border-gray-300 rounded-md text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-brand-600 text-white text-sm py-2 px-4 rounded">Filter</button>
                    <a href="{{ route('suppliers.index') }}" class="text-sm text-gray-500 hover:underline py-2">Reset</a>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-4 py-3 border text-sm">Company Name</th>
                                <th class="px-4 py-3 border text-sm">Contact Person</th>
                                <th class="px-4 py-3 border text-sm">Email & Phone</th>
                                <th class="px-4 py-3 border text-sm text-center">Lead Time</th>
                                <th class="px-4 py-3 border text-sm text-center">Status</th>
                                <th class="px-4 py-3 border text-sm text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border text-sm font-semibold">{{ $supplier->name }}</td>
                                    <td class="px-4 py-3 border text-sm">{{ $supplier->contact_person }}</td>
                                    <td class="px-4 py-3 border text-sm">
                                        {{ $supplier->email }}<br>
                                        <span class="text-gray-500">{{ $supplier->phone }}</span>
                                    </td>
                                    <td class="px-4 py-3 border text-sm text-center">{{ $supplier->lead_time_days }} days</td>
                                    <td class="px-4 py-3 border text-center">
                                        @if($supplier->is_active)
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Active</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border text-center space-x-2 whitespace-nowrap">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-brand-600 hover:underline">View</a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Delete this supplier?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No suppliers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $suppliers->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>