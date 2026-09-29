<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ContactChannel;
use App\Models\HomepageContent;
use App\Models\HomepageHero;
use App\Models\HomepageVideo;
use App\Models\LegalPage;
use App\Models\NavigationItem;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Services\HomeLocalization;
use App\Services\InquiryAvailability;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegacyHomeController
{
    public function english(InquiryAvailability $availability, HomeLocalization $localizer): Response
    {
        return $this->index($availability, $localizer, 'en');
    }

    public function previewHero(InquiryAvailability $availability, HomeLocalization $localizer, string $locale, HomepageHero $hero): Response
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return $this->index($availability, $localizer, $locale, $hero);
    }

    public function index(InquiryAvailability $availability, HomeLocalization $localizer, string $locale = 'ar', ?HomepageHero $previewHero = null): Response
    {
        $source = base_path('../dist/index.html');
        abort_unless(is_file($source), 503, 'The homepage build is unavailable.');

        $html = file_get_contents($source);
        $original = $html;
        $html = $this->applyAgentConfiguration($html);
        $mobileSectionFix = <<<'HTML'
<style id="keenguild-mobile-sections">
@media (max-width: 767px) {
  html { scroll-snap-type:none !important; }
  html, body { overflow-x:clip !important; overscroll-behavior-x:none; }
  main > section { height:auto !important; min-height:100dvh !important; overflow:visible !important; display:flex !important; align-items:flex-start !important; padding-top:92px !important; padding-bottom:24px !important; scroll-snap-align:start; }
  main > section > .max-w-7xl { width:100%; }
  main > section h2 { font-size:2rem !important; line-height:1.08 !important; margin-top:.55rem !important; }
  main > section p { font-size:.8rem !important; line-height:1.5 !important; }
  #proof .proof-intro { margin-bottom:10px !important; gap:8px !important; }
  #proof .proof-intro > p { min-height:0 !important; max-height:none !important; overflow:visible !important; }
  #proof .proof-grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; grid-auto-rows:auto !important; height:auto !important; max-height:none !important; overflow:visible !important; }
  #proof article { height:auto !important; min-height:190px !important; padding:15px !important; }
  #proof article h3 { font-size:.9rem !important; margin-top:14px !important; }
  #services .grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; gap:8px !important; }
  #services article { min-height:210px !important; padding:16px !important; }
  #services article h3 { font-size:1rem !important; margin-top:10px !important; }
  #demos > .max-w-7xl > div:first-child, #work > .max-w-7xl > div:first-child, #journal > .max-w-7xl > div:first-child { margin-bottom:10px !important; gap:8px !important; }
  #demos > .max-w-7xl > .grid { grid-template-columns:minmax(0,1fr) !important; gap:10px !important; }
  #demos article { min-height:164px !important; height:auto !important; padding:9px !important; border-radius:16px !important; display:grid !important; grid-template-columns:minmax(0,42%) minmax(0,1fr); gap:10px; overflow:visible; }
  #demos article > div:first-child { height:auto !important; min-width:0; padding:9px !important; border-radius:11px !important; overflow:hidden; }
  #demos article > div:last-child { min-width:0; padding:4px !important; display:flex; flex-direction:column; justify-content:center; }
  #demos article > div:first-child .mt-7, #demos article > div:first-child .mt-8 { margin-top:9px !important; }
  #demos article > div:first-child .h-24, #demos article > div:first-child .h-28 { height:66px !important; }
  #demos article h3 { font-size:.95rem !important; }
  #demos article p { display:block; overflow:visible; margin-top:4px !important; }
  #demos article button { font-size:.7rem !important; padding:7px 9px !important; }
  #work .grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; gap:8px !important; }
  #work article { min-height:390px !important; height:auto !important; padding:12px !important; border-radius:17px !important; }
  #work.home-managed article > .home-cover img, #work.home-managed article > .home-cover .home-cover-fallback { height:145px !important; }
  #work article > div:first-child, #work article h3, #work article p, #work article .home-actions { font-size:.75rem !important; }
  #journal .grid { grid-template-columns:repeat(3,minmax(0,1fr)) !important; gap:7px !important; }
  #journal article { min-height:0 !important; padding:10px !important; border-radius:14px !important; }
  #journal article h3 { font-size:.85rem !important; }
  #journal .journal-image { height:120px !important; }
}
@media (max-width: 480px) {
  #demos article { grid-template-columns:minmax(0,1fr) !important; }
  #demos article > div:first-child { min-height:150px; }
  #demos article > div:last-child { display:block !important; }
}
@media (max-width: 420px) {
  #proof .proof-grid { grid-template-columns:minmax(0,1fr) !important; }
}
</style>
HTML;
        $html = str_replace('</head>', '<link rel="canonical" href="'.e(route('home')).'" />'.$mobileSectionFix.'</head>', $html);
        $contactIntro = '<p class="text-lg leading-8 mb-6">احكِ لنا ببساطة عن المنتج أو الموقع الذي تفكر فيه، ونرجع لك بخطوة واضحة.</p>';
        $contactButton = '<button onclick="openContactPreview()" class="w-full bg-ink text-white rounded-xl py-4 font-bold hover:bg-white hover:text-ink transition">ابدأ محادثة قصيرة ↗</button>';
        if (Schema::hasTable('contact_channels') && Schema::hasTable('legal_pages') && $availability->enabled('ar') && $availability->enabled('en')) {
            $introAr = 'احكِ لنا ببساطة عن المنتج أو الموقع الذي تفكر فيه، ونرجع لك بخطوة واضحة.';
            $introEn = 'Tell us about the product or website you have in mind, and we will respond with a clear next step.';
            $button = '<a href="/ar/request-quote" data-home-route="request-quote" data-home-ar="ابدأ مشروعك ↗" data-home-en="Start a project ↗" class="w-full inline-flex items-center justify-center bg-ink text-white rounded-xl py-4 font-bold hover:bg-white hover:text-ink transition">ابدأ مشروعك ↗</a>';
        } else {
            $introAr = 'هذه معاينة لشكل بدء المشروع فقط؛ لا تُرسل الطلبات من هذا النموذج حاليًا.';
            $introEn = 'This is a preview of the inquiry flow; this form does not send requests yet.';
            $button = '<button onclick="openContactPreview()" data-home-ar="عاين نموذج التواصل ↗" data-home-en="Preview contact form ↗" class="w-full bg-ink text-white rounded-xl py-4 font-bold hover:bg-white hover:text-ink transition">عاين نموذج التواصل ↗</button>';
        }
        $intro = '<p class="text-lg leading-8 mb-6" data-home-ar="'.e($introAr).'" data-home-en="'.e($introEn).'">'.e($introAr).'</p>';
        $html = str_replace([$contactIntro, $contactButton], [$intro, $button], $html);
        $hero = $previewHero ?? (Schema::hasTable('homepage_heroes') ? HomepageHero::query()->where('published', true)->first() : null);
        if ($hero) {
            $html = $this->applyHomepageHero($html, $hero);
        }
        $html = str_replace(
            '<span class="w-2 h-2 rounded-full bg-lime-600"></span>استوديو رقمي من القاهرة إلى العالم',
            '<span class="w-2 h-2 rounded-full bg-lime-600"></span><span data-home-ar="استوديو رقمي نبني حول هدفك" data-home-en="A digital studio built around your goal">استوديو رقمي نبني حول هدفك</span>',
            $html,
        );
        $html = str_replace(
            '<span>القاهرة · نعمل مع فرق طموحة</span>',
            '<span data-home-ar="نعمل مع فرق طموحة" data-home-en="Working with ambitious teams">نعمل مع فرق طموحة</span>',
            $html,
        );
        $html = str_replace('© 2026 KeenGuild · القاهرة', '© '.date('Y').' KeenGuild', $html);
        $html = str_replace('© 2026 KeenGuild', '© '.date('Y').' KeenGuild', $html);
        $officialLinks = Schema::hasTable('contact_channels')
            ? ContactChannel::query()->where('published', true)->get()
                ->mapWithKeys(fn (ContactChannel $channel) => [$channel->platform => $channel->publicUrl()])
                ->filter()->all()
            : [];
        $html = preg_replace_callback('/<a\b[^>]*\baria-label="(LinkedIn|Instagram|Facebook|X|Behance|Dribbble|GitHub|YouTube|WhatsApp|Email)"[^>]*>/u', function (array $match) use ($officialLinks): string {
            $platform = $match[1];
            $officialUrl = $officialLinks[$platform] ?? null;
            $safeUrl = htmlspecialchars($officialUrl ?? '#', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $anchor = preg_replace('/\shref="[^"]*"/u', ' href="'.$safeUrl.'"', $match[0], 1) ?? $match[0];
            if ($officialUrl === null) {
                return preg_replace('/\starget="[^"]*"/u', '', $anchor) ?? $anchor;
            }

            return substr_replace($anchor, ' data-official-url="'.$safeUrl.'"', -1, 0);
        }, $html);
        $html = preg_replace_callback('~<nav class="social-links".*?</nav>~s', function (array $match): string {
            return preg_replace('~<a\b[^>]*href="#"[^>]*>.*?</a>~s', '', $match[0]) ?? $match[0];
        }, $html, 1) ?? $html;
        $html = preg_replace_callback('~<div class="social-floater-links">.*?</div>~s', function (array $match): string {
            return preg_replace('~<a\b[^>]*href="#"[^>]*>.*?</a>~s', '', $match[0]) ?? $match[0];
        }, $html, 1) ?? $html;
        if (Schema::hasTable('services')) {
            $html = $this->applyFeaturedServices($html);
        }
        if (Schema::hasTable('articles')) {
            $articles = Article::query()->publiclyVisible()->with('category')->orderByDesc('published_at')->limit(3)->get();
            $journal = view('home.journal', compact('articles'))->render();
            $html = preg_replace('~<section id="journal"[^>]*>.*?</section>~s', $journal, $html, 1);
        }
        if (Schema::hasTable('homepage_videos') && $video = HomepageVideo::query()->where('published', true)->first()) {
            $url = $video->publicUrl();
            if ($url !== null) {
                $html = str_replace('data-src="/assets/showreel.mp4"', 'data-src="'.e($url).'"', $html);
            }
        }
        if (Schema::hasTable('projects')) {
            $demoProjects = Project::query()->publiclyVisible()->where('featured_in_demos', true)
                ->where('demo_status', 'ready')->orderBy('sort_order')->orderBy('id')->get()
                ->filter(fn (Project $project): bool => $project->hasLiveDemo())->take(3);
            if ($demoProjects->isNotEmpty()) {
                $demos = view('home.demos', ['projects' => $demoProjects])->render();
                $html = preg_replace('~<section id="demos"[^>]*>.*?</section>~s', $demos, $html, 1) ?? $html;
            }
            $projects = Project::query()->publiclyVisible()->orderByDesc('featured')->orderBy('sort_order')->limit(2)->get();
            if ($projects->isNotEmpty()) {
                $work = view('home.work', compact('projects'))->render();
                $html = preg_replace('~<section id="work"[^>]*>.*?</section>~s', $work, $html, 1);
            } else {
                $work = view('home.work-concepts', compact('locale'))->render();
                $html = preg_replace('~<section id="work"[^>]*>.*?</section>~s', $work, $html, 1);
            }
        }
        $externalDemos = view('shared.external-demos', ['locale' => 'ar', 'home' => true])->render();
        $html = preg_replace_callback('~<section id="demos"[^>]*>.*?</section>~s',
            fn (array $match): string => $match[0].$externalDemos, $html, 1) ?? $html;
        if (Schema::hasTable('homepage_contents') && $content = HomepageContent::query()->first()) {
            $managedCopy = $content->content ?? [];
            if (! $availability->enabled('ar') || ! $availability->enabled('en')) {
                unset($managedCopy['contact_button_ar'], $managedCopy['contact_button_en']);
            }
            $html = $this->applyHomepageContent($html, $managedCopy, $locale);
            if ($logo = $content->publicImageUrl('logo_path', 'branding')) {
                $html = str_replace('src="/assets/brand/favicon-new2.png"', 'src="'.e($logo).'"', $html);
            }
            if ($favicon = $content->publicImageUrl('favicon_path', 'branding')) {
                $html = str_replace('href="/assets/brand/favicon-new2.png?v=1"', 'href="'.e($favicon).'"', $html);
            }
            if ($orbitLogo = $content->publicImageUrl('orbit_logo_path', 'branding')) {
                $html = str_replace('class="orbit-logo" src="/assets/brand/kg-transparent.png"', 'class="orbit-logo" src="'.e($orbitLogo).'"', $html);
            }
            if ($contactBackground = $content->publicImageUrl('contact_background_path', 'homepage')) {
                $style = '<style id="managed-contact-background">#contact{background-image:linear-gradient(90deg,rgba(4,13,22,.22),rgba(4,13,22,.38),rgba(4,13,22,.12)),url("'.e($contactBackground).'")!important}</style>';
                $html = str_replace('</head>', $style.'</head>', $html);
            }
        }
        // The built-in concept cards lead to the interactive Laravel previews.
        // Managed project demos have their own approved external URLs and are untouched.
        $html = preg_replace_callback('~<button\b[^>]*\bonclick="openDemo\(\'(Flowboard|Storefront|Pulse)\'\)"[^>]*>.*?</button>~s', function (array $match): string {
            $slug = strtolower($match[1]);
            $link = preg_replace('~\s+onclick="[^"]*"~', '', $match[0], 1) ?? $match[0];
            $link = preg_replace('~^<button\b~', '<a href="'.route('concept.demo', ['locale' => 'ar', 'slug' => $slug], false).'"', $link, 1) ?? $link;

            return preg_replace('~</button>$~', '</a>', $link, 1) ?? $link;
        }, $html) ?? $html;
        $html = str_replace('href="#journal" class="hidden md:block font-bold underline underline-offset-8"', 'href="/ar/articles" data-home-route="articles" class="hidden md:block font-bold underline underline-offset-8"', $html);
        $links = '<a href="/ar/services" class="nav-link px-3 py-2 rounded-lg hover:bg-[#eef8fb] hover:text-ink transition" data-catalog-ar="كل الخدمات" data-catalog-en="All services">كل الخدمات</a>'
            .'<a href="/ar/pricing" class="nav-link px-3 py-2 rounded-lg hover:bg-[#eef8fb] hover:text-ink transition" data-catalog-ar="الأسعار" data-catalog-en="Pricing">الأسعار</a>';
        $html = str_replace('الأفكار</a></div>', 'الأفكار</a>'.$links.'</div>', $html);
        $html = str_replace('href="#journal" class="nav-link px-3 py-2 rounded-lg hover:bg-[#eef8fb] hover:text-ink transition">الأفكار</a>', 'href="/ar/articles" data-catalog-ar="المقالات" data-catalog-en="Articles" class="nav-link px-3 py-2 rounded-lg hover:bg-[#eef8fb] hover:text-ink transition">المقالات</a>', $html);
        $menuLinks = '<nav aria-label="صفحات KeenGuild" class="mt-5 flex flex-wrap gap-2">'
            .'<a href="/" data-catalog-ar="الرئيسية" data-catalog-en="Home" class="rounded-xl border border-white/20 px-3 py-2 text-sm">الرئيسية</a>'
            .'<a href="/ar/about" data-catalog-ar="من نحن" data-catalog-en="About" class="rounded-xl border border-white/20 px-3 py-2 text-sm">من نحن</a>'
            .'<a href="/ar/services" data-catalog-ar="كل الخدمات" data-catalog-en="All services" class="rounded-xl border border-white/20 px-3 py-2 text-sm">كل الخدمات</a>'
            .'<a href="/ar/pricing" data-catalog-ar="الأسعار" data-catalog-en="Pricing" class="rounded-xl border border-white/20 px-3 py-2 text-sm">الأسعار</a>'
            .'<a href="/ar/work" data-catalog-ar="الأعمال والديمو" data-catalog-en="Work and demos" class="rounded-xl border border-white/20 px-3 py-2 text-sm">الأعمال والديمو</a>'
            .'<a href="/ar/articles" data-catalog-ar="المقالات" data-catalog-en="Articles" class="rounded-xl border border-white/20 px-3 py-2 text-sm">المقالات</a>'
            .'<a href="/ar/faq" data-catalog-ar="الأسئلة الشائعة" data-catalog-en="FAQ" class="rounded-xl border border-white/20 px-3 py-2 text-sm">الأسئلة الشائعة</a>'
            .'<a href="/ar/contact" data-catalog-ar="تواصل معنا" data-catalog-en="Contact" class="rounded-xl border border-white/20 px-3 py-2 text-sm">تواصل معنا</a>'
            .'</nav>';
        $html = str_replace('لنتحدث عن فكرتك ↗</a></div>', 'لنتحدث عن فكرتك ↗</a>'.$menuLinks.'</div>', $html);
        $catalogLinks = '<nav aria-label="KeenGuild catalog" style="display:flex;gap:12px;flex-wrap:wrap;padding:18px 40px;background:#0b1d2e;color:white">'
            .'<a href="/ar/about" data-catalog-ar="من نحن" data-catalog-en="About">من نحن</a>'
            .'<a href="/ar/services" data-catalog-ar="كل الخدمات" data-catalog-en="All services">كل الخدمات</a>'
            .'<a href="/ar/pricing" data-catalog-ar="الأسعار" data-catalog-en="Pricing">الأسعار</a>'
            .'<a href="/ar/work" data-catalog-ar="الأعمال والديمو" data-catalog-en="Work and demos">الأعمال والديمو</a>'
            .'<a href="/ar/articles" data-catalog-ar="المقالات" data-catalog-en="Articles">المقالات</a>'
            .'<a href="/ar/faq" data-catalog-ar="الأسئلة الشائعة" data-catalog-en="FAQ">الأسئلة الشائعة</a>'
            .'<a href="/ar/contact" data-catalog-ar="تواصل معنا" data-catalog-en="Contact">تواصل معنا</a>'
            .'</nav>';
        if (Schema::hasTable('legal_pages')) {
            foreach (LegalPage::query()->publiclyVisible()->whereIn('type', ['privacy', 'terms'])->get() as $legalPage) {
                $type = $legalPage->type;
                $ar = htmlspecialchars($legalPage->title_ar, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $en = htmlspecialchars($legalPage->title_en, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $catalogLinks .= '<a href="/ar/legal/'.$type.'" data-legal-type="'.$type.'" data-catalog-ar="'.$ar.'" data-catalog-en="'.$en.'" style="margin-inline:12px">'.$ar.'</a>';
            }
        }
        $html = str_replace('</footer>', $catalogLinks.'</footer>', $html);
        $html = str_replace('</body>', '<script>(function(){const links=document.querySelectorAll("[data-catalog-ar]");const serviceLinks=document.querySelectorAll("[data-home-service-slug]");const managedText=document.querySelectorAll("[data-home-ar]");const managedLinks=document.querySelectorAll("[data-home-route]");const homeMeta=document.querySelector("[data-home-meta-ar]");function sync(){const en=document.documentElement.lang==="en";links.forEach(a=>{a.textContent=a.getAttribute(en?"data-catalog-en":"data-catalog-ar");a.href=a.href.replace(/\/(ar|en)\/(about|services|pricing|work|articles|faq|contact)$/, "/"+(en?"en":"ar")+"/$2").replace(/\/(ar|en)\/legal\/(privacy|terms)$/, "/"+(en?"en":"ar")+"/legal/$2")});serviceLinks.forEach(a=>{a.href="/"+(en?"en":"ar")+"/services/"+a.dataset.homeServiceSlug});managedText.forEach(el=>{el.textContent=el.getAttribute(en?"data-home-en":"data-home-ar")});managedLinks.forEach(a=>{a.href=a.href.replace(/\/(ar|en)\/(articles|work|request-quote)(\/[^/?#]+)?$/, "/"+(en?"en":"ar")+"/$2$3")});if(homeMeta)homeMeta.content=homeMeta.getAttribute(en?"data-home-meta-en":"data-home-meta-ar")}new MutationObserver(sync).observe(document.documentElement,{attributes:true,attributeFilter:["lang"]});sync()})()</script></body>', $html);
        $html = str_replace('(articles|work|request-quote)(\/[^/?#]+)?', '(articles|work|request-quote|pricing)((?:\/[^/?#]+){0,2})', $html);
        $html = str_replace('(about|services|pricing|work|articles|faq|contact)$', '(about|services|pricing|work|articles|faq|contact|request-quote)$', $html);
        if (Schema::hasTable('navigation_items')) {
            $html = $this->applyNavigation($html, $locale);
        }
        $html = $this->applySeoSettings($html);
        $html = preg_replace_callback('~(<p\b[^>]*\bid="servicesAiCopy"[^>]*\bdata-ar="([^"]*)"[^>]*>)(</p>)~s',
            fn (array $match): string => $match[1].$match[2].$match[3], $html, 1) ?? $html;
        $html = str_replace('</head>', '<link rel="alternate" hreflang="ar" href="'.e(route('home')).'" /><link rel="alternate" hreflang="en" href="'.e(route('home.en')).'" /></head>', $html);
        $target = $locale === 'en' ? route('home') : route('home.en');
        $html = str_replace('onclick="setLanguage(document.documentElement.lang === \'ar\' ? \'en\' : \'ar\')"', 'onclick="location.href=\''.e($target).'\'+location.hash"', $html);
        $html = str_replace("setLanguage(localStorage.getItem('keenguild-language') || 'ar');", "setLanguage('".$locale."');", $html);
        if ($locale === 'en') {
            $seo = SeoSetting::current();
            $title = filled($seo?->home_title_en) ? $seo->home_title_en : 'KeenGuild — We build products that grow with you';
            $description = filled($seo?->default_description_en) ? $seo->default_description_en : 'KeenGuild builds thoughtful websites and digital products around your business goals.';
            if (preg_match('/data-home-meta-en="([^"]*)"/', $html, $descriptionMatch)) {
                $description = html_entity_decode($descriptionMatch[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            $html = $localizer->english($html, $original, $title, $description);
        }

        if ($previewHero) {
            $html = preg_replace('~<link\s+rel="(?:canonical|alternate)"[^>]*>~i', '', $html) ?? $html;
            $html = preg_replace('~<meta\s+name="robots"[^>]*>~i', '', $html) ?? $html;
            $html = str_replace('</head>', '<meta name="robots" content="noindex,nofollow"></head>', $html);
        }

        $response = response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');

        return $previewHero ? $response->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow') : $response;
    }

    private function applyAgentConfiguration(string $html): string
    {
        $configuredApi = config('keenguild.agent_api_url');
        $configuredKey = config('keenguild.agent_site_key');
        if (blank($configuredApi) && blank($configuredKey)) {
            return $html;
        }

        $api = is_string($configuredApi) ? rtrim(trim($configuredApi), '/') : '';
        $validApi = filter_var($api, FILTER_VALIDATE_URL) !== false && parse_url($api, PHP_URL_SCHEME) === 'https';
        $key = is_string($configuredKey) ? trim($configuredKey) : '';
        $validKey = preg_match('/^[A-Za-z0-9_-]{16,128}$/', $key) === 1;

        foreach (['keenguild-agent-api' => $validApi ? $api : '', 'keenguild-agent-site-key' => $validKey ? $key : ''] as $name => $value) {
            $html = preg_replace_callback(
                '/(<meta name="'.preg_quote($name, '/').'" content=")[^"]*(")/',
                fn (array $match): string => $match[1].e($value).$match[2],
                $html,
                1,
            ) ?? $html;
        }

        return $html;
    }

    private function applyHomepageHero(string $html, HomepageHero $hero): string
    {
        $fields = [
            'kicker' => [$hero->kicker_ar, $hero->kicker_en],
            'headline' => [$hero->headline_ar, $hero->headline_en],
            'accent' => [$hero->accent_ar, $hero->accent_en],
            'description' => [$hero->description_ar, $hero->description_en],
        ];

        foreach ($fields as $slot => [$ar, $en]) {
            $html = preg_replace_callback(
                '~<(span|p)\b([^>]*data-hero-slot="'.preg_quote($slot, '~').'"[^>]*)>.*?</\1>~s',
                function (array $match) use ($ar, $en): string {
                    $attributes = preg_replace('/\sdata-home-(ar|en)="[^"]*"/', '', $match[2]);

                    return '<'.$match[1].$attributes.' data-home-ar="'.e($ar).'" data-home-en="'.e($en).'">'.e($ar).'</'.$match[1].'>';
                },
                $html,
                1,
            ) ?? $html;
        }

        return $html;
    }

    private function applyHomepageContent(string $html, array $content, string $locale): string
    {
        // Each language can be edited independently. Existing tag replacement expects
        // both values, so mirror a single edit only for the response being rendered.
        foreach (array_keys($content) as $key) {
            if (! preg_match('/^(.+)_(ar|en)$/', $key, $matches)) {
                continue;
            }
            $base = $matches[1];
            $arKey = $base.'_ar';
            $enKey = $base.'_en';
            if (filled($content[$arKey] ?? null) === filled($content[$enKey] ?? null)) {
                continue;
            }
            if (filled($content[$base.'_'.$locale] ?? null)) {
                $content[$base.'_'.($locale === 'ar' ? 'en' : 'ar')] = $content[$base.'_'.$locale];
            } else {
                unset($content[$arKey], $content[$enKey]);
            }
        }
        $html = $this->applyMenuCopy($html, $content, $locale);
        foreach (['nav_studio' => 'استوديو منتجات رقمية', 'nav_availability' => 'متاح لمشاريع جديدة'] as $key => $original) {
            if (filled($content[$key.'_ar'] ?? null) && filled($content[$key.'_en'] ?? null)) {
                $html = str_replace('<span>'.$original.'</span>', '<span data-home-ar="'.e($content[$key.'_ar']).'" data-home-en="'.e($content[$key.'_en']).'">'.e($content[$key.'_ar']).'</span>', $html);
            }
        }
        if (filled($content['nav_cta_ar'] ?? null) && filled($content['nav_cta_en'] ?? null)) {
            $html = preg_replace_callback('~<a\b[^>]*id="navCta"[^>]*>.*?</a>~s', function (array $match) use ($content): string {
                $anchor = preg_replace('/>.*?<\/a>$/s', '>'.e($content['nav_cta_ar']).'</a>', $match[0]) ?? $match[0];

                return preg_replace('/<a\b/', '<a data-home-ar="'.e($content['nav_cta_ar']).'" data-home-en="'.e($content['nav_cta_en']).'"', $anchor, 1) ?? $match[0];
            }, $html, 1) ?? $html;
        }
        if (filled($content['orbit_kicker_ar'] ?? null) && filled($content['orbit_kicker_en'] ?? null)) {
            $html = str_replace('<span class="arabic-copy">الفكرة تتحرك. الأثر يبقى.</span><span class="english-copy">Ideas move. Impact remains.</span>', '<span class="arabic-copy">'.e($content['orbit_kicker_ar']).'</span><span class="english-copy">'.e($content['orbit_kicker_en']).'</span>', $html);
        }
        $sections = [
            'proof' => ['proof_kicker' => ['span', 0], 'proof_heading' => ['h2', 0], 'proof_description' => ['p', 0]],
            'services' => ['services_kicker' => ['span', 0], 'services_heading' => ['h2', 0]],
            'demos' => ['demos_kicker' => ['span', 0], 'demos_heading' => ['h2', 0], 'demos_description' => ['p', 0]],
            'work' => ['work_kicker' => ['span', 0], 'work_heading' => ['h2', 0]],
            'journal' => ['journal_kicker' => ['span', 0], 'journal_heading' => ['h2', 0]],
            'reel' => ['reel_kicker' => ['span', 0], 'reel_heading' => ['h2', 0], 'reel_description' => ['p', 0]],
            'contact' => ['contact_kicker' => ['span', 0], 'contact_heading' => ['h2', 0], 'contact_description' => ['p', 0]],
        ];

        foreach ($sections as $id => $fields) {
            $html = preg_replace_callback('~<section\b[^>]*\bid="'.preg_quote($id, '~').'"[^>]*>.*?</section>~s', function (array $match) use ($fields, $content, $id): string {
                $section = $match[0];
                foreach ($fields as $key => [$tag, $index]) {
                    $section = $this->replaceManagedTag($section, $tag, $index, $content, $key);
                }
                if ($id === 'proof') {
                    for ($i = 1; $i <= 4; $i++) {
                        $section = $this->replaceManagedTag($section, 'h3', $i - 1, $content, 'proof_'.$i.'_title');
                        $section = $this->replaceManagedTag($section, 'p', $i, $content, 'proof_'.$i.'_description');
                    }
                }
                if ($id === 'services' && filled($content['services_description_ar'] ?? null) && filled($content['services_description_en'] ?? null)) {
                    $section = preg_replace_callback('~(<p\b[^>]*\bid="servicesAiCopy"[^>]*)(></p>)~s', function (array $paragraph) use ($content): string {
                        $tag = preg_replace('/\sdata-(ar|en)="[^"]*"/', '', $paragraph[1]);

                        return $tag.' data-ar="'.e($content['services_description_ar']).'" data-en="'.e($content['services_description_en']).'"'.$paragraph[2];
                    }, $section, 1) ?? $section;
                }
                if ($id === 'demos' && ! str_contains($section, 'data-home-demo-project=')) {
                    $demoIndex = 0;
                    $section = preg_replace_callback('~<article\b.*?</article>~s', function (array $article) use (&$demoIndex, $content): string {
                        $demoIndex++;
                        if ($demoIndex > 3) {
                            return $article[0];
                        }

                        return $this->replaceManagedTag(
                            $this->replaceManagedTag($article[0], 'h3', 0, $content, 'demo_'.$demoIndex.'_title'),
                            'p', 0, $content, 'demo_'.$demoIndex.'_description'
                        );
                    }, $section) ?? $section;
                }
                if ($id === 'reel') {
                    $section = preg_replace_callback('~<button class="video-play".*?</button>~s', fn (array $button): string => $this->replaceManagedTag($button[0], 'span', 1, $content, 'reel_button'), $section, 1) ?? $section;
                }
                if ($id === 'contact' && filled($content['contact_button_ar'] ?? null) && filled($content['contact_button_en'] ?? null)) {
                    $section = preg_replace_callback('~<(a|button)\b(?=[^>]*(?:data-home-route="request-quote"|onclick="openContactPreview\(\)"))[^>]*>.*?</\1>~s', function (array $action) use ($content): string {
                        $tag = preg_replace('/\sdata-home-(ar|en)="[^"]*"/', '', $action[0]);
                        $tag = preg_replace('~>.*?</'.preg_quote($action[1], '~').'>$~s', '>'.e($content['contact_button_ar']).'</'.$action[1].'>', $tag);

                        return preg_replace('/<'.preg_quote($action[1], '/').'\b/', '<'.$action[1].' data-home-ar="'.e($content['contact_button_ar']).'" data-home-en="'.e($content['contact_button_en']).'"', $tag, 1) ?? $action[0];
                    }, $section, 1) ?? $section;
                }

                return $section;
            }, $html, 1) ?? $html;
        }

        if (filled($content['footer_tagline_ar'] ?? null) && filled($content['footer_tagline_en'] ?? null)) {
            $html = preg_replace_callback('~<footer\b.*?</footer>~s', fn (array $match): string => $this->replaceManagedTag($match[0], 'p', 0, $content, 'footer_tagline'), $html, 1) ?? $html;
        }

        return $html;
    }

    private function applyFeaturedServices(string $html): string
    {
        if (! Service::query()->exists()) {
            return $html;
        }

        $services = Service::query()->where('published', true)->where('featured_on_home', true)
            ->orderBy('sort_order')->orderBy('id')->limit(4)->get();

        return preg_replace_callback('~<section id="services"[^>]*>.*?</section>~s', function (array $match) use ($services): string {
            $section = $match[0];
            preg_match_all('~<article\b.*?</article>~s', $section, $matches, PREG_OFFSET_CAPTURE);
            $templates = $matches[0] ?? [];
            if ($templates === []) {
                return $section;
            }

            $cards = '';
            foreach ($services as $index => $service) {
                if (preg_match('/^[a-z0-9-]+$/', $service->slug) !== 1) {
                    continue;
                }
                $card = $templates[$index][0] ?? $templates[0][0];
                $heading = '<h3 class="text-2xl font-black"><a href="/ar/services/'.rawurlencode($service->slug).'" data-home-service-slug="'.e($service->slug).'"><span data-home-ar="'.e($service->title_ar).'" data-home-en="'.e($service->title_en).'">'.e($service->title_ar).'</span></a></h3>';
                $card = preg_replace('~<h3 class="text-2xl font-black">.*?</h3>~s', $heading, $card, 1) ?? $card;
                if (filled($service->summary_ar) && filled($service->summary_en)) {
                    $card = preg_replace_callback('~<p\b([^>]*)>.*?</p>~s', fn (array $paragraph): string => '<p'.$paragraph[1].'><span data-home-ar="'.e($service->summary_ar).'" data-home-en="'.e($service->summary_en).'">'.e($service->summary_ar).'</span></p>', $card, 1) ?? $card;
                }
                $cards .= $card;
            }
            if ($cards === '') {
                $cards = '<p class="rounded-2xl border border-black/10 p-6 text-black/60" data-home-ar="الخدمات قيد المراجعة." data-home-en="Services are being reviewed.">الخدمات قيد المراجعة.</p>';
            }

            $first = $templates[0][1];
            $last = end($templates);
            $end = $last[1] + strlen($last[0]);

            return substr($section, 0, $first).$cards.substr($section, $end);
        }, $html, 1) ?? $html;
    }

    private function applyMenuCopy(string $html, array $content, string $locale): string
    {
        $start = strpos($html, '<div id="menuPanel"');
        $end = strpos($html, '<main id="top"');
        if ($start === false || $end === false || $end <= $start) {
            return $html;
        }

        $menu = substr($html, $start, $end - $start);
        $menu = $this->replaceManagedTag($menu, 'h2', 0, $content, 'menu_heading');
        $menu = $this->replaceManagedTag($menu, 'p', 0, $content, 'menu_description');
        $menu = $this->replaceManagedTag($menu, 'a', 0, $content, 'menu_cta');
        for ($i = 1; $i <= 4; $i++) {
            $menu = $this->replaceManagedTag($menu, 'h3', $i - 1, $content, 'menu_'.$i.'_title');
            $menu = $this->replaceManagedTag($menu, 'p', $i, $content, 'menu_'.$i.'_description');
        }

        $allowedTargets = ['#services', '#pricing', '#demos', '#work', '#journal', '#reel', '#contact'];
        $cardIndex = 0;
        $menu = preg_replace_callback('~<a href="#[^"]+" onclick="closeMenu\(\)" class="menu-card[^>]*>~', function (array $match) use (&$cardIndex, $content, $allowedTargets, $locale): string {
            $cardIndex++;
            $target = $content['menu_'.$cardIndex.'_target'] ?? null;
            if (! in_array($target, $allowedTargets, true)) {
                return $match[0];
            }

            $url = $target === '#pricing' ? route('pricing', ['locale' => $locale], false) : $target;

            return preg_replace('/href="#[^"]+"/', 'href="'.$url.'"', $match[0], 1) ?? $match[0];
        }, $menu) ?? $menu;

        return substr($html, 0, $start).$menu.substr($html, $end);
    }

    private function applyNavigation(string $html, string $locale): string
    {
        $items = NavigationItem::query()->where('published', true)->orderBy('sort_order')->orderBy('id')->get()->groupBy('location');
        $render = function (string $location, string $class, bool $closeMenu = false) use ($items, $locale): string {
            return ($items->get($location) ?? collect())->map(function (NavigationItem $item) use ($class, $closeMenu, $locale): string {
                $url = $item->publicUrl($locale, true);
                if ($url === null) {
                    return '';
                }

                return '<a href="'.e($url).'" data-catalog-ar="'.e($item->label_ar).'" data-catalog-en="'.e($item->label_en).'" class="'.$class.'"'.($closeMenu ? ' onclick="closeMenu()"' : '').'>'.e($locale === 'en' ? $item->label_en : $item->label_ar).'</a>';
            })->implode('');
        };

        $header = '<div class="hidden xl:flex items-center gap-1 text-sm font-bold text-black/55">'.$render('header', 'nav-link px-3 py-2 rounded-lg hover:bg-[#eef8fb] hover:text-ink transition').'</div>';
        $html = preg_replace('~<div class="hidden xl:flex items-center gap-1 text-sm font-bold text-black/55">.*?</div>~s', $header, $html, 1) ?? $html;
        $overlay = '<nav aria-label="صفحات KeenGuild" class="mt-5 flex flex-wrap gap-2">'.$render('overlay', 'rounded-xl border border-white/20 px-3 py-2 text-sm', true).'</nav>';
        $html = preg_replace('~<nav aria-label="صفحات KeenGuild".*?</nav>~s', $overlay, $html, 1) ?? $html;
        $footer = '<nav aria-label="KeenGuild catalog" style="display:flex;gap:12px;flex-wrap:wrap;padding:18px 40px;background:#0b1d2e;color:white">'.$render('footer', '').'</nav>';
        $html = preg_replace('~<nav aria-label="KeenGuild catalog".*?</nav>~s', $footer, $html, 1) ?? $html;

        return $html;
    }

    private function applySeoSettings(string $html): string
    {
        $seo = SeoSetting::current();
        if (! $seo) {
            return $html;
        }

        if (! $seo->allow_indexing) {
            $html = str_replace('</head>', '<meta name="robots" content="noindex,nofollow"></head>', $html);
        }
        if (filled($seo->default_description_ar) || filled($seo->default_description_en)) {
            preg_match('~<meta name="description" content="([^"]*)"~', $html, $currentDescription);
            $descriptionAr = filled($seo->default_description_ar)
                ? $seo->default_description_ar
                : html_entity_decode($currentDescription[1] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $descriptionEn = filled($seo->default_description_en)
                ? $seo->default_description_en
                : 'KeenGuild builds thoughtful websites and digital products around your business goals.';
            $meta = '<meta name="description" content="'.e($descriptionAr).'" data-home-meta-ar="'.e($descriptionAr).'" data-home-meta-en="'.e($descriptionEn).'" />';
            $html = preg_replace('~<meta name="description"[^>]*>~', $meta, $html, 1) ?? $html;
        }
        if (filled($seo->home_title_ar)) {
            $html = preg_replace('~<title>.*?</title>~s', '<title>'.e($seo->home_title_ar).'</title>', $html, 1) ?? $html;
        }
        if (filled($seo->home_title_ar) || filled($seo->home_title_en)) {
            $titleAr = filled($seo->home_title_ar) ? $seo->home_title_ar : 'KeenGuild — نبني المنتجات التي تكبر معك';
            $titleEn = filled($seo->home_title_en) ? $seo->home_title_en : 'KeenGuild — We build products that grow with you';
            $html = str_replace('</body>', '<script>(function(){const titles={ar:'.json_encode($titleAr, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE).',en:'.json_encode($titleEn, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE).'};function update(){document.title=titles[document.documentElement.lang]||titles.ar}new MutationObserver(update).observe(document.documentElement,{attributes:true,attributeFilter:["lang"]});update()})()</script></body>', $html);
        }
        if (filled($seo->social_image_path) && Storage::disk('public')->exists($seo->social_image_path)) {
            $html = str_replace('</head>', '<meta property="og:image" content="'.e(asset('storage/'.$seo->social_image_path)).'"></head>', $html);
        }

        return $html;
    }

    private function replaceManagedTag(string $html, string $tag, int $index, array $content, string $key): string
    {
        $ar = trim((string) ($content[$key.'_ar'] ?? ''));
        $en = trim((string) ($content[$key.'_en'] ?? ''));
        if ($ar === '' || $en === '') {
            return $html;
        }

        $seen = 0;

        return preg_replace_callback('~(<'.$tag.'\b[^>]*>)(.*?)(</'.$tag.'>)~s', function (array $match) use (&$seen, $index, $ar, $en): string {
            if ($seen++ !== $index) {
                return $match[0];
            }

            return $match[1].'<span data-home-ar="'.e($ar).'" data-home-en="'.e($en).'">'.e($ar).'</span>'.$match[3];
        }, $html) ?? $html;
    }

    public function asset(string $path): BinaryFileResponse
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        abort_unless(in_array($extension, ['avif', 'css', 'jpg', 'jpeg', 'mp4', 'png', 'svg', 'webp'], true), 404);

        $root = realpath(base_path('../dist/assets'));
        $file = $root ? realpath($root.DIRECTORY_SEPARATOR.$path) : false;
        abort_unless($root && $file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file), 404);

        return response()->file($file, [
            'Content-Type' => $extension === 'css' ? 'text/css; charset=UTF-8' : (mime_content_type($file) ?: 'application/octet-stream'),
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function agentBridge(): Response
    {
        $source = base_path('../dist/agent-api-bridge.js');
        abort_unless(is_file($source), 404);

        return response(file_get_contents($source), 200)->header('Content-Type', 'application/javascript; charset=UTF-8');
    }
}
