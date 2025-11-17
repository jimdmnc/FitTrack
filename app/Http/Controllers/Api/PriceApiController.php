<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Price;

class PriceApiController extends Controller
{
    public function index()
    {
        $prices = Price::select('id', 'type', 'amount')->get();

        $formatted = $prices->map(function ($price) {
            // Only include relevant membership types
            if (!in_array($price->type, ['weekly', 'monthly', 'annual'])) {
                return null;
            }

            $days = match($price->type) {
                'weekly'  => 7,
                'monthly' => 30,
                'annual'  => 365,
                default   => 0
            };

            return [
                'id'       => $price->id,
                'type'     => $price->type,
                'amount'   => (float) $price->amount,
                'display'  => ucfirst($price->type) . " Membership - ₱" . number_format($price->amount, 0),
                'days'     => $days,
            ];
        })->filter()->values(); // remove nulls and reindex

        return response()->json([
            'success' => true,
            'prices'  => $formatted
        ]);
    }
}