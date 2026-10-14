<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     * Auto-update product quantity after a transaction is saved.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = Product::lockForUpdate()->find($transaction->product_id);

            if (!$product) {
                throw new \Exception('Product not found.');
            }

            $newQuantity = $product->quantity;

            if ($transaction->type === 'stock_in') {
                $newQuantity += $transaction->quantity;
            } elseif ($transaction->type === 'stock_out') {
                if ($product->quantity < $transaction->quantity) {
                    throw new \Exception('Insufficient stock for this transaction.');
                }
                $newQuantity -= $transaction->quantity;
            } elseif ($transaction->type === 'adjustment') {
                $newQuantity += $transaction->quantity;
            }

            if ($newQuantity < 0) {
                throw new \Exception('Stock cannot go below zero.');
            }

            $product->update(['quantity' => $newQuantity]);

            // Send low stock notification if product is now low/out of stock
            if ($product->isLowStock() || $product->isOutOfStock()) {
                $users = User::all();
                foreach ($users as $user) {
                    $user->notify(new LowStockNotification($product));
                }
            }
        });
    }

    /**
     * Handle the InventoryTransaction "deleted" event.
     * Reverse the stock change when a transaction is deleted.
     */
    public function deleted(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = Product::lockForUpdate()->find($transaction->product_id);

            if (!$product) {
                return;
            }

            $reversedQuantity = $product->quantity;

            if ($transaction->type === 'stock_in') {
                $reversedQuantity -= $transaction->quantity;
            } elseif ($transaction->type === 'stock_out') {
                $reversedQuantity += $transaction->quantity;
            } elseif ($transaction->type === 'adjustment') {
                $reversedQuantity -= $transaction->quantity;
            }

            $product->update(['quantity' => max(0, $reversedQuantity)]);
        });
    }
}