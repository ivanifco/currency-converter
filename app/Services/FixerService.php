<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FixerService
{
    protected string $apiKey;

    const FIXER_URL = 'http://data.fixer.io/api';

    public function __construct()
    {
        $this->apiKey = config('services.fixer.key');
    }

    /**
     * Fetch exchange rates for the given symbols.
     *
     * @param string[] $symbols
     * @return array|null
     */
    public function getRates(array $symbols): ?array
    {
        try {
            $response = Http::get(self::FIXER_URL . "/latest", [
                'access_key' => $this->apiKey,
                'symbols' => implode(',', $symbols),
            ]);

            $data = $response->json();

            // Validate response
            if (!$response->ok() || !isset($data['success']) || !$data['success']) {
                Log::error("Fixer API failed", ['response' => $data]);
                return null;
            }

            return $data['rates'] ?? [];
        } catch (\Throwable $e) {
            Log::error("Fixer API exception", ['message' => $e->getMessage()]);
            return [];
        }
    }
}
