<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentType;
use App\Services\DateService;
use App\Services\Options;
use App\Services\OrdersService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\View;

class PublicReceiptController extends Controller
{
    private $paymentTypes;

    public function __construct(
        private OrdersService $ordersService,
        private Options $optionsService,
        protected DateService $dateService
    ) {
        $this->middleware(function ($request, $next) {
            /**
             * @todo must be refactored
             */
            $this->paymentTypes = PaymentType::orderBy('priority', 'asc')
                ->active()
                ->get()
                ->map(function ($payment, $index) {
                    $payment->selected = $index === 0;

                    return $payment;
                });

            return $next($request);
        });
    }
    public function publicReceipt($id)
    {
        // Retrieve the order by ID
        $order = Order::findOrFail($id);

        $order->load('customer');
        $order->load('products');
        $order->load('shipping_address');
        $order->load('billing_address');
        $order->load('user');

        $receiptUrl = route('receipt.public', ['id' => $order->id]);

        $qrCode = QrCode::size(100)->generate($receiptUrl);

        return View::make('pages.dashboard.orders.templates._naked_receipt', [
            'order' => $order,
            'title' => sprintf(__('Order Receipt &mdash; %s'), $order->code),
            'optionsService' => $this->optionsService,
            'ordersService' => $this->ordersService,
            'paymentTypes' => collect($this->paymentTypes)->mapWithKeys(function ($payment) {
                return [$payment['identifier'] => $payment['label']];
            }),
            'qrCode' => $qrCode,
        ]);

    }
}
