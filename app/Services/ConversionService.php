<?php

namespace App\Services;

class ConversionService
{
    protected $fixer;

    public function __construct(FixerService $fixer)
    {
        $this->fixer = $fixer;
    }

    /**
     * Convert currency value from source to target currency.
     *
     * @param array $data
     * @return array|null [convertedValue, rate] or null on failure
     */
    public function convert(array $data): ? array
    {
        $rates = $this->fixer->getRates([
            $data['target_currency'],
            $data['source_currency'],
        ]);

        if (!$rates) {
            return null;
        }

        $rate = $rates[$data['target_currency']] / $rates[$data['source_currency']];
        $convertedValue = $data['value'] * $rate;

        return [$convertedValue, $rate];
    }
}