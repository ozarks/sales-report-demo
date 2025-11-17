<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Customer, Category, Product, Order, OrderItem};
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class DemoSalesSeeder extends Seeder
{
    public function run(): void
    {
        // Customers
        $customers = collect([
            ['name' => 'Acme Sdn Bhd',   'email' => 'acme@example.com',  'state' => 'Klang'],
            ['name' => 'Beta Trading',   'email' => 'beta@example.com',  'state' => 'Shah Alam'],
            ['name' => 'Gamma Retail',   'email' => 'gamma@example.com', 'state' => 'Kuala Lumpur'],
        ])->map(fn ($data) => Customer::create($data));

        // Categories
        $categories = collect(['Electronics', 'Groceries', 'Office Supplies'])
            ->map(fn ($name) => Category::create(['name' => $name]));

        // Products
        $products = collect();
        foreach ($categories as $cat) {
            for ($i = 1; $i <= 5; $i++) {
                $products->push(Product::create([
                    'category_id' => $cat->id,
                    'name'        => $cat->name . " Item $i",
                    'unit_price'  => rand(20, 400),
                ]));
            }
        }

        // Orders across last 60 days
        for ($i = 1; $i <= 80; $i++) {
            $customer  = $customers->random();
            $orderDate = Carbon::now()->subDays(rand(0, 60))->toDateString();

            $order = Order::create([
                'customer_id'  => $customer->id,
                'order_date'   => $orderDate,
                'order_number' => 'ORD-' . Str::upper(Str::random(6)),
                'total_amount'  => 0,
            ]);

            $itemsCount = rand(1, 5);
            $grandTotal = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $product = $products->random();
                $qty     = rand(1, 10);
                $price   = $product->unit_price;
                $line    = $qty * $price;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'line_total' => $line,
                ]);

                $grandTotal += $line;
            }

            $order->update(['total_amount' => $grandTotal]);
        }
    }
}