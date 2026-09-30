<section id="demos" class="px-5 md:px-10 py-28 bg-ink text-white noise">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row items-start justify-between gap-6 mb-14 reveal">
            <div>
                <span class="text-sm font-black text-lime" data-home-ar="02 / جرّب بنفسك" data-home-en="02 / Try it yourself">02 / جرّب بنفسك</span>
                <h2 class="text-4xl md:text-6xl font-black tracking-tight mt-4"><span data-home-ar="مشاريع لها" data-home-en="Projects you can">مشاريع لها</span><br><span class="text-white/40" data-home-ar="تجربة مباشرة." data-home-en="explore live.">تجربة مباشرة.</span></h2>
            </div>
            <p class="max-w-md text-white/55 text-lg leading-8" data-home-ar="هذه المشاريع لديها رابط ديمو مستقل. اطلع على التفاصيل ثم افتح التجربة في نافذة جديدة." data-home-en="These projects have independent live demos. Read the details, then open a demo in a new tab.">هذه المشاريع لديها رابط ديمو مستقل. اطلع على التفاصيل ثم افتح التجربة في نافذة جديدة.</p>
        </div>
        <div class="grid lg:grid-cols-3 gap-5">
            @foreach($projects as $project)
                <article class="reveal bg-white/8 border border-white/10 rounded-3xl p-4" data-home-demo-project="{{ $project->slug }}">
                    <div class="bg-white/10 rounded-2xl h-56 overflow-hidden flex items-center justify-center">
                        @if($project->coverUrl())
                            <img src="{{ $project->coverUrl() }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <img src="/assets/brand/favicon-new2.png" alt="" loading="lazy" style="width:72px;height:72px;object-fit:contain">
                        @endif
                    </div>
                    <div class="p-3 pt-5">
                        @if($project->project_type === 'external')<p class="text-lime text-xs font-bold" data-home-ar="مثال خارجي من {{ $project->source_name }} — ليس من أعمالنا" data-home-en="Third-party example by {{ $project->source_name }} — not our work">مثال خارجي من {{ $project->source_name }} — ليس من أعمالنا</p>@endif
                        <h3 class="text-xl font-black" data-home-ar="{{ $project->title_ar }}" data-home-en="{{ $project->title_en }}">{{ $project->title_ar }}</h3>
                        <p class="text-white/55 text-sm mt-2" data-home-ar="{{ $project->summary_ar }}" data-home-en="{{ $project->summary_en }}">{{ $project->summary_ar }}</p>
                        <div class="flex flex-wrap gap-3 mt-4 text-sm font-bold">
                            <a href="{{ route('project', ['locale' => 'ar', 'slug' => $project->slug]) }}" data-home-route="project" data-home-ar="تفاصيل المشروع" data-home-en="Project details">تفاصيل المشروع</a>
                            <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="bg-lime text-ink px-4 py-2 rounded-xl" data-home-ar="افتح الديمو ↗" data-home-en="Open live demo ↗">افتح الديمو ↗</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
