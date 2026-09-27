@extends('layouts.marketing')
@section('title', $locale === 'ar' ? 'اطلب عرض سعر' : 'Request a quote')
@section('content')
<style>
    .quote-form{max-width:760px;display:grid;gap:18px}.quote-form label{display:grid;gap:7px;font-weight:700;font-size:14px}.quote-form input,.quote-form textarea,.quote-form select{width:100%;padding:12px 14px;border:1px solid #b8cfda;border-radius:11px;background:#fff;color:#10243a}.quote-form textarea{min-height:150px;resize:vertical}.quote-form input:focus,.quote-form textarea:focus,.quote-form select:focus{outline:3px solid #a9edfa;border-color:#12c4ea}.quote-form .check{display:flex;align-items:flex-start;gap:10px;line-height:1.8}.quote-form .check input{width:auto;margin-top:7px}.error{color:#b42330;font-size:13px;font-weight:600}.quote-form .honeypot{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}
</style>
<section class="hero"><div class="shell">
    <span class="eyebrow">{{ $locale === 'ar' ? 'نبدأ بفهم مشروعك' : 'Start with your project' }}</span>
    <h1>{{ $locale === 'ar' ? 'احكِ لنا ما الذي تريد بناءه.' : 'Tell us what you want to build.' }}</h1>
    <p>{{ $locale === 'ar' ? 'صف الهدف والوظائف المهمة، وسنراجع النطاق قبل تقديم أي سعر نهائي. إرسال الطلب لا يُنشئ التزامًا بالتنفيذ أو بسعر معيّن.' : 'Describe the goal and key features. We review the scope before any final price. Submitting a request does not create a commitment to a price or delivery.' }}</p>
</div></section>
<section class="section"><div class="shell">
    @if(!$enabled)
        <div class="notice">{{ $locale === 'ar' ? 'استقبال طلبات عرض السعر عبر الموقع غير مُفعّل بعد. سنفتحه بعد اعتماد معلومات التواصل وسياسة الخصوصية.' : 'Online quote requests are not enabled yet. They will open after contact details and the privacy policy are approved.' }}</div>
    @elseif(session('quote_success'))
        <div class="notice" role="status">{{ $locale === 'ar' ? 'تم حفظ طلبك. سنراجعه عبر لوحة الإدارة ثم نتواصل معك بشأن الخطوة التالية.' : 'Your request was saved. We will review it before getting in touch about the next step.' }}</div>
    @else
        <form class="quote-form card" method="post" action="{{ route('quote.store', ['locale' => $locale]) }}">
            @csrf
            <label>{{ $locale === 'ar' ? 'الاسم' : 'Name' }}<input name="name" value="{{ old('name') }}" required minlength="2" maxlength="120" autocomplete="name">@error('name')<span class="error">{{ $message }}</span>@enderror</label>
            <label>{{ $locale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}<input type="email" name="email" value="{{ old('email') }}" required maxlength="254" autocomplete="email">@error('email')<span class="error">{{ $message }}</span>@enderror</label>
            <label>{{ $locale === 'ar' ? 'رقم الهاتف (اختياري)' : 'Phone (optional)' }}<input type="tel" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="tel">@error('phone')<span class="error">{{ $message }}</span>@enderror</label>
            <label>{{ $locale === 'ar' ? 'الباقة الأقرب لفكرتك (اختياري)' : 'Preferred package (optional)' }}<select name="package_id"><option value="">{{ $locale === 'ar' ? 'غير محددة بعد' : 'Not sure yet' }}</option>@foreach($packages as $package)<option value="{{ $package->id }}" @selected((string)old('package_id', request('package')) === (string)$package->id)>{{ $locale === 'ar' ? $package->name_ar : $package->name_en }}</option>@endforeach</select>@error('package_id')<span class="error">{{ $message }}</span>@enderror</label>
            <label>{{ $locale === 'ar' ? 'وصف المشروع' : 'Project brief' }}<textarea name="project_brief" required minlength="30" maxlength="4000" placeholder="{{ $locale === 'ar' ? 'ما الهدف؟ من العملاء؟ وما الصفحات أو الوظائف المهمة؟' : 'What is the goal, audience, and key pages or features?' }}">{{ old('project_brief') }}</textarea>@error('project_brief')<span class="error">{{ $message }}</span>@enderror</label>
            <div class="honeypot" aria-hidden="true"><label>Company website<input name="company_website" tabindex="-1" autocomplete="off"></label></div>
            <label class="check"><input type="checkbox" name="privacy_consent" value="1" required @checked(old('privacy_consent'))><span>{{ $locale === 'ar' ? 'أوافق على استخدام بياناتي للتواصل بخصوص هذا الطلب، وقد قرأت' : 'I agree to the use of my details to respond to this request and have read the' }} <a href="{{ $privacyUrl }}" target="_blank" rel="noopener noreferrer" style="color:#078cb0;text-decoration:underline">{{ $locale === 'ar' ? 'سياسة الخصوصية' : 'privacy policy' }}</a>.</span></label>
            @error('privacy_consent')<span class="error">{{ $message }}</span>@enderror
            <button class="button primary" type="submit">{{ $locale === 'ar' ? 'أرسل طلب العرض' : 'Send quote request' }}</button>
            <span class="small">{{ $locale === 'ar' ? 'لا تضع كلمات مرور أو بيانات دفع أو معلومات حساسة في وصف المشروع.' : 'Do not include passwords, payment details, or sensitive information in the brief.' }}</span>
        </form>
    @endif
</div></section>
@endsection
