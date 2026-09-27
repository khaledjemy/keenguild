<section id="journal" class="px-5 md:px-10 py-28 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-end mb-12 reveal">
            <div>
                <span class="text-sm font-black text-lime-700">04 / من المدونة</span>
                <h2 class="text-4xl md:text-6xl font-black mt-4">أفكار تساعدك<br>تبني بشكل أفضل.</h2>
                <p class="mt-3 text-sm text-black/50" data-home-ar="{{ $articles->isEmpty() ? 'المقالات قيد الإعداد؛ لن نعرض معاينات على أنها منشورة.' : 'مقالات منشورة من فريق KeenGuild.' }}" data-home-en="{{ $articles->isEmpty() ? 'Articles are being prepared; no previews are presented as published work.' : 'Published articles from KeenGuild.' }}">{{ $articles->isEmpty() ? 'المقالات قيد الإعداد؛ لن نعرض معاينات على أنها منشورة.' : 'مقالات منشورة من فريق KeenGuild.' }}</p>
            </div>
            <a href="{{ route('articles', ['locale' => 'ar']) }}" data-home-route="articles" class="hidden md:block font-bold underline underline-offset-8">استكشف المقالات ↗</a>
        </div>
        @if($articles->isEmpty())
            <div class="rounded-2xl border border-black/10 bg-[#f4f8f9] p-8 text-black/60" data-home-ar="لا توجد مقالات منشورة بعد. ستظهر هنا عند نشرها من لوحة الإدارة." data-home-en="No articles have been published yet. They will appear here when published from the admin panel.">لا توجد مقالات منشورة بعد. ستظهر هنا عند نشرها من لوحة الإدارة.</div>
        @else
        <div class="grid md:grid-cols-3 gap-5">
            @foreach($articles as $article)
                <article class="reveal border-t-2 border-ink pt-5">
                    @if($article->cover_path)
                        <img class="journal-image" src="{{ asset('storage/'.$article->cover_path) }}" alt="" loading="lazy">
                    @else
                        <div class="journal-image bg-mist" style="display:flex;align-items:center;justify-content:center" aria-hidden="true"><img src="/assets/brand/favicon-new2.png" alt="" style="width:58px;height:58px;object-fit:contain"></div>
                    @endif
                    <div class="flex justify-between text-xs font-bold text-black/50">@if($article->category)<a href="{{ route('articles.category', ['locale' => 'ar', 'slug' => $article->category->slug]) }}" data-home-route="articles.category" data-home-ar="{{ $article->category->name_ar }}" data-home-en="{{ $article->category->name_en }}">{{ $article->category->name_ar }}</a>@else<span data-home-ar="مقال" data-home-en="Article">مقال</span>@endif<time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->format('Y-m-d') }}</time></div>
                    <h3 class="text-2xl font-black leading-tight mt-7"><a href="{{ route('article', ['locale' => 'ar', 'slug' => $article->slug]) }}" data-home-route="article" data-home-ar="{{ $article->title_ar }}" data-home-en="{{ $article->title_en }}">{{ $article->title_ar }}</a></h3>
                    <p class="text-black/55 leading-7 mt-4" data-home-ar="{{ $article->summary_ar }}" data-home-en="{{ $article->summary_en }}">{{ $article->summary_ar }}</p>
                    <a href="{{ route('article', ['locale' => 'ar', 'slug' => $article->slug]) }}" data-home-route="article" class="inline-block mt-8 font-bold" data-home-ar="اقرأ المقال ←" data-home-en="Read article →">اقرأ المقال ←</a>
                </article>
            @endforeach
        </div>
        @endif
    </div>
</section>
