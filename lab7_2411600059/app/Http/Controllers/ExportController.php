<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportProducts()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=products_export.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $products = Product::all();
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'SKU', 'Name', 'Category', 'Quantity', 'Price']);
            foreach ($products as $product) {
                fputcsv($file, [$product->id, $product->sku, $product->name, $product->category, $product->quantity, $product->unit_price]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportPurchaseOrders()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=purchase_orders_export.csv",
        ];
        $orders = PurchaseOrder::with('supplier')->get();
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Ref No', 'Supplier', 'Date', 'Total', 'Status']);
            foreach ($orders as $order) {
                fputcsv($file, [$order->reference_number, $order->supplier->name ?? 'N/A', $order->order_date->format('Y-m-d'), $order->total, $order->status]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // BAGONG PDF EXPORT METHOD
    public function exportPdf()
    {
        $products = Product::all();
        $pdf = Pdf::loadView('exports.products-pdf', compact('products'));
        return $pdf->download('inventory_report.pdf');
    }
}