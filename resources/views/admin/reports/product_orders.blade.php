@extends('layouts.app') {{-- use your own layout --}}

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Product Order Summary Report</h2>

    {{-- Date filter + export --}}
    <form method="GET" action="{{ route('admin.reports.product-orders.index') }}" class="row mb-4">
        <div class="col-md-3">
            <label for="from" class="form-label">From</label>
            <input type="date" id="from" name="from" class="form-control"
                   value="{{ request('from', $from) }}">
        </div>
        <div class="col-md-3">
            <label for="to" class="form-label">To</label>
            <input type="date" id="to" name="to" class="form-control"
                   value="{{ request('to', $to) }}">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Apply</button>

            <a href="{{ route('admin.reports.product-orders.export', [
                    'from' => request('from', $from),
                    'to'   => request('to', $to),
                ]) }}"
               class="btn btn-success">
               Export Excel
            </a>
        </div>
    </form>

    {{-- 1. Summary cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3>RM {{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Orders</h6>
                    <h3>{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Quantity Sold</h6>
                    <h3>{{ $totalQuantity }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Top Product</h6>
                    @if($topProduct)
                        <h5 class="mb-1">{{ $topProduct->product->name }}</h5>
                        <p class="mb-0 small text-muted">
                            Qty: {{ $topProduct->total_qty }}
                        </p>
                    @else
                        <p class="mb-0">-</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Detailed table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Order Details</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th colspan="3" class="text-center">Order Info</th>
                            <th colspan="2" class="text-center">Customer Info</th>
                            <th colspan="3" class="text-center">Product Info</th>
                            <th colspan="3" class="text-center">Metrics</th>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <th>Order #</th>
                            <th>Category</th>

                            <th>Customer</th>
                            <th>State</th>

                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>

                            <th>Line Total</th>
                            <th>Row Total</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $row)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($row->order_date)->format('Y-m-d') }}</td>
                                <td>{{ $row->order_number }}</td>
                                <td>{{ $row->category_name }}</td>

                                <td>{{ $row->customer_name }}</td>
                                <td>{{ $row->customer_state }}</td>

                                <td>{{ $row->product_name }}</td>
                                <td>{{ number_format($row->unit_price, 2) }}</td>
                                <td>{{ $row->quantity }}</td>

                                <td>{{ number_format($row->line_total, 2) }}</td>
                                <td>{{ number_format($row->line_total, 2) }}</td>
                                <td>MYR</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">
                                    No data for selected date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
