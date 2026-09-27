@extends('layouts.marketing')

@section('title', $concept['title'].' — '.($locale === 'ar' ? 'معاينة تصميمية' : 'Design concept'))

@section('content')
<style>
    .demo-surface{background:#fff;border:1px solid var(--line);border-radius:25px;box-shadow:0 24px 60px rgba(16,36,58,.09);padding:clamp(18px,4vw,38px);min-height:340px}.demo-toolbar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:22px}.demo-columns{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.demo-column{background:#f0f6f8;border-radius:17px;padding:16px;min-height:245px}.demo-task{display:block;width:100%;text-align:start;background:#fff;border:1px solid #d5e6eb;border-radius:12px;padding:13px;margin-top:10px;cursor:pointer;color:var(--ink)}.demo-task.is-done{border-color:#12c4ea;background:#e7faff}.demo-products{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.demo-product{border:1px solid var(--line);border-radius:19px;padding:16px}.demo-art{height:150px;border-radius:13px;background:linear-gradient(135deg,#ffe1c7,#f3b690)}.demo-art.cool{background:linear-gradient(135deg,#d5ddff,#a6b8ee)}.demo-product h2{font-size:20px;margin:15px 0 5px}.demo-product p{color:var(--muted);margin:0 0 16px}.demo-chart{height:230px;display:flex;align-items:end;gap:12px;border-bottom:1px solid #b8ccd5;padding:0 12px}.demo-chart i{display:block;flex:1;border-radius:9px 9px 0 0;background:#12c4ea;transition:height .35s ease}.demo-metrics{display:flex;gap:15px;flex-wrap:wrap;margin:22px 0}.demo-metrics span{background:#e8f8fb;border-radius:11px;padding:10px 14px}.demo-status{margin-top:20px;min-height:25px;color:#087e9b;font-weight:700}@media(max-width:650px){.demo-columns,.demo-products{grid-template-columns:1fr}.demo-column{min-height:0}.demo-chart{height:180px}}
</style>
<section class="hero"><div class="shell">
    <a class="eyebrow" href="{{ route('work', ['locale' => $locale]) }}">{{ $locale === 'ar' ? '← الأعمال والتجارب' : '← Work and demos' }}</a>
    <h1>{{ $concept['title'] }}</h1>
    <p>{{ $locale === 'ar' ? $concept['ar'] : $concept['en'] }}</p>
    <div class="notice" style="margin-top:25px;max-width:820px">{{ $locale === 'ar' ? 'معاينة تصميمية ببيانات افتراضية فقط. ليست مشروع عميل منفذًا، ولا تحفظ البيانات أو ترسل طلبات أو تنفذ شراء.' : 'A design concept with fictional data only. This is not a completed client project; it does not save data, send orders, or process purchases.' }}</div>
</div></section>
<section class="section"><div class="shell"><div class="demo-surface" data-demo="{{ $concept['type'] }}">
    <div class="demo-toolbar"><strong>{{ $concept['title'] }}</strong><span class="pill">{{ $locale === 'ar' ? 'تجربة محلية افتراضية' : 'Local fictional preview' }}</span></div>
    @if($slug === 'flowboard')
        <div class="demo-columns">
            <div class="demo-column"><strong>{{ $locale === 'ar' ? 'قيد التخطيط' : 'Planned' }}</strong><button class="demo-task" type="button" aria-pressed="false">{{ $locale === 'ar' ? 'خريطة المنتج' : 'Product map' }}</button><button class="demo-task" type="button" aria-pressed="false">{{ $locale === 'ar' ? 'مراجعة البداية' : 'Review onboarding' }}</button></div>
            <div class="demo-column"><strong>{{ $locale === 'ar' ? 'قيد التنفيذ' : 'In progress' }}</strong><button class="demo-task" type="button" aria-pressed="false">{{ $locale === 'ar' ? 'تصميم لوحة التحكم' : 'Dashboard layout' }}</button><button class="demo-task" type="button" aria-pressed="false">{{ $locale === 'ar' ? 'تجربة الهاتف' : 'Mobile flow' }}</button></div>
            <div class="demo-column"><strong>{{ $locale === 'ar' ? 'جاهز للمراجعة' : 'Ready to review' }}</strong><button class="demo-task" type="button" aria-pressed="false">{{ $locale === 'ar' ? 'مقابلة مستخدم' : 'User interview' }}</button><button class="demo-task" type="button" aria-pressed="false">{{ $locale === 'ar' ? 'نظام التصميم' : 'Design tokens' }}</button></div>
        </div>
        <p class="demo-status" role="status" aria-live="polite">{{ $locale === 'ar' ? 'اضغط على بطاقة لتجرب حالة المراجعة.' : 'Select a card to try its review state.' }}</p>
    @elseif($slug === 'storefront')
        <div class="demo-products">
            <div class="demo-product"><div class="demo-art"></div><h2>{{ $locale === 'ar' ? 'كرسي Line' : 'Line chair' }}</h2><p>{{ $locale === 'ar' ? 'منتج افتراضي' : 'Fictional product' }}</p><button class="button primary demo-add" type="button">{{ $locale === 'ar' ? 'أضف للسلة التجريبية' : 'Add to demo bag' }}</button></div>
            <div class="demo-product"><div class="demo-art cool"></div><h2>{{ $locale === 'ar' ? 'مصباح Orb' : 'Orb lamp' }}</h2><p>{{ $locale === 'ar' ? 'منتج افتراضي' : 'Fictional product' }}</p><button class="button primary demo-add" type="button">{{ $locale === 'ar' ? 'أضف للسلة التجريبية' : 'Add to demo bag' }}</button></div>
        </div>
        <p class="demo-status" role="status" aria-live="polite">{{ $locale === 'ar' ? 'السلة التجريبية: 0 عنصر' : 'Demo bag: 0 items' }}</p>
    @else
        <div class="demo-toolbar"><div class="demo-metrics"><span>{{ $locale === 'ar' ? 'زيارات افتراضية' : 'Sample visits' }} · <b data-visits>2,480</b></span><span>{{ $locale === 'ar' ? 'تسجيلات افتراضية' : 'Sample sign-ups' }} · <b data-signups>186</b></span></div><label>{{ $locale === 'ar' ? 'الفترة التجريبية' : 'Sample period' }} <select class="demo-period"><option value="week">{{ $locale === 'ar' ? 'أسبوع' : 'Week' }}</option><option value="month">{{ $locale === 'ar' ? 'شهر' : 'Month' }}</option></select></label></div>
        <div class="demo-chart" aria-label="{{ $locale === 'ar' ? 'رسم توضيحي ببيانات افتراضية' : 'Chart with fictional data' }}">@foreach([35,48,42,72,62,91,76] as $height)<i style="height:{{ $height }}%"></i>@endforeach</div>
        <p class="demo-status" role="status" aria-live="polite">{{ $locale === 'ar' ? 'بدّل الفترة لترى بيانات توضيحية مختلفة.' : 'Change the period to view different sample data.' }}</p>
    @endif
</div></div></section>
@endsection

@push('scripts')
<script>
(() => {
    const surface = document.querySelector('[data-demo]');
    const status = surface.querySelector('.demo-status');
    const ar = @json($locale === 'ar');
    if (surface.dataset.demo === 'board') {
        surface.querySelectorAll('.demo-task').forEach(button => button.addEventListener('click', () => {
            const selected = button.classList.toggle('is-done');
            button.setAttribute('aria-pressed', String(selected));
            status.textContent = selected ? (ar ? 'تم تحديد البطاقة للمراجعة — تغيير محلي فقط.' : 'Card marked for review — local preview only.') : (ar ? 'عادت البطاقة لحالتها الأولى.' : 'Card returned to its original state.');
        }));
    } else if (surface.dataset.demo === 'store') {
        let count = 0;
        surface.querySelectorAll('.demo-add').forEach(button => button.addEventListener('click', () => {
            count++;
            status.textContent = ar ? `السلة التجريبية: ${count} عنصر — لا يوجد شراء حقيقي.` : `Demo bag: ${count} items — no real purchase.`;
        }));
    } else {
        surface.querySelector('.demo-period').addEventListener('change', event => {
            const month = event.target.value === 'month';
            const heights = month ? [42,65,55,83,74,98,88] : [35,48,42,72,62,91,76];
            surface.querySelectorAll('.demo-chart i').forEach((bar, index) => bar.style.height = `${heights[index]}%`);
            surface.querySelector('[data-visits]').textContent = month ? '9,720' : '2,480';
            surface.querySelector('[data-signups]').textContent = month ? '704' : '186';
            status.textContent = ar ? 'تم تغيير البيانات الافتراضية المعروضة.' : 'Displayed sample data changed.';
        });
    }
})();
</script>
@endpush
