<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class EditorialStarterSeeder extends Seeder
{
    public function run(): void
    {
        // Never overwrite edits made through the admin panel.
        LegalPage::firstOrCreate(['type' => 'privacy'], [
            'title_ar' => 'سياسة الخصوصية', 'title_en' => 'Privacy Policy', 'published' => false,
            'body_ar' => <<<'TEXT'
مسودة للمراجعة قبل النشر — لا تُعد سياسة معتمدة بعد.

1. من يدير البيانات؟
KeenGuild تدير الموقع. يجب استكمال اسم الجهة القانوني ووسيلة تواصل رسمية قبل اعتماد هذه الصفحة.

2. البيانات التي قد نجمعها
عند تفعيل نموذج طلب المشروع، يرسل الزائر اسمه وبريده الإلكتروني ووصف مشروعه، ويمكنه إضافة رقم هاتف واختيار باقة. تُحفظ هذه البيانات لمراجعة الطلب والرد عليه. يرجى عدم إرسال كلمات مرور أو بيانات دفع أو بيانات حساسة ضمن الوصف.

3. سبب الاستخدام والمشاركة
نستخدم بيانات الطلب للتواصل بشأن المشروع وتقييم نطاقه، ولا نستخدمها للتسويق من دون ترتيب منفصل. قد يعالج مزود الاستضافة البيانات اللازمة لتشغيل الموقع. يجب تحديد المزود ومكان المعالجة وأي خدمات تحليلات أو ملفات تعريف ارتباط اختيارية قبل النشر.

4. مدة الحفظ والأمان
يجب تحديد مدة احتفاظ فعلية بطلبات العملاء وآلية الحذف قبل اعتماد السياسة. نحدّ الوصول إلى لوحة الإدارة بالمستخدمين المصرح لهم، لكن لا يمكن ضمان أمان مطلق لأي خدمة عبر الإنترنت.

5. حقوق صاحب البيانات والتواصل
يمكن طلب الاطلاع على بيانات الطلب أو تصحيحها أو حذفها، وفق القانون الساري والالتزامات المشروعة. يجب إضافة وسيلة تواصل رسمية لهذه الطلبات، وبيان كيفية تقديم شكوى للجهة المختصة حيث ينطبق ذلك.

6. التحديثات
سنراجع هذه السياسة عند تغيير طريقة جمع البيانات أو تشغيل أدوات جديدة، ونوضح تاريخ آخر تحديث عند نشر النسخة المعتمدة.
TEXT,
            'body_en' => <<<'TEXT'
Draft for review before publication — this is not an approved policy yet.

1. Who handles the data?
KeenGuild operates this website. Add the legal entity name and an official contact method before approval.

2. Data we may collect
When the project inquiry form is enabled, visitors provide a name, email address and project brief; a phone number and preferred package are optional. We store this information to review and respond to the inquiry. Please do not include passwords, payment details or sensitive information in the brief.

3. Purpose and sharing
We use inquiry details to discuss and assess the proposed project, not for marketing without a separate arrangement. A hosting provider may process data needed to run the site. Identify the provider, processing location, and any optional analytics or cookies before publication.

4. Retention and security
Specify an actual retention period and deletion process for inquiries before approving this policy. Access to the admin panel is limited to authorised users, but no online service can promise absolute security.

5. Your choices and contact
You may request access, correction or deletion of your inquiry data, subject to applicable law and legitimate obligations. Add an official contact method for these requests and information about complaints to the competent authority where applicable.

6. Updates
We will review this policy when data collection or site tools change. The approved page will display its last update date.
TEXT,
        ]);

        LegalPage::firstOrCreate(['type' => 'terms'], [
            'title_ar' => 'الشروط والأحكام', 'title_en' => 'Terms of Use', 'published' => false,
            'body_ar' => <<<'TEXT'
مسودة للمراجعة قبل النشر — يلزم اعتمادها وفق طريقة العمل الفعلية.

1. استخدام الموقع
يعرض موقع KeenGuild معلومات عن خدمات التصميم والتطوير ومحتوى توضيحيًا. يرجى استخدامه بصورة مشروعة، وعدم محاولة تعطيله أو الوصول غير المصرح به إلى أنظمته.

2. المعلومات والأسعار
الأسعار المعروضة نقاط بداية أو تقديرات استرشادية بحسب النطاق المذكور. لا يشكّل إرسال طلب أو استخدام حاسبة الأسعار عقدًا أو عرضًا ملزمًا؛ يُحدّد نطاق العمل والسعر والمدة والدفعات في اتفاق مستقل قبل بدء التنفيذ.

3. الملكية الفكرية
المحتوى والتصميمات المعروضة مخصصة للتعريف بخدمات الموقع. لا يمنح تصفح الموقع ترخيصًا لإعادة نشرها أو استخدامها تجاريًا. تبقى حقوق المواد التابعة للغير لأصحابها.

4. الروابط الخارجية
قد تقود بعض الروابط إلى خدمات مستقلة لا يديرها KeenGuild. راجع شروط تلك الخدمات وسياسات خصوصيتها قبل استخدامها.

5. توفر الموقع والتعديلات
قد تُحدث صفحات الموقع أو تتوقف مؤقتًا للصيانة. يُعلن تاريخ تحديث هذه الشروط عند اعتماد نسخة جديدة. لا تحل هذه الشروط محل اتفاق المشروع الخاص بكل عميل.

6. التواصل
يجب إضافة وسيلة التواصل الرسمية والاسم القانوني للجهة قبل النشر النهائي، ومراجعة القانون المختص وآلية تسوية النزاعات مع مختص قانوني.
TEXT,
            'body_en' => <<<'TEXT'
Draft for review before publication — approval must reflect actual business practices.

1. Using the site
KeenGuild presents design and development services and explanatory content. Use the site lawfully; do not disrupt it or attempt unauthorised access.

2. Information and prices
Displayed prices are starting points or indicative estimates for the stated scope. Sending an inquiry or using the calculator does not create a contract or binding offer. Scope, price, schedule and payments are agreed separately before work begins.

3. Intellectual property
The site's content and designs describe its services. Browsing does not grant permission to republish or commercially use them. Third-party materials remain the property of their owners.

4. External links
Some links may lead to independent services outside KeenGuild's control. Review their terms and privacy policies before using them.

5. Availability and changes
Pages may change or be temporarily unavailable for maintenance. An approved revision will show its update date. These website terms do not replace an individual client project agreement.

6. Contact
Add an official contact method and legal entity name before final publication. Have qualified counsel review the governing law and dispute process.
TEXT,
        ]);

        $category = ArticleCategory::firstOrCreate(['slug' => 'web-planning'], [
            'name_ar' => 'تخطيط المواقع', 'name_en' => 'Website planning',
            'description_ar' => 'قرارات عملية قبل تطوير موقعك.',
            'description_en' => 'Practical decisions before building your website.',
        ]);

        foreach ($this->articles() as $article) {
            Article::firstOrCreate(['slug' => $article['slug']], $article + [
                'article_category_id' => $category->id,
                'published' => true,
                'published_at' => now(),
            ]);
            Article::where('slug', $article['slug'])->whereNull('cover_path')
                ->update(['cover_path' => $article['cover_path']]);
        }
    }

    private function articles(): array
    {
        return [
            [
                'slug' => 'website-brief-before-design',
                'cover_path' => 'assets/articles/website-brief.png',
                'title_ar' => 'كيف تكتب وصفًا واضحًا لمشروع موقعك؟',
                'title_en' => 'How to write a useful website brief',
                'summary_ar' => 'خمس نقاط تساعدك على شرح هدف الموقع ونطاقه قبل طلب عرض السعر.',
                'summary_en' => 'Five points that clarify your website goal and scope before requesting a quote.',
                'body_ar' => "وصف المشروع الجيد لا يحتاج إلى مصطلحات تقنية كثيرة. ابدأ بالمشكلة التي تريد حلها، ثم صف الجمهور الذي سيستخدم الموقع وما الذي تريد منه فعله.\n\n1. الهدف: هل تريد استقبال طلبات، عرض خدمات، بيع منتجات، أم تنظيم عمليات داخلية؟ اختر هدفًا أساسيًا يمكن قياسه.\n\n2. الجمهور: من سيستخدم الموقع؟ وما المعلومات التي يحتاجها قبل التواصل أو الشراء؟\n\n3. الصفحات والوظائف: اكتب قائمة أولية بالصفحات، والنماذج، وطرق الدفع أو التكاملات المطلوبة إن وجدت. فرّق بين الضروري للنسخة الأولى وما يمكن تأجيله.\n\n4. المحتوى والمسؤوليات: حدّد من سيجهز النصوص والصور والهوية البصرية، ومن سيعتمدها. غياب المحتوى قد يؤخر المشروع أكثر من كتابة الشفرة.\n\n5. القيود: شارك موعد الإطلاق المستهدف، والميزانية التقريبية، والأنظمة الحالية التي يجب ربطها.\n\nبعد ذلك اطلب تقسيم العرض إلى نطاق واضح، واستثناءات، ومراحل تسليم. هذا يجعل مقارنة العروض أسهل ويقلل المفاجآت أثناء التنفيذ.",
                'body_en' => "A useful project brief does not need technical jargon. Start with the problem you want to solve, then describe your audience and the action you want visitors to take.\n\n1. Goal: Do you want inquiries, service discovery, product sales, or an internal workflow? Pick one primary goal you can measure.\n\n2. Audience: Who will use the site, and what do they need to know before contacting or buying from you?\n\n3. Pages and features: List the initial pages, forms, payments or integrations you need. Separate launch essentials from ideas that can wait.\n\n4. Content and ownership: Decide who will prepare and approve copy, images and brand assets. Missing content can delay a project more than development.\n\n5. Constraints: Share your target launch date, approximate budget and systems the website must connect to.\n\nAsk for a proposal that separates scope, exclusions and delivery milestones. That makes offers easier to compare and reduces surprises during the build.",
            ],
            [
                'slug' => 'landing-page-or-company-website',
                'cover_path' => 'assets/articles/landing-or-website.png',
                'title_ar' => 'صفحة هبوط أم موقع شركة: أيهما تحتاج؟',
                'title_en' => 'Landing page or company website: which fits?',
                'summary_ar' => 'طريقة بسيطة للاختيار بناءً على عدد الرسائل والجمهور ومسار العميل.',
                'summary_en' => 'A practical way to choose based on messages, audiences and the customer journey.',
                'body_ar' => "صفحة الهبوط تركز عادةً على عرض واحد ودعوة واضحة لاتخاذ إجراء. تناسب حملة محددة أو اختبار فكرة عندما يحتاج الزائر إلى قرار واحد، مثل حجز استشارة أو إرسال طلب.\n\nموقع الشركة ينظم عدة موضوعات: من نحن، الخدمات، الأعمال، الأسئلة المتكررة ووسائل التواصل. يكون أنسب عندما يحتاج العميل إلى مقارنة خدمات أو التحقق من خبرتك قبل التواصل.\n\nلا تختَر بناءً على عدد الصفحات وحده. اسأل: كم نوعًا من الزوار لدي؟ وهل يحتاج كل نوع إلى معلومات مختلفة؟ هل ستضيف مقالات أو وظائف جديدة قريبًا؟\n\nيمكن البدء بصفحة هبوط قابلة للتوسع إذا كان الهدف ضيقًا والمحتوى محدودًا. أما إذا كانت خدماتك متعددة وتحتاج إلى هيكل تنقل واضح، فموقع الشركة بداية أكثر اتساقًا. في الحالتين، حدد هدف التحويل والمحتوى المطلوب قبل اختيار التصميم.",
                'body_en' => "A landing page usually focuses on one offer and one clear call to action. It suits a specific campaign or an early idea test when visitors have one decision to make, such as booking a consultation.\n\nA company website organises several topics: about, services, work, FAQs and contact details. It is more useful when customers need to compare services or assess your experience before getting in touch.\n\nDo not decide by page count alone. Ask how many audience types you serve, whether each needs different information, and whether you plan to add articles or features soon.\n\nA focused landing page can be a sensible start if the goal and content are narrow. If you have multiple services and need clear navigation, a company website is a more coherent foundation. In either case, define the desired action and prepare the content before choosing the visual design.",
            ],
            [
                'slug' => 'what-website-quote-should-include',
                'cover_path' => 'assets/articles/website-quote.png',
                'title_ar' => 'ما الذي يجب توضيحه في عرض سعر الموقع؟',
                'title_en' => 'What should a website quote explain?',
                'summary_ar' => 'افهم النطاق والاستثناءات والتكاليف المستمرة قبل مقارنة الأرقام.',
                'summary_en' => 'Understand scope, exclusions and ongoing costs before comparing numbers.',
                'body_ar' => "السعر وحده لا يكفي لمقارنة عروض تطوير المواقع. قد يشير رقمان مختلفان إلى نطاقين مختلفين تمامًا.\n\nاطلب وصفًا للصفحات والوظائف المتفق عليها، وعدد جولات المراجعة، وما إذا كان إدخال المحتوى والترجمة والتصوير ضمن العمل. تأكد من تحديد ما إذا كان التصميم مخصصًا أو مبنيًا على قالب، وما إذا كانت لوحة الإدارة والتدريب مشمولين.\n\nاسأل عن العناصر غير المشمولة: الدومين، الاستضافة، التراخيص، رسوم بوابات الدفع، ربط الشحن، كتابة المحتوى والصيانة بعد الإطلاق. ثم راجع طريقة التعامل مع الطلبات الإضافية والتغييرات بعد الموافقة.\n\nأخيرًا، اطلب مراحل التسليم ومعايير قبول واضحة، وما الذي يحدث إذا تأخر المحتوى أو الموافقات. تقدير السعر المبكر نقطة بداية للمحادثة، أما الالتزام النهائي فيحتاج إلى نطاق مكتوب يتفق عليه الطرفان.",
                'body_en' => "Price alone is not enough to compare website proposals. Two different numbers may describe very different scopes.\n\nAsk for the agreed pages and features, review rounds, and whether content entry, translation or photography are included. Clarify whether the design is custom or template-based, and whether admin access and training are part of delivery.\n\nCheck exclusions such as domain registration, hosting, licences, payment processing fees, shipping integrations, copywriting and post-launch maintenance. Ask how extra requests and changes after approval are priced.\n\nFinally, look for milestones, acceptance criteria and a plan for delayed content or approvals. An early estimate starts the conversation; a final commitment needs a written scope accepted by both sides.",
            ],
        ];
    }
}
