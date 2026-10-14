<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $transactions = $query->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'products'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $types = ['stock_in', 'stock_out', 'adjustment'];

        return view('transactions.create', compact('products', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check sufficient stock for stock_out
        if ($validated['type'] === 'stock_out') {
            $product = Product::find($validated['product_id']);
            if ($product->quantity < $validated['quantity']) {
                return back()->withErrors([
                    'quantity' => "Insufficient stock. Only {$product->quantity} available."
                ])->withInput();
            }
        }

        $validated['user_id'] = Auth::id();

        try {
            InventoryTransaction::create($validated);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction recorded successfully.');
    }

    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(InventoryTransaction $transaction)
    {
        try {
            $transaction->delete(); // Observer will reverse stock change
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted and stock reversed.');
    }

    // ===== Stock Adjustment Methods =====

    public function adjustmentForm()
    {
        $products = Product::orderBy('name')->get();
        return view('transactions.adjustment', compact('products'));
    }

    public function processAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'physical_count' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $difference = $validated['physical_count'] - $product->quantity;

        if ($difference === 0) {
            return back()->withErrors(['physical_count' => 'No difference detected. No adjustment needed.']);
        }

        // Record the adjustment transaction
        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => 'adjustment',
            'quantity' => abs($difference),
            'reference_number' => 'ADJ-' . now()->format('YmdHis'),
            'notes' => $validated['reason'] . ' — ' . ($validated['notes'] ?? '') . " (System: {$product->quantity}, Physical: {$validated['physical_count']})",
        ]);

        // Manually apply the adjustment to set the exact physical count
        $product->update(['quantity' => $validated['physical_count']]);

        return redirect()->route('transactions.index')
            ->with('success', "Adjustment recorded. Quantity changed by " . ($difference > 0 ? '+' : '') . $difference . ".");
    }
}