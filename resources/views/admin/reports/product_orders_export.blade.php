<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Order Summary Export</title>
</head>
<body>

    {{-- A. SUMMARY SECTION --}}
    <table border="1" cellspacing="0" cellpadding="5"
           style="border-collapse:collapse; width:100%; font-family:Arial, sans-serif; font-size:11pt;">
        {{-- Title row (merged) --}}
        <tr>
            <th colspan="2"
                style="background:#1f2933; color:#ffffff; font-weight:bold; text-align:left;">
                A. Summary Section
            </th>
        </tr>

        {{-- Header row: Metric / Value --}}
        <tr style="background:#111827; color:#9ca3af; font-weight:bold;">
            <td style="border:1px solid #4b5563; width:30%;">Metric</td>
            <td style="border:1px solid #4b5563; width:70%;">Value</td>
        </tr>

        {{-- Total Orders --}}
        <tr style="background:#111827; color:#e5e7eb;">
            <td style="border:1px solid #4b5563;">Total Orders</td>
            <td style="border:1px solid #4b5563; text-align:right;">
                {{ number_format($totalOrders) }}
            </td>
        </tr>

        {{-- Total Revenue --}}
        <tr style="background:#111827; color:#e5e7eb;">
            <td style="border:1px solid #4b5563;">Total Revenue</td>
            <td style="border:1px solid #4b5563; text-align:right;">
                RM {{ number_format($totalRevenue, 2) }}
            </td>
        </tr>

        {{-- Top 3 Products --}}
        <tr style="background:#111827; color:#e5e7eb;">
            <td style="border:1px solid #4b5563;">Top 3 Products</td>
            <td style="border:1px solid #4b5563;">
                {{ $top3ProductsList ?: '-' }}
            </td>
        </tr>

        {{-- Average Order Value --}}
        <tr style="background:#111827; color:#e5e7eb;">
            <td style="border:1px solid #4b5563;">Average Order Value</td>
            <td style="border:1px solid #4b5563; text-align:right;">
                RM {{ number_format($averageOrderValue, 2) }}
            </td>
        </tr>
    </table>

    <br>

    {{-- B. DETAILED TABLE --}}
    <table border="1" cellspacing="0" cellpadding="5"
           style="border-collapse:collapse; width:100%; font-family:Arial, sans-serif; font-size:11pt;">
        {{-- Section title --}}
        <tr>
            <th colspan="9"
                style="background:#1f2933; color:#ffffff; font-weight:bold; text-align:left;">
                B. Detailed Table
            </th>
        </tr>

        {{-- Column headers --}}
        <tr style="background:#111827; color:#9ca3af; font-weight:bold; text-align:center;">
            <td style="border:1px solid #4b5563; width:12%;">Order Date</td>
            <td style="border:1px solid #4b5563; width:15%;">Customer</td>
            <td style="border:1px solid #4b5563; width:10%;">State</td>
            <td style="border:1px solid #4b5563; width:13%;">Category</td>
            <td style="border:1px solid #4b5563; width:20%;">Product</td>
            <td style="border:1px solid #4b5563; width:5%;">Qty</td>
            <td style="border:1px solid #4b5563; width:10%;">Unit Price (RM)</td>
            <td style="border:1px solid #4b5563; width:10%;">Subtotal (RM)</td>
            <td style="border:1px solid #4b5563; width:5%;">Currency</td>
        </tr>

        @forelse($orders as $order)
            {{-- Order header row (merged, like "Order #012345") --}}
            <tr style="background:#020617; color:#e5e7eb; font-weight:bold;">
                <td colspan="9" style="border:1px solid #4b5563;">
                    Order #{{ $order->order_number }}
                </td>
            </tr>

            @foreach($order->items as $item)
                <tr style="background:#020617; color:#e5e7eb;">
                    <td style="border:1px solid #4b5563;">
                        {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('Y-m-d') }}
                    </td>
                    <td style="border:1px solid #4b5563;">
                        {{ $order->customer?->name }}
                    </td>
                    <td style="border:1px solid #4b5563;">
                        {{-- If you only have "state", show it as state here --}}
                        {{ $order->customer?->state }}
                    </td>
                    <td style="border:1px solid #4b5563;">
                        {{ $item->product?->category?->name }}
                    </td>
                    <td style="border:1px solid #4b5563;">
                        {{ $item->product?->name }}
                    </td>
                    <td style="border:1px solid #4b5563; text-align:center;">
                        {{ $item->quantity }}
                    </td>
                    <td style="border:1px solid #4b5563; text-align:right;">
                        {{ number_format($item->unit_price, 2) }}
                    </td>
                    <td style="border:1px solid #4b5563; text-align:right;">
                        {{ number_format($item->line_total, 2) }}
                    </td>
                    <td style="border:1px solid #4b5563; text-align:center;">
                        MYR
                    </td>
                </tr>
            @endforeach

            {{-- Order total row --}}
            <tr style="background:#020617; color:#e5e7eb; font-weight:bold;">
                <td colspan="7" style="border:1px solid #4b5563; text-align:right;">
                    Order #{{ $order->order_number }} Total
                </td>
                <td style="border:1px solid #4b5563; text-align:right;">
                    {{ number_format($order->total_amount, 2) }}
                </td>
                <td style="border:1px solid #4b5563; text-align:center;">
                    MYR
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="border:1px solid #4b5563; text-align:center;">
                    No data for selected date range.
                </td>
            </tr>
        @endforelse
    </table>

</body>
</html>
