<?php

namespace Database\Seeders;

use App\Models\ContactChannel;
use App\Models\InquirySetting;
use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class ApprovedLegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'privacy' => [
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'body_ar' => <<<'TEXT'
آخر تحديث: 28 سبتمبر 2026

تدير KeenGuild هذا الموقع. للاستفسار عن بياناتك أو طلب الاطلاع عليها أو تصحيحها أو حذفها، راسل info@keenguild.com.

عند إرسال طلب مشروع، نجمع الاسم والبريد الإلكتروني ووصف المشروع. رقم الهاتف والباقة المختارة اختياريان. نستخدم هذه البيانات لدراسة الطلب والتواصل بشأنه فقط، ولا نستخدمها لإرسال رسائل تسويقية دون موافقة مستقلة. لا ترسل كلمات مرور أو بيانات دفع أو بيانات حساسة في وصف المشروع.

نحتفظ بطلبات المشاريع لمدة 12 شهرًا من تاريخ استلامها ثم نحذفها آليًا من قاعدة بيانات الموقع، ما لم يقتضِ التزام قانوني أو نزاع قائم الاحتفاظ ببيانات محددة لمدة أطول. قد تبقى نسخ احتياطية لفترة محدودة وفق إعدادات مزود الاستضافة. ويمكنك طلب حذف بياناتك قبل انتهاء المدة عبر البريد المذكور، مع مراعاة الالتزامات القانونية.

تُستضاف بيانات الموقع لدى Hostinger عند إطلاقه. قد تُعالج البيانات في مركز البيانات الذي يُختار عند إعداد الاستضافة؛ لم يُحدد موقعه النهائي بعد. يعالج مزود الاستضافة البيانات اللازمة لتشغيل الخدمة. إذا استخدمت المساعد التفاعلي طوعًا، فقد تُرسل الرسالة التي تكتبها إلى خدمة المساعد المنفصلة عبر Cloudflare لمعالجة الرد؛ لا تضع بيانات حساسة في المحادثة.

يستخدم الموقع ملفات الارتباط أو التخزين الضروريين لتشغيل الجلسة وحماية النماذج. لا نُفعّل أدوات تحليلات أو ملفات ارتباط تسويقية غير أساسية عند الإطلاق. قد تحمل الروابط الخارجية وخدماتها سياسات مستقلة. نحدّ الوصول إلى الطلبات بحسابات الإدارة المصرح بها ونتخذ تدابير تقنية معقولة، لكن لا يمكن ضمان أمان مطلق عبر الإنترنت.

قد نحدّث هذه السياسة إذا تغيرت طريقة تشغيل الموقع أو خدماته، وسنُظهر تاريخ التحديث عند التغيير.
TEXT,
                'body_en' => <<<'TEXT'
Last updated: 28 September 2026

KeenGuild operates this website. For questions about your data or requests to access, correct or delete it, contact info@keenguild.com.

When you send a project inquiry, we collect your name, email address and project brief. Your phone number and selected package are optional. We use this information only to assess and respond to the inquiry, not to send marketing without separate consent. Do not include passwords, payment details or sensitive information in your brief.

We keep project inquiries for 12 months after receipt and then automatically delete them from the website database, unless a legal obligation or ongoing dispute requires limited data to be kept longer. Backups may retain copies for a limited time under the hosting provider's settings. You can request earlier deletion using the email above, subject to legal obligations.

The site will be hosted by Hostinger at launch. Data may be processed in the data centre selected when hosting is configured; its final location has not yet been chosen. The hosting provider processes data needed to run the service. If you voluntarily use the interactive assistant, the message you type may be sent to the separate assistant service through Cloudflare to generate a reply. Do not share sensitive data in chat.

The site uses only cookies or storage necessary for sessions and form protection. No non-essential analytics or marketing cookies are enabled at launch. External links and services may have their own policies. We limit access to inquiries to authorised administrators and take reasonable technical precautions, but no online service can guarantee absolute security.

We may update this policy if the website or its services change, and will show the updated date.
TEXT,
            ],
            'terms' => [
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms of Use',
                'body_ar' => <<<'TEXT'
آخر تحديث: 28 سبتمبر 2026

يقدم موقع KeenGuild معلومات عن خدمات تصميم وتطوير المواقع والمنتجات الرقمية ومحتوى توضيحيًا. باستخدامك الموقع، توافق على استخدامه بصورة مشروعة وألا تحاول تعطيله أو الوصول غير المصرح به إلى أنظمته.

الأسعار المعروضة أسعار بداية أو تقديرات استرشادية للنطاق المذكور، وقد تتغير حسب متطلبات المشروع. إرسال طلب مشروع أو استخدام أي حاسبة في الموقع لا يُنشئ عقدًا أو عرضًا ملزمًا. يُتفق على نطاق العمل والسعر والمدة والدفعات كتابةً قبل بدء التنفيذ.

المحتوى والتصميمات الأصلية الخاصة بـ KeenGuild مخصصة للتعريف بالخدمات. لا يمنح تصفح الموقع إذنًا بإعادة نشرها أو استخدامها تجاريًا. النماذج الخارجية وروابط الديمو المعروضة تعود إلى أصحابها ولا تمثل أعمالًا نفذتها KeenGuild، وتخضع لشروط أصحابها.

قد يتغير محتوى الموقع أو يتوقف مؤقتًا للصيانة. نسعى إلى دقة المعلومات، لكن المحتوى العام ليس ضمانًا لنتيجة محددة أو بديلًا عن اتفاق مشروع فردي. للاستفسارات راسل info@keenguild.com. لا تحل هذه الشروط محل أي اتفاق مكتوب منفصل مع العميل.
TEXT,
                'body_en' => <<<'TEXT'
Last updated: 28 September 2026

KeenGuild provides information about website and digital product design and development, together with explanatory content. Use the site lawfully and do not disrupt it or attempt unauthorised access.

Displayed prices are starting prices or indicative estimates for the stated scope and may change with project requirements. Sending an inquiry or using a calculator on the site does not create a contract or binding offer. Scope, price, timing and payments are agreed in writing before work starts.

Original KeenGuild content and designs describe our services. Browsing the site does not grant permission to republish or commercially use them. The external examples and demo links belong to their respective owners, are not work delivered by KeenGuild, and are subject to those owners' terms.

Site content may change or be temporarily unavailable for maintenance. We aim for accurate information, but general site content is not a guarantee of a particular outcome or a substitute for an individual project agreement. For questions, contact info@keenguild.com. These terms do not replace any separate written client agreement.
TEXT,
            ],
        ];

        foreach ($pages as $type => $copy) {
            $page = LegalPage::firstOrNew(['type' => $type]);
            if ($page->exists && $page->published) {
                continue;
            }
            if ($page->exists && ! str_starts_with((string) $page->body_en, 'Draft for review')) {
                continue;
            }

            $page->fill($copy + ['published' => true]);
            $page->save();
        }

        if (LegalPage::query()->whereIn('type', ['privacy', 'terms'])->publiclyVisible()->count() === 2) {
            ContactChannel::firstOrCreate(
                ['platform' => 'Email', 'url' => 'info@keenguild.com'],
                ['published' => true, 'sort_order' => 0],
            );

            $setting = InquirySetting::current() ?? new InquirySetting();
            $setting->intake_requested = true;
            $setting->save();
        }
    }
}
