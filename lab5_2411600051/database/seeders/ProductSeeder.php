<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => '2x4 Pine Stud 8ft', 'sku' => 'LUM-1001', 'category' => 'Lumber', 'quantity' => 250, 'reorder_level' => 50, 'unit_price' => 4.25, 'supplier' => 'Northwest Timber Co.'],
            ['name' => 'Plywood Sheet 4x8 3/4"', 'sku' => 'LUM-1002', 'category' => 'Lumber', 'quantity' => 40, 'reorder_level' => 15, 'unit_price' => 38.99, 'supplier' => 'Northwest Timber Co.'],
            ['name' => 'Pressure-Treated Deck Board', 'sku' => 'LUM-1003', 'category' => 'Lumber', 'quantity' => 8, 'reorder_level' => 20, 'unit_price' => 12.50, 'supplier' => 'Northwest Timber Co.'],
            ['name' => 'Galvanized Wood Screws (1lb box)', 'sku' => 'HW-2001', 'category' => 'Hardware', 'quantity' => 120, 'reorder_level' => 30, 'unit_price' => 8.75, 'supplier' => 'FastenPro Supply'],
            ['name' => 'Door Hinge Set (3-pack)', 'sku' => 'HW-2002', 'category' => 'Hardware', 'quantity' => 60, 'reorder_level' => 20, 'unit_price' => 6.40, 'supplier' => 'FastenPro Supply'],
            ['name' => 'Padlock 40mm', 'sku' => 'HW-2003', 'category' => 'Hardware', 'quantity' => 0, 'reorder_level' => 10, 'unit_price' => 9.99, 'supplier' => 'FastenPro Supply'],
            ['name' => 'Cordless Drill 20V', 'sku' => 'TL-3001', 'category' => 'Tools', 'quantity' => 15, 'reorder_level' => 5, 'unit_price' => 89.99, 'supplier' => 'PowerTool Direct'],
            ['name' => 'Claw Hammer 16oz', 'sku' => 'TL-3002', 'category' => 'Tools', 'quantity' => 45, 'reorder_level' => 10, 'unit_price' => 14.50, 'supplier' => 'PowerTool Direct'],
            ['name' => 'Tape Measure 25ft', 'sku' => 'TL-3003', 'category' => 'Tools', 'quantity' => 4, 'reorder_level' => 15, 'unit_price' => 11.25, 'supplier' => 'PowerTool Direct'],
            ['name' => 'Circular Saw 7-1/4"', 'sku' => 'TL-3004', 'category' => 'Tools', 'quantity' => 9, 'reorder_level' => 5, 'unit_price' => 74.00, 'supplier' => 'PowerTool Direct'],
            ['name' => '12-Gauge Wire (250ft)', 'sku' => 'ELE-4001', 'category' => 'Electrical', 'quantity' => 30, 'reorder_level' => 10, 'unit_price' => 45.60, 'supplier' => 'CircuitWorks'],
            ['name' => 'Duplex Outlet Receptacle', 'sku' => 'ELE-4002', 'category' => 'Electrical', 'quantity' => 3, 'reorder_level' => 15, 'unit_price' => 2.85, 'supplier' => 'CircuitWorks'],
            ['name' => 'LED Shop Light 4ft', 'sku' => 'ELE-4003', 'category' => 'Electrical', 'quantity' => 22, 'reorder_level' => 8, 'unit_price' => 24.99, 'supplier' => 'CircuitWorks'],
            ['name' => 'PVC Pipe 1/2" (10ft)', 'sku' => 'PLB-5001', 'category' => 'Plumbing', 'quantity' => 70, 'reorder_level' => 20, 'unit_price' => 5.10, 'supplier' => 'FlowRight Plumbing Supply'],
            ['name' => 'Ball Valve 3/4"', 'sku' => 'PLB-5002', 'category' => 'Plumbing', 'quantity' => 0, 'reorder_level' => 10, 'unit_price' => 7.85, 'supplier' => 'FlowRight Plumbing Supply'],
            ['name' => 'Kitchen Faucet Standard', 'sku' => 'PLB-5003', 'category' => 'Plumbing', 'quantity' => 6, 'reorder_level' => 5, 'unit_price' => 59.99, 'supplier' => 'FlowRight Plumbing Supply'],
            ['name' => 'Interior Latex Paint (Gallon)', 'sku' => 'PNT-6001', 'category' => 'Paint', 'quantity' => 35, 'reorder_level' => 12, 'unit_price' => 28.75, 'supplier' => 'ColorCraft Paints'],
            ['name' => 'Exterior Primer (Gallon)', 'sku' => 'PNT-6002', 'category' => 'Paint', 'quantity' => 10, 'reorder_level' => 12, 'unit_price' => 22.00, 'supplier' => 'ColorCraft Paints'],
            ['name' => 'Paint Roller Kit', 'sku' => 'PNT-6003', 'category' => 'Paint', 'quantity' => 18, 'reorder_level' => 8, 'unit_price' => 9.40, 'supplier' => 'ColorCraft Paints'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
