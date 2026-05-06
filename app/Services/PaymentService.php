<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process a fake payment using predefined card numbers.
     */
    public function processFakePayment(Order $order, string $cardNumber): string
    {
        $normalizedCard = preg_replace('/\D+/', '', $cardNumber) ?? '';

        Log::info('Processing fake payment.', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'card_last4' => substr($normalizedCard, -4),
        ]);

        if ($normalizedCard === '1111111111111111') {
            return 'completed';
        }

        if ($normalizedCard === '0000000000000000') {
            return 'failed';
        }

        return 'failed';
    }
}
