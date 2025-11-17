<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductOrderSummaryController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $ordersQuery = Order::whereBetween('order_date', [
            $from->toDateString(),
            $to->toDateString(),
        ]);

        // 1) Summary cards
        $totalOrders   = (clone $ordersQuery)->count();
        $totalRevenue  = (clone $ordersQuery)->sum('total_amount');
        $orderIds      = (clone $ordersQuery)->pluck('id');

        $totalQuantity = OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

        $topProduct = OrderItem::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereIn('order_id', $orderIds)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->first();

        // 2) Detailed joined table
        $details = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.order_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('orders.order_date')
            ->orderBy('orders.id')
            ->select([
                'orders.order_date',
                'orders.order_number',
                'customers.name as customer_name',
                'customers.state as customer_state',
                'categories.name as category_name',
                'products.name as product_name',
                'order_items.unit_price',
                'order_items.quantity',
                'order_items.line_total',
            ])
            ->get();

        return view('admin.reports.product_orders', [
            'from'          => $from->toDateString(),
            'to'            => $to->toDateString(),
            'totalOrders'   => $totalOrders,
            'totalRevenue'  => $totalRevenue,
            'totalQuantity' => $totalQuantity,
            'topProduct'    => $topProduct,
            'details'       => $details,
        ]);
    }

    // 3) Export to Excel
    public function export(Request $request)
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->toDateString()
            : now()->subDays(30)->toDateString();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->toDateString()
            : now()->toDateString();

        // Eager Load everything we need -> NO N+1
        $orders = Order::with(['customer', 'items.product.category'])
            ->whereBetween('order_date', [$from, $to])
            ->orderBy('order_date')
            ->orderBy('id')
            ->get();

        // ---- SUMMARY METRICS ----
        $totalOrders   = $orders->count();
        $totalRevenue  = $orders->sum('total_amount');

        $allItems      = $orders->flatMap->items;
        $totalQuantity = $allItems->sum('quantity');

        // Top 3 products by quantity
        $groupedByProduct = $allItems
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'product'    => $items->first()->product,
                    'total_qty'  => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total_qty')
            ->take(3);

        $top3ProductsList = $groupedByProduct
            ->map(fn($row) => $row['product']->name)
            ->implode(', ');

        $averageOrderValue = $totalOrders > 0
            ? $totalRevenue / $totalOrders
            : 0;

        $fileName = "product-order-summary-{$from}-{$to}.xls";

        // Render HTML (Excel will open this as a spreadsheet)
        $content = view('admin.reports.product_orders_export', [
            'from'              => $from,
            'to'                => $to,
            'orders'            => $orders,
            'totalOrders'       => $totalOrders,
            'totalRevenue'      => $totalRevenue,
            'totalQuantity'     => $totalQuantity,
            'top3ProductsList'  => $top3ProductsList,
            'averageOrderValue' => $averageOrderValue,
        ])->render();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}