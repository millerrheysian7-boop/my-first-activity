<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;

class InventoryTransactionObserver
{
    
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Lock the product row for update to avoid race conditions
            // if two transactions are processed at nearly the same time.
            $product = $transaction->product()->lockForUpdate()->first();

            if (! $product) {
                return;
            }

            $newQuantity = $product->quantity + $transaction->quantity;

            if ($newQuantity < 0) {
                throw new \RuntimeException(
                    "Stock update rejected: resulting quantity for '{$product->name}' would be negative."
                );
            }

            $product->quantity = $newQuantity;
            $product->save();

            if ($product->isLowStock() || $product->isOutOfStock()) {
                $this->notifyLowStock($product);
            }
        });
    }

    protected function notifyLowStock($product): void
    {
        $admins = User::all();

        $alreadyNotified = $admins->first()
            ?->notifications()
            ->where('type', LowStockNotification::class)
            ->whereNull('read_at')
            ->get()
            ->contains(function ($n) use ($product) {
                return ($n->data['product_id'] ?? null) === $product->id;
            });

        if ($alreadyNotified) {
            return;
        }

        foreach ($admins as $admin) {
            $admin->notify(new LowStockNotification($product));
        }
    }
}
