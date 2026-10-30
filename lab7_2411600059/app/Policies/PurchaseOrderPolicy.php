<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, PurchaseOrder $purchaseOrder): bool { return true; }
    public function create(User $user): bool { return true; }
    
    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->status === 'draft' && ($user->id === $purchaseOrder->created_by || $user->id === 1);
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->status === 'draft' && $user->id === 1;
    }

    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // I-assume natin na admin (ID 1) or manager pwedeng mag-approve
        return $user->id === 1; 
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return in_array($purchaseOrder->status, ['approved', 'sent']);
    }
}