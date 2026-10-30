<?php

namespace App\Console\Commands;

use App\Services\ReorderService;
use Illuminate\Console\Command;

class CheckReorderPoints extends Command
{
    // Ito yung command na ite-type natin sa terminal
    protected $signature = 'inventory:check-reorder';
    protected $description = 'Check inventory levels and automatically generate purchase orders for low stock items';

    public function handle(ReorderService $reorderService)
    {
        $this->info('Checking inventory levels for reordering...');
        
        // Tatawagin natin yung ginawa nating service kanina
        $generatedPOs = $reorderService->generatePurchaseOrders();

        if (empty($generatedPOs)) {
            $this->info('No products need reordering at this time.');
        } else {
            $this->info('Successfully generated Purchase Orders: ' . implode(', ', $generatedPOs));
        }

        return Command::SUCCESS;
    }
}