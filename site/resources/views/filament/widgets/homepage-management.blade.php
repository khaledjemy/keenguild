<x-filament-widgets::widget>
    <div class="kg-dashboard" dir="rtl">
        <div class="kg-dashboard-hero">
            <div>
                <span class="kg-dashboard-eyebrow">نظرة عامة</span>
                <h2>إدارة الموقع تبدأ من هنا</h2>
                <p>حالة النشر والطلبات التي تحتاج متابعة، من بيانات الموقع الفعلية.</p>
            </div>
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">معاينة الموقع ↗</a>
        </div>

        <div class="kg-dashboard-grid">
            <section class="kg-dashboard-panel" aria-labelledby="site-status-title">
                <div class="kg-dashboard-panel-heading">
                    <h3 id="site-status-title">حالة التشغيل</h3>
                    <span class="kg-dashboard-badge {{ $intakeEnabled ? 'is-ready' : '' }}">{{ $intakeStatus }}</span>
                </div>
                <p>حالة نموذج طلب المشروع على الموقع.</p>
                <a class="kg-dashboard-settings-link" href="{{ \App\Filament\Pages\SiteSettings::getUrl() }}">تعديل إعدادات التشغيل ←</a>
            </section>

            <section class="kg-dashboard-panel" aria-labelledby="attention-title">
                <div class="kg-dashboard-panel-heading"><h3 id="attention-title">يحتاج إجراء</h3><span class="kg-dashboard-count">{{ count($actions) }}</span></div>
                @forelse ($actions as $action)
                    <a class="kg-dashboard-action" href="{{ $action['url'] }}"><span><strong>{{ $action['title'] }}</strong><small>{{ $action['detail'] }}</small></span><span aria-hidden="true">↖</span></a>
                @empty
                    <p class="kg-dashboard-empty">لا توجد مهام معلّقة حاليًا.</p>
                @endforelse
            </section>
        </div>

        <section class="kg-dashboard-panel kg-dashboard-recent" aria-labelledby="recent-title">
            <div class="kg-dashboard-panel-heading"><h3 id="recent-title">أحدث طلبات المشاريع</h3><a href="{{ \App\Filament\Resources\QuoteRequests\QuoteRequestResource::getUrl('index') }}">عرض الكل ←</a></div>
            @forelse ($recentRequests as $request)
                <a class="kg-dashboard-request" href="{{ \App\Filament\Resources\QuoteRequests\QuoteRequestResource::getUrl('edit', ['record' => $request]) }}"><strong>{{ $request->name }}</strong><span>{{ match ($request->status) { 'new' => 'جديد', 'reviewing' => 'قيد المراجعة', 'contacted' => 'تم التواصل', 'closed' => 'مغلق', default => 'غير محدد' } }}</span><time datetime="{{ $request->created_at?->toIso8601String() }}">{{ $request->created_at?->diffForHumans() }}</time></a>
            @empty
                <p class="kg-dashboard-empty">لا توجد طلبات مشاريع حتى الآن.</p>
            @endforelse
        </section>
    </div>
    <style>
        .kg-dashboard{display:grid;gap:1rem;font-family:inherit}.kg-dashboard-hero{display:flex;align-items:center;justify-content:space-between;gap:1.5rem;padding:1.6rem 1.8rem;border-radius:1.25rem;background:linear-gradient(115deg,#082439,#0b4b5d);color:#fff}.kg-dashboard-eyebrow{color:#7be2ed;font-size:.75rem;font-weight:700}.kg-dashboard-hero h2{font-size:1.4rem;font-weight:800;margin:.25rem 0}.kg-dashboard-hero p{color:#c3e1e7;font-size:.85rem}.kg-dashboard-hero>a{flex:none;background:#fff;color:#083549;padding:.65rem 1rem;border-radius:.7rem;font-size:.8rem;font-weight:700}.kg-dashboard-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.kg-dashboard-panel{background:#fff;border:1px solid #dbe5e9;border-radius:1rem;padding:1.2rem 1.35rem;box-shadow:0 2px 10px #0d334408}.kg-dashboard-panel-heading{display:flex;align-items:center;justify-content:space-between;gap:.7rem;margin-bottom:.85rem}.kg-dashboard-panel h3{font-size:1rem;font-weight:800;color:#112f40}.kg-dashboard-panel p{font-size:.85rem;color:#40576a;margin:.3rem 0}.kg-dashboard-settings-link{display:inline-block;margin-top:.55rem;color:#0788a4;font-size:.78rem;font-weight:700}.kg-dashboard-badge{font-size:.72rem;font-weight:700;color:#9a570b;background:#fff3dc;border-radius:99px;padding:.32rem .65rem}.kg-dashboard-badge.is-ready{color:#087447;background:#dcf7e9}.kg-dashboard-count{background:#e1f5f8;color:#087c92;border-radius:99px;padding:.2rem .55rem;font-size:.75rem;font-weight:800}.kg-dashboard-action,.kg-dashboard-request{display:flex;align-items:center;justify-content:space-between;gap:.8rem;border-top:1px solid #e9eff1;padding:.65rem 0;color:#11374a}.kg-dashboard-action:hover,.kg-dashboard-request:hover{color:#0891b2}.kg-dashboard-action strong,.kg-dashboard-action small{display:block}.kg-dashboard-action strong,.kg-dashboard-request strong{font-size:.83rem}.kg-dashboard-action small,.kg-dashboard-request span,.kg-dashboard-request time{font-size:.73rem;color:#718693}.kg-dashboard-empty{padding:.8rem 0}.kg-dashboard-panel-heading>a{color:#0788a4;font-size:.78rem;font-weight:700}.kg-dashboard-request time{margin-inline-start:auto}@media(max-width:700px){.kg-dashboard-grid{grid-template-columns:1fr}.kg-dashboard-hero{align-items:flex-start;flex-direction:column;padding:1.2rem}.kg-dashboard-panel{padding:1rem}}
        .dark .kg-dashboard-panel{background:#172633;border-color:#314656}.dark .kg-dashboard-panel h3,.dark .kg-dashboard-action,.dark .kg-dashboard-request{color:#f1f7f9}.dark .kg-dashboard-panel p{color:#b8ccd5}.dark .kg-dashboard-action,.dark .kg-dashboard-request{border-color:#314656}
    </style>
</x-filament-widgets::widget>
