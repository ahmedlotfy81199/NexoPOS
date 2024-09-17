<?php
use App\Models\Order;
use App\Classes\Hook;
use Illuminate\Support\Facades\View;
?>

<div class="w-full h-full">
    <div class="w-full md:w-1/2 lg:w-1/3 shadow-lg bg-white rounded-lg p-4 mx-auto border border-gray-300">
        <!-- Header Section with Store Name & Logo -->
        <div class="text-center mb-4">
            <div class="text-xl font-bold text-gray-900 tracking-wide">{{ __('Simplified Tax Invoice') }}</div>
            <div class="mt-2">
                @if (empty(ns()->option->get('ns_invoice_receipt_logo')))
                    <h3 class="text-lg font-semibold">{{ ns()->option->get('ns_store_name') }}</h3>
                @else
                    <img src="{{ ns()->option->get('ns_invoice_receipt_logo') }}"
                        alt="{{ ns()->option->get('ns_store_name') }}" class="mx-auto h-16">
                @endif
            </div>
        </div>

        <!-- Store and Invoice Details -->
        <div class="text-center text-sm ">
            <div class="font-medium">{{ __('Invoice Number') }}: {{ $order->code }}</div>
            <div>{{ ns()->option->get('ns_store_address') }}</div>
            <div>{{ __('Date') }}: {{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</div>
            <div>{{ __('VAT Registration Number') }}: {{ ns()->option->get('ns_store_pobox') }}</div>
        </div>

        <!-- Order Columns for Details -->
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex flex-wrap -mx-2 text-sm">
                <div class="px-2 w-1/2">
                    {!! nl2br($ordersService->orderTemplateMapping('ns_invoice_receipt_column_a', $order)) !!}
                </div>
                <div class="px-2 w-1/2">
                    {!! nl2br($ordersService->orderTemplateMapping('ns_invoice_receipt_column_b', $order)) !!}
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="w-full">
            <table class="w-full text-sm table-auto mb-4">
                <thead class="bg-gray-200">
                    <tr class="text-left">
                        <th class="p-2 border-b border-gray-300">{{ __('Product') }}</th>
                        <th class="p-2 border-b border-gray-300">{{ __('Quantity') }}</th>
                        <th class="p-2 border-b border-gray-300">{{ __('Unit Price') }}</th>
                        <th class="p-2 border-b border-gray-300">{{ __('Vat Amount') }}</th>
                        <th class="p-2 border-b border-gray-300 text-right">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach (Hook::filter('ns-receipt-products', $order->combinedProducts) as $product)
                        <tr class="hover:bg-gray-100">
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2">{{ $product->quantity }}</td>
                            <td class="p-2">{{ ns()->currency->define($product->unit_price) }}</td>
                            <td class="p-2">{{ ns()->currency->define($product->tax_value / $product->quantity) }}
                            </td>
                            <td class="p-2 text-right">{{ ns()->currency->define($product->total_price_with_tax) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <!-- Subtotal, Discount, Coupons, Taxes, Shipping -->
                    <tr>
                        <td colspan="4" class="p-2">{{ __('Sub Total') }}</td>
                        <td class="p-2 text-right">{{ ns()->currency->define($order->subtotal) }}</td>
                    </tr>

                    @if ($order->discount > 0)
                        <tr>
                            <td colspan="4" class="p-2">{{ __('Discount') }}
                                @if ($order->discount_type === 'percentage')
                                    ({{ $order->discount_percentage }}%)
                                @endif
                            </td>
                            <td class="p-2 text-right">{{ ns()->currency->define($order->discount) }}</td>
                        </tr>
                    @endif

                    @if ($order->total_coupons > 0)
                        <tr>
                            <td colspan="4" class="p-2">{{ __('Coupons') }}</td>
                            <td class="p-2 text-right">{{ ns()->currency->define($order->total_coupons) }}</td>
                        </tr>
                    @endif

                    <!-- Tax Breakdown -->
                    @if (ns()->option->get('ns_invoice_display_tax_breakdown') === 'yes')
                        @foreach ($order->taxes as $tax)
                            <tr>
                                <td colspan="4" class="p-2">{{ $tax->tax_name }} ({{ __('Tax') }})</td>
                                <td class="p-2 text-right">{{ ns()->currency->define($tax->tax_value) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Tax Breakdown -->
                    @if (ns()->option->get('ns_invoice_display_tax_breakdown') === 'yes')
                        @foreach ($order->taxes as $tax)
                            <tr>
                                <td colspan="4" class="p-2">{{ $tax->tax_name }} ({{ __('Tax') }})</td>
                                <td class="p-2 text-right">{{ ns()->currency->define($tax->tax_value) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="p-2">{{ __('Taxes') }}</td>
                            <td class="p-2 text-right"> {{ ns()->currency->define($order->tax_value) }}</td>

                        </tr>
                    @endif

                    @if ($order->shipping > 0)
                        <tr>
                            <td colspan="4" class="p-2">{{ __('Shipping') }}</td>
                            <td class="p-2 text-right">{{ ns()->currency->define($order->shipping) }}</td>
                        </tr>
                    @endif

                    <!-- Total -->
                    <tr class="text-lg bg-gray-200 text-gray-800">
                        <td colspan="4" class="p-2">{{ __('Total') }}</td>
                        <td class="p-2 text-right">{{ ns()->currency->define($order->total) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Details -->
        <div class="w-full mb-4">
            <table class="w-full text-sm">
                @foreach ($order->payments as $payment)
                    <tr>
                        <td colspan="4" class="p-2">
                            {{ $paymentTypes[$payment['identifier']] ?? __('Unknown Payment') }}</td>
                        <td class="p-2 text-right">{{ ns()->currency->define($payment['value']) }}</td>
                    </tr>
                @endforeach

                <tr class="text-lg">
                    <td colspan="4" class="p-2">{{ __('Paid') }}</td>
                    <td class="p-2 text-right">{{ ns()->currency->define($order->tendered) }}</td>
                </tr>

                @if ($order->payment_status === Order::PAYMENT_PAID)
                    <tr>
                        <td colspan="4" class="p-2">{{ __('Change') }}</td>
                        <td class="p-2 text-right">{{ ns()->currency->define($order->change) }}</td>
                    </tr>
                @elseif($order->payment_status === Order::PAYMENT_PARTIALLY)
                    <tr>
                        <td colspan="4" class="p-2">{{ __('Due') }}</td>
                        <td class="p-2 text-right">{{ ns()->currency->define(abs($order->change)) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Optional Note Section -->
        @if ($order->note_visibility === 'visible')
            <div class="p-4 bg-yellow-50 text-center text-sm rounded-lg mb-4">
                <strong class="text-yellow-600">{{ __('Note:') }}</strong> {{ $order->note }}
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center text-gray-500 text-sm">
            {{ ns()->option->get('ns_invoice_receipt_footer') }}
        </div>

        <div class="flex justify-center text-gray-500 text-sm ">
            <div class="text-center">
                {!! $qrCode !!}
            </div>
        </div>
    </div>
</div>

<!-- Auto Print Feature -->
@includeWhen(request()->query('autoprint') === 'true', '/pages/dashboard/orders/templates/_autoprint')
