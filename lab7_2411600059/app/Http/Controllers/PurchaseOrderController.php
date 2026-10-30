<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Mail\PurchaseOrderApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'createdBy'])->latest();
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('supplier_id')) { $query->where('supplier_id', $request->supplier_id); }
        $purchaseOrders = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::active()->get();
        return view('purchase_orders.index', compact('purchaseOrders', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->get();
        $products = Product::orderBy('name')->get();
        return view('purchase_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $po = PurchaseOrder::create([
                    'reference_number' => 'PO-' . strtoupper(Str::random(8)),
                    'supplier_id' => $validated['supplier_id'],
                    'status' => 'draft',
                    'order_date' => $validated['order_date'],
                    'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]);
                foreach ($validated['items'] as $item) {
                    $po->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity_ordered' => $item['quantity_ordered'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
                $po->calculateTotal();
            });
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'createdBy', 'approvedBy']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('update', $purchaseOrder);
        $suppliers = Supplier::active()->get();
        $products = Product::orderBy('name')->get();
        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($request->has('status') && $request->status === 'pending') {
            $purchaseOrder->update(['status' => 'pending']);
            return back()->with('success', 'Order submitted for approval.');
        }
        $this->authorize('update', $purchaseOrder);
        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('delete', $purchaseOrder);
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder);
        if ($purchaseOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Order is not pending approval.']);
        }
        $purchaseOrder->update(['status' => 'approved', 'approved_by' => Auth::id()]);
        try {
            Mail::to('supplier@example.com')->send(new PurchaseOrderApproved($purchaseOrder));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email failed: ' . $e->getMessage());
        }
        return back()->with('success', 'Purchase order approved and email sent.');
    }

    // BAGONG REJECT METHOD
    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve', $purchaseOrder); 
        if ($purchaseOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Order is not pending approval.']);
        }
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        
        $purchaseOrder->update([
            'status' => 'cancelled',
            'notes' => $purchaseOrder->notes . "\n[REJECTED]: " . $request->rejection_reason
        ]);
        
        return back()->with('success', 'Purchase order rejected.');
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update(['status' => 'sent']);
        return back()->with('success', 'Purchase order marked as sent to supplier.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('receive', $purchaseOrder);
        if (!$purchaseOrder->canBeReceived()) {
            return back()->withErrors(['error' => 'Order cannot be received.']);
        }
        try {
            DB::transaction(function () use ($purchaseOrder) {
                foreach ($purchaseOrder->items as $item) {
                    $item->update(['quantity_received' => $item->quantity_ordered]);
                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'user_id' => Auth::id(),
                        'type' => 'stock_in',
                        'quantity' => $item->quantity_ordered,
                        'reference_number' => $purchaseOrder->reference_number,
                        'notes' => 'Received from PO',
                    ]);
                }
                $purchaseOrder->update(['status' => 'received']);
            });
            return back()->with('success', 'Order received and stock updated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}