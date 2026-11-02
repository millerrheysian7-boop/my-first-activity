<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PurchaseOrdersGeneratedNotification;
use App\Services\ReorderService;
use Illuminate\Console\Command;

class CheckReorderPoints extends Command
{
    protected $signature = 'inventory:check-reorder';

    protected $description = 'Check all products against their reorder points and generate draft purchase orders as needed.';

    public function handle(ReorderService $reorderService): int
    {
        $this->info('Checking reorder points...');

        $orders = $reorderService->generatePurchaseOrders();

        if (count($orders) === 0) {
            $this->info('No products currently need reordering.');

            return self::SUCCESS;
        }

        $this->info(count($orders) . ' draft purchase order(s) generated:');
        foreach ($orders as $order) {
            $this->line(' - ' . $order->reference_number . ' (' . $order->supplier->company_name . ')');
        }

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PurchaseOrdersGeneratedNotification($orders));
        }

        return self::SUCCESS;
    }
}
