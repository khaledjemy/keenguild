<details class="estimate-details">
    <summary>{{ $locale === 'ar' ? 'احسب نطاق السعر لمشروعك' : 'Estimate your project range' }} <span aria-hidden="true">↗</span></summary>
    <form class="estimate-form" data-estimate-url="{{ $preview ? route('preview.pricing.estimate', ['locale' => $locale, 'package' => $package->id]) : route('pricing.estimate', ['locale' => $locale, 'package' => $package->id]) }}">
        <h4>{{ $locale === 'ar' ? 'تقدير مبدئي' : 'A starting estimate' }}</h4>
        @foreach($package->options as $option)
            <label class="estimate-option"><input type="checkbox" data-option="{{ $option->code }}"><span>{{ $locale === 'ar' ? $option->label_ar : $option->label_en }} @if($option->calculation_type === 'percent')(+{{ $option->percent }}%)@else(+{{ number_format($option->amount_egp) }} {{ $locale === 'ar' ? 'ج.م' : 'EGP' }})@endif</span>@if($option->calculation_type !== 'fixed' && $option->max_quantity > 1)<input type="number" min="1" max="{{ $option->max_quantity }}" value="1" data-quantity="{{ $option->code }}" aria-label="{{ $locale === 'ar' ? 'العدد' : 'Quantity' }}">@endif</label>
        @endforeach
        <div class="estimate-selects"><label>{{ $locale === 'ar' ? 'التعقيد' : 'Complexity' }}<select name="complexity"><option value="standard">{{ $locale === 'ar' ? 'عادي' : 'Standard' }}</option><option value="moderate">{{ $locale === 'ar' ? 'متوسط' : 'Moderate' }}</option><option value="high">{{ $locale === 'ar' ? 'مرتفع' : 'High' }}</option></select></label><label>{{ $locale === 'ar' ? 'الوقت' : 'Timing' }}<select name="urgency"><option value="flexible">{{ $locale === 'ar' ? 'مرن' : 'Flexible' }}</option><option value="urgent">{{ $locale === 'ar' ? 'مستعجل' : 'Urgent' }}</option></select></label></div>
        <button class="button primary" type="submit">{{ $locale === 'ar' ? 'احسب التقدير' : 'Calculate estimate' }}</button>
        <div class="estimate-output" role="status" aria-live="polite"></div>
        <span class="small">{{ $locale === 'ar' ? 'الناتج غير ملزم ويحتاج مراجعة المتطلبات.' : 'This is not a binding quote and requires scope review.' }}</span>
    </form>
</details>
