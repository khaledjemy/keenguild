<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $faqs = [
            ['ما الخدمات التي تقدمها KeenGuild؟', 'What services does KeenGuild offer?',
                'نعمل على تصميم وتطوير المواقع والمتاجر الإلكترونية وتطبيقات الويب، مع خدمات تصميم الواجهات والتكاملات والتحسينات التقنية. يُحدد نطاق كل خدمة حسب متطلبات المشروع.',
                'We work on websites, online stores, web applications, interface design, integrations and technical improvements. The scope of each service depends on the project requirements.'],
            ['كيف أبدأ مشروعًا معكم؟', 'How do I start a project with you?',
                'أرسل وصفًا مختصرًا لهدف المشروع والجمهور المستهدف والوظائف المطلوبة. نراجع المتطلبات أولًا، ثم نناقش النطاق والخطوات التالية قبل إعداد عرض مناسب.',
                'Share a brief description of your goal, audience and required features. We review the requirements, then discuss scope and next steps before preparing a suitable proposal.'],
            ['هل أحتاج صفحة هبوط أم موقع شركة؟', 'Do I need a landing page or a company website?',
                'صفحة الهبوط مناسبة عادةً لهدف واحد أو حملة محددة. موقع الشركة أنسب عندما تحتاج إلى عرض خدمات ومعلومات متعددة. نختار الهيكل بعد فهم ما يحتاجه زوارك.',
                'A landing page usually fits one goal or a focused campaign. A company website is better when visitors need several services and information pages. We choose the structure after understanding your audience.'],
            ['كيف تُحدد تكلفة المشروع؟', 'How is a project priced?',
                'تعتمد التكلفة على الصفحات والوظائف والتصميم والتكاملات والمحتوى المطلوب. الأسعار المعروضة في الموقع استرشادية حسب النطاق المذكور، ويُحدد السعر النهائي في عرض واضح بعد مراجعة المتطلبات.',
                'Cost depends on the pages, features, design, integrations and content required. Prices shown on the site are indicative for the stated scope; the final price is set out in a clear proposal after reviewing your requirements.'],
            ['هل تشمل الأسعار الدومين والاستضافة والخدمات الخارجية؟', 'Do prices include a domain, hosting and third-party services?',
                'ليس بالضرورة. تكلفة الدومين والاستضافة والتراخيص أو رسوم مزودي الخدمات تُوضح منفصلةً في العرض، ولا تُعد مشمولة إلا إذا ذُكر ذلك صراحةً.',
                'Not necessarily. Domain, hosting, licence and provider fees are identified separately in the proposal and are included only when explicitly stated.'],
            ['كم يستغرق تنفيذ الموقع أو التطبيق؟', 'How long does it take to build a website or app?',
                'تختلف المدة حسب حجم المشروع وجاهزية المحتوى وسرعة المراجعات والتكاملات المطلوبة. نحدد جدولًا تقديريًا ومراحل تسليم بعد الاتفاق على النطاق؛ ولا نعد بمدة ثابتة لكل المشاريع.',
                'Timing varies with scope, content readiness, review cycles and integrations. We outline an estimated schedule and milestones once the scope is agreed; there is no fixed timeline for every project.'],
            ['ما الذي يجب أن أجهزه قبل بدء العمل؟', 'What should I prepare before work starts?',
                'يفيد تجهيز أهداف المشروع، وصف الجمهور، قائمة الصفحات أو الوظائف الأساسية، الهوية البصرية إن وُجدت، والنصوص والصور المتاحة. إذا لم تكن كل التفاصيل جاهزة، يمكن البدء بتحديد الأولويات.',
                'It helps to prepare your goals, audience, essential pages or features, any existing brand assets, and available copy and images. If everything is not ready, we can begin by identifying priorities.'],
            ['هل يمكن تطوير موقع موجود بدل إنشاء موقع جديد؟', 'Can you improve an existing site instead of building a new one?',
                'يمكن تقييم الموقع الحالي وتحديد ما إذا كانت التحسينات مناسبة أو أن إعادة البناء أوضح وأوفر على المدى الطويل. القرار يعتمد على حالة الكود والاستضافة والوظائف المطلوبة.',
                'We can assess the current site and determine whether improvements or a rebuild make more sense. The recommendation depends on the codebase, hosting and features you need.'],
            ['هل يمكن إنشاء متجر إلكتروني وربطه بالدفع والشحن؟', 'Can an online store include payment and shipping integrations?',
                'يمكن دراسة كتالوج المنتجات والسلة والطلبات والتكاملات المطلوبة ضمن النطاق. ربط بوابات الدفع والشحن يعتمد على مزودي الخدمة وشروطهم ورسومهم، ويُذكر بوضوح في العرض.',
                'We can scope the product catalogue, cart, orders and required integrations. Payment and shipping connections depend on the providers, their requirements and fees, and are specified in the proposal.'],
            ['هل يمكن إضافة لغتين أو ربط الموقع بأنظمة أخرى؟', 'Can the site support two languages or connect to other systems?',
                'يمكن بحث تعدد اللغات أو التكامل مع أنظمة أخرى بعد معرفة اللغات والبيانات والواجهات المتاحة. نوضح ما يدخل ضمن التنفيذ وما يتطلب خدمات أو تراخيص إضافية.',
                'We can assess multilingual support and integrations once the languages, data and available APIs are known. The proposal distinguishes included work from additional services or licences.'],
            ['هل تضمنون ظهور الموقع في نتائج البحث؟', 'Do you guarantee search rankings?',
                'لا يمكن ضمان ترتيب معين في محركات البحث. يمكن إدراج أساسيات تقنية ومحتوى مناسب لتحسين قابلية الفهرسة وفق نطاق المشروع، بينما تعتمد النتائج على عوامل أخرى وتحتاج متابعة مستمرة.',
                'No specific search ranking can be guaranteed. Technical foundations and suitable content can support indexability within the agreed scope, but results depend on other factors and ongoing work.'],
            ['ماذا يحدث بعد إطلاق المشروع؟', 'What happens after launch?',
                'نراجع التسليم والتشغيل وفق ما تم الاتفاق عليه. أي صيانة أو دعم أو تطوير لاحق يُحدد نطاقه ومدته وتكلفته بشكل منفصل أو ضمن اتفاق واضح؛ فلا نفترض شمول دعم مفتوح تلقائيًا.',
                'We review delivery and operation against the agreed scope. Maintenance, support or later improvements have a defined scope, term and cost, either separately or under an explicit agreement; unlimited support is not assumed.'],
        ];

        foreach ($faqs as $order => [$questionAr, $questionEn, $answerAr, $answerEn]) {
            $exists = DB::table('faqs')->where('question_ar', $questionAr)
                ->orWhere('question_en', $questionEn)->exists();
            if ($exists) {
                continue;
            }

            DB::table('faqs')->insert([
                'question_ar' => $questionAr,
                'question_en' => $questionEn,
                'answer_ar' => $answerAr,
                'answer_en' => $answerEn,
                'published' => true,
                'sort_order' => ($order + 1) * 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Published FAQ content may have been edited in the admin panel; keep it intact.
    }
};
