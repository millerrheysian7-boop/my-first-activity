<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->status === 'draft'
            && ($purchaseOrder->created_by === $user->id || $user->role === 'admin');
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->status === 'draft' && $user->role === 'admin';
    }

    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return in_array($user->role, ['manager', 'admin']) && $purchaseOrder->canBeApproved();
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return in_array($user->role, ['warehouse', 'admin']) && $purchaseOrder->canBeReceived();
    }
}
