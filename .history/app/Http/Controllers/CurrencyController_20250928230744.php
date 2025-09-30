<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversion;

class CurrencyController extends Controller
{
    public function convert(Request $request)
    {
        $validated = $request->validate([
            'source_currency' => 'required|string|size:3',
            'target_currency' => 'required|string|size:3',
            'value' => 'required|numeric|min:0',
        ]);

        $apiKey = config('services.fixer.key');

        // Call Fixer's convert API
        $response = Http::post("http://data.fixer.io/api/convert", [
            'access_key' => $apiKey,
            'from' => $validated['source_currency'],
            'to' => $validated['target_currency'],
            'amount' => $validated['value'],
        ]);

        if (!$response->ok() || !$response->json('success')) {
            return response()->json([
                'error' => 'Failed to fetch data from endpoint.'
            ], 500);
        }

        // Fixer’s /convert returns the result directly
        $rate = $response->json('info.rate'); 
        $convertedValue = $response->json('result');

        $conversion = Conversion::create([
            'source_currency' => $validated['source_currency'],
            'target_currency' => $validated['target_currency'],
            'value' => $validated['value'],
            'converted_value' => $convertedValue,
            'rate' => $rate,
        ]);

        return response()->json([
            'message' => 'Conversion successful',
            'data' => $conversion,
        ]);
    }
}