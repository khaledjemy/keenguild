<?php

namespace App\Filament\Resources\PricingSettings\Schemas;

use App\Models\PricingSetting;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PricingSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('value')
                    ->label(fn (?PricingSetting $record): string => $record?->key === 'estimate_range_percent' ? 'هامش النطاق (%)' : 'المعامل (من 1 إلى 2)')
                    ->helperText(fn (?PricingSetting $record): string => $record?->key === 'estimate_range_percent'
                        ? 'نسبة الزيادة والنقصان حول التقدير، من 0 إلى 30%. لا تغيّر سعر البداية المنشور.'
                        : 'يُضرب به مجموع سعر الباقة والإضافات للوصول إلى تقدير استرشادي. لا يغيّر سعر البداية المنشور.')
                    ->required()
                    ->numeric()
                    ->minValue(fn (?PricingSetting $record): int => $record?->key === 'estimate_range_percent' ? 0 : 1)
                    ->maxValue(fn (?PricingSetting $record): int => $record?->key === 'estimate_range_percent' ? 30 : 2)
                    ->step(fn (?PricingSetting $record): float => $record?->key === 'estimate_range_percent' ? 1 : 0.05)
                    ->rule(fn (?PricingSetting $record) => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                        if (! $record || ! array_key_exists($record->key, PricingSetting::FACTOR_DEFAULTS)) {
                            return;
                        }

                        $factors = PricingSetting::FACTOR_DEFAULTS;
                        foreach (PricingSetting::query()->whereIn('key', array_keys($factors))->get() as $setting) {
                            $factors[$setting->key] = (float) $setting->value;
                        }
                        $factors[$record->key] = (float) $value;

                        if (str_starts_with($record->key, 'complexity_')
                            && ($factors['complexity_standard'] > $factors['complexity_moderate']
                                || $factors['complexity_moderate'] > $factors['complexity_high'])) {
                            $fail('يجب أن يكون معامل التعقيد العادي ≤ المتوسط ≤ المرتفع.');
                        }
                        if (str_starts_with($record->key, 'urgency_')
                            && $factors['urgency_flexible'] > $factors['urgency_urgent']) {
                            $fail('يجب أن يكون معامل الموعد المرن أقل من أو يساوي المستعجل.');
                        }
                    }),
            ]);
    }
}
