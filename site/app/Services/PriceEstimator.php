<?php

namespace App\Services;

use App\Models\Package;
use InvalidArgumentException;

class PriceEstimator
{
    /**
     * @param  array<string, int>  $quantities  Option code => selected quantity.
     * @return array{base:int,extras:array<int,array{code:string,label:string,quantity:int,total:int}>,complexity:float,urgency:float,total:int,low:int,high:int}
     */
    public function estimate(Package $package, array $quantities, float $complexity = 1.0, float $urgency = 1.0, string $locale = 'ar', bool $includeDraftOptions = false, float $rangeMargin = 0.1): array
    {
        if ($package->pricing_mode !== 'estimate' || $package->base_price_egp === null) {
            throw new InvalidArgumentException('This package requires a custom quote.');
        }

        if ($complexity < 1 || $complexity > 2 || $urgency < 1 || $urgency > 2) {
            throw new InvalidArgumentException('Invalid pricing factor.');
        }
        if ($rangeMargin < 0 || $rangeMargin > 0.3) {
            throw new InvalidArgumentException('Invalid estimate range margin.');
        }

        $package->loadMissing('options');
        $options = ($includeDraftOptions ? $package->options : $package->options->where('published', true))->keyBy('code');
        $base = (int) $package->base_price_egp;
        $extras = [];
        $extraTotal = 0;

        foreach ($quantities as $code => $quantity) {
            $option = $options->get($code);
            if (! $option || ! is_int($quantity) || $quantity < 1 || $quantity > $option->max_quantity) {
                throw new InvalidArgumentException('Invalid option or quantity.');
            }

            if ($option->calculation_type === 'fixed' && $quantity !== 1) {
                throw new InvalidArgumentException('A fixed option can only be selected once.');
            }

            $amount = match ($option->calculation_type) {
                'fixed' => (int) $option->amount_egp,
                'per_unit' => (int) $option->amount_egp * $quantity,
                'percent' => (int) round($base * $option->percent / 100) * $quantity,
                default => throw new InvalidArgumentException('Unknown option calculation type.'),
            };
            $extras[] = [
                'code' => $code,
                'label' => $locale === 'en' ? $option->label_en : $option->label_ar,
                'quantity' => $quantity,
                'total' => $amount,
            ];
            $extraTotal += $amount;
        }

        $total = (int) (round((($base + $extraTotal) * $complexity * $urgency) / 500) * 500);

        return [
            'base' => $base,
            'extras' => $extras,
            'complexity' => $complexity,
            'urgency' => $urgency,
            'total' => $total,
            'low' => max($base, (int) (round(($total * (1 - $rangeMargin)) / 500) * 500)),
            'high' => (int) (round(($total * (1 + $rangeMargin)) / 500) * 500),
        ];
    }
}
