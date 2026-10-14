<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => '2x4 Pine Lumber (8ft)',
                'sku' => 'LUM-2408',
                'description' => 'Standard construction grade pine lumber',
                'category' => 'Lumber',
                'quantity' => 150,
                'reorder_level' => 30,
                'unit_price' => 285.00,
                'supplier' => 'Northern Wood Supply',
            ],
            [
                'name' => 'Plywood 1/2 inch (4x8)',
                'sku' => 'LUM-PLY12',
                'description' => 'Construction plywood sheet',
                'category' => 'Lumber',
                'quantity' => 45,
                'reorder_level' => 20,
                'unit_price' => 890.00,
                'supplier' => 'Northern Wood Supply',
            ],
            [
                'name' => 'Galvanized Nails 3 inch (1kg)',
                'sku' => 'HW-NAIL3',
                'description' => 'Heavy duty galvanized nails',
                'category' => 'Hardware',
                'quantity' => 8,
                'reorder_level' => 15,
                'unit_price' => 175.00,
                'supplier' => 'Metro Hardware Inc.',
            ],
            [
                'name' => 'Carriage Bolts M10x100 (pack of 20)',
                'sku' => 'HW-BOLT10',
                'description' => 'Stainless steel carriage bolts',
                'category' => 'Hardware',
                'quantity' => 25,
                'reorder_level' => 10,
                'unit_price' => 320.00,
                'supplier' => 'Metro Hardware Inc.',
            ],
            [
                'name' => 'Claw Hammer 16oz',
                'sku' => 'TL-HAM16',
                'description' => 'Professional claw hammer with fiberglass handle',
                'category' => 'Tools',
                'quantity' => 18,
                'reorder_level' => 5,
                'unit_price' => 450.00,
                'supplier' => 'ToolMaster PH',
            ],
            [
                'name' => 'Adjustable Wrench 10 inch',
                'sku' => 'TL-WR10',
                'description' => 'Chrome vanadium adjustable wrench',
                'category' => 'Tools',
                'quantity' => 12,
                'reorder_level' => 5,
                'unit_price' => 380.00,
                'supplier' => 'ToolMaster PH',
            ],
            [
                'name' => 'Electric Drill 600W',
                'sku' => 'TL-DRILL600',
                'description' => 'Corded electric drill with variable speed',
                'category' => 'Tools',
                'quantity' => 3,
                'reorder_level' => 5,
                'unit_price' => 1850.00,
                'supplier' => 'PowerTools Depot',
            ],
            [
                'name' => 'THHN Wire 2.0mm (100m roll)',
                'sku' => 'EL-WIRE20',
                'description' => 'Copper THHN electrical wire',
                'category' => 'Electrical',
                'quantity' => 22,
                'reorder_level' => 10,
                'unit_price' => 1250.00,
                'supplier' => 'ElectroSupply Co.',
            ],
            [
                'name' => 'Circuit Breaker 20A',
                'sku' => 'EL-CB20',
                'description' => 'Single pole circuit breaker',
                'category' => 'Electrical',
                'quantity' => 35,
                'reorder_level' => 15,
                'unit_price' => 185.00,
                'supplier' => 'ElectroSupply Co.',
            ],
            [
                'name' => 'LED Bulb 9W (Daylight)',
                'sku' => 'EL-LED9',
                'description' => 'Energy saving LED bulb',
                'category' => 'Electrical',
                'quantity' => 0,
                'reorder_level' => 20,
                'unit_price' => 95.00,
                'supplier' => 'BrightLight PH',
            ],
            [
                'name' => 'PVC Pipe 1/2 inch (3m)',
                'sku' => 'PL-PVC12',
                'description' => 'Schedule 40 PVC pipe',
                'category' => 'Plumbing',
                'quantity' => 60,
                'reorder_level' => 20,
                'unit_price' => 85.00,
                'supplier' => 'PipeMaster Inc.',
            ],
            [
                'name' => 'Ball Valve 1/2 inch',
                'sku' => 'PL-VALVE12',
                'description' => 'Brass ball valve',
                'category' => 'Plumbing',
                'quantity' => 14,
                'reorder_level' => 10,
                'unit_price' => 220.00,
                'supplier' => 'PipeMaster Inc.',
            ],
            [
                'name' => 'Teflon Tape (roll)',
                'sku' => 'PL-TEFLON',
                'description' => 'Plumbing thread seal tape',
                'category' => 'Plumbing',
                'quantity' => 7,
                'reorder_level' => 15,
                'unit_price' => 25.00,
                'supplier' => 'PipeMaster Inc.',
            ],
            [
                'name' => 'Latex Paint White 4L',
                'sku' => 'PT-LATEX4',
                'description' => 'Interior latex paint',
                'category' => 'Paint',
                'quantity' => 28,
                'reorder_level' => 10,
                'unit_price' => 680.00,
                'supplier' => 'ColorCoat PH',
            ],
            [
                'name' => 'Paint Roller Set',
                'sku' => 'PT-ROLLER',
                'description' => '9 inch roller with tray',
                'category' => 'Paint',
                'quantity' => 16,
                'reorder_level' => 8,
                'unit_price' => 195.00,
                'supplier' => 'ColorCoat PH',
            ],
            [
                'name' => 'Exterior Paint Red 4L',
                'sku' => 'PT-EXTRED',
                'description' => 'Weather resistant exterior paint',
                'category' => 'Paint',
                'quantity' => 4,
                'reorder_level' => 8,
                'unit_price' => 920.00,
                'supplier' => 'ColorCoat PH',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}