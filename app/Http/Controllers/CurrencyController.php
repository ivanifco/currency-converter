<?php

namespace App\Http\Controllers;

use App\Services\ConversionService;
use App\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    private $conversionService;

    public function __construct(ConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    public function convert(Request $request)
    {
        $validated = $request->validate([
            'source_currency' => 'required|string|size:3',
            'target_currency' => 'required|string|size:3',
            'value' => 'required|numeric|min:0',
        ]);

        try {
            list($conversion, $rate) = $this->conversionService->convert($validated);
        } catch (\Throwable $e) {
            Log::error("Conversion error", ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to fetch exchange rates',
            ], 500);
        }

        // If conversion or rate is null, treat it as API failure
        if (is_null($conversion) || is_null($rate)) {
            return response()->json([
                'error' => 'Failed to fetch exchange rates',
            ], 500);
        }

        $validated['converted_value'] = $conversion;
        $validated['rate'] = $rate;

        // create() throws an exception on failure, so no try-catch for that.
        $updated = Conversion::create($validated);

        Log::info("Conversion success", $updated->toArray());

        return response()->json([
            'message' => 'Conversion successful',
            'data' => $updated,
        ]);
    }
}
