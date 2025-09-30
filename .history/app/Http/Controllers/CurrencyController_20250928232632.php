<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversion;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    protected function convert(Request $request)
    {
        $validated = $request->validate([
            'source_currency' => 'required|string|size:3',
            'target_currency' => 'required|string|size:3',
            'value' => 'required|numeric|min:0',
        ]);

        $apiKey = config('services.fixer.key');

        // Free plan supports only /latest method, /convert is not available.
        $response = Http::get("http://data.fixer.io/api/latest", [
            'access_key' => $apiKey,
            'symbols' => $validated['target_currency'] . ',' . $validated['source_currency']
        ]);

        if (!$response->ok() || !$response->json('success')) {
            Log::error("Fixer API failed", ['response' => $response->json()]);
            return response()->json([
                'error' => 'Failed to fetch exchange rates'
            ], 500);
        }

        $rates = $response->json('rates');

        // Calculate conversion manually
        $rate = $rates[$validated['target_currency']] / $rates[$validated['source_currency']];
        $convertedValue = $validated['value'] * $rate;

        $conversion = Conversion::create([
            'source_currency'  => $validated['source_currency'],
            'target_currency'  => $validated['target_currency'],
            'value'            => $validated['value'],
            'converted_value'  => $convertedValue,
            'rate'             => $rate,
        ]);

        Log::info("Conversion success", [
            'from' => $validated['source_currency'],
            'to' => $validated['target_currency'],
            'amount' => $validated['value'],
            'converted' => $convertedValue,
            'rate' => $rate
        ]);

        return response()->json([
            'message' => 'Conversion successful',
            'data' => $conversion,
        ]);
    }
}