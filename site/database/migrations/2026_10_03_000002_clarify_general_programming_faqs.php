<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $changes = [
            [
                'ما الخدمات التي تقدمها KeenGuild؟', 'What services does KeenGuild offer?',
                'نعمل على تصميم وتطوير المواقع والمتاجر الإلكترونية وتطبيقات الويب، مع خدمات تصميم الواجهات والتكاملات والتحسينات التقنية. يُحدد نطاق كل خدمة حسب متطلبات المشروع.',
                'We work on websites, online stores, web applications, interface design, integrations and technical improvements. The scope of each service depends on the project requirements.',
                'KeenGuild تعمل في البرمجة والحلول الرقمية عمومًا، مثل تطبيقات الويب والأنظمة المخصصة والتكاملات وواجهات المستخدم. تصميم وتطوير المواقع والمتاجر الإلكترونية هو مجال تركيزنا الأساسي، ويُحدد نطاق أي مشروع بعد فهم متطلباته.',
                'KeenGuild works on software and digital solutions broadly, including web apps, custom systems, integrations and user interfaces. Websites and online stores are our main focus. We define each project scope after understanding its requirements.',
            ],
            [
                'كيف تُحدد تكلفة المشروع؟', 'How is a project priced?',
                'تعتمد التكلفة على الصفحات والوظائف والتصميم والتكاملات والمحتوى المطلوب. الأسعار المعروضة في الموقع استرشادية حسب النطاق المذكور، ويُحدد السعر النهائي في عرض واضح بعد مراجعة المتطلبات.',
                'Cost depends on the pages, features, design, integrations and content required. Prices shown on the site are indicative for the stated scope; the final price is set out in a clear proposal after reviewing your requirements.',
                'تعتمد التكلفة على نطاق البرمجة وتعقيد الوظائف والتصميم والتكاملات والمحتوى المطلوب. أسعار باقات المواقع المعروضة استرشادية للنطاق المذكور؛ أما الأنظمة والتطبيقات المخصصة فيُحدد سعرها بعد دراسة المتطلبات وإعداد عرض واضح.',
                'Cost depends on the software scope, feature complexity, design, integrations and content required. Listed website package prices are indicative for their stated scope; custom systems and applications are priced after requirements review and a clear proposal.',
            ],
            [
                'كم يستغرق تنفيذ الموقع أو التطبيق؟', 'How long does it take to build a website or app?',
                'تختلف المدة حسب حجم المشروع وجاهزية المحتوى وسرعة المراجعات والتكاملات المطلوبة. نحدد جدولًا تقديريًا ومراحل تسليم بعد الاتفاق على النطاق؛ ولا نعد بمدة ثابتة لكل المشاريع.',
                'Timing varies with scope, content readiness, review cycles and integrations. We outline an estimated schedule and milestones once the scope is agreed; there is no fixed timeline for every project.',
                'تختلف مدة تنفيذ أي مشروع برمجي حسب نطاقه وتعقيده وجاهزية المحتوى والبيانات وسرعة المراجعات والتكاملات المطلوبة. نحدد جدولًا تقديريًا ومراحل تسليم بعد الاتفاق على النطاق، ولا نعد بمدة ثابتة لكل المشاريع.',
                'The timeline for any software project depends on scope, complexity, content and data readiness, review cycles and integrations. We outline estimated milestones after agreeing on the scope rather than promising one fixed duration.',
            ],
            [
                'ما الذي يجب أن أجهزه قبل بدء العمل؟', 'What should I prepare before work starts?',
                'يفيد تجهيز أهداف المشروع، وصف الجمهور، قائمة الصفحات أو الوظائف الأساسية، الهوية البصرية إن وُجدت، والنصوص والصور المتاحة. إذا لم تكن كل التفاصيل جاهزة، يمكن البدء بتحديد الأولويات.',
                'It helps to prepare your goals, audience, essential pages or features, any existing brand assets, and available copy and images. If everything is not ready, we can begin by identifying priorities.',
                'يفيد تجهيز هدف المشروع والمستخدمين المستهدفين والوظائف الأساسية والأنظمة أو البيانات المطلوب ربطها. للمواقع، أضف الصفحات والمحتوى والهوية البصرية المتاحة. وإذا لم تكن كل التفاصيل جاهزة، نبدأ بتحديد الأولويات.',
                'Prepare the project goal, target users, essential features and any systems or data to connect. For websites, include the pages, content and available brand assets. If details are incomplete, we can start by prioritising requirements.',
            ],
        ];

        foreach ($changes as [$questionAr, $questionEn, $oldAr, $oldEn, $newAr, $newEn]) {
            DB::table('faqs')
                ->where('question_ar', $questionAr)
                ->where('question_en', $questionEn)
                ->where('answer_ar', $oldAr)
                ->where('answer_en', $oldEn)
                ->update(['answer_ar' => $newAr, 'answer_en' => $newEn, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Keep published copy and any owner edits intact.
    }
};
