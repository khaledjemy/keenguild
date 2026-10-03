<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class KeenGuildClientProjectsSeeder extends Seeder
{
    public function run(): void
    {
        Project::query()->firstOrCreate(['slug' => 'italiano988-import-export'], [
            'title_ar' => 'Italiano988 — موقع للاستيراد والتصدير',
            'title_en' => 'Italiano988 — Import & export website',
            'summary_ar' => 'موقع تعريفي ثنائي اللغة يعرض خدمات الاستيراد والتصدير وصفحات الشركة ووسائل التواصل.',
            'summary_en' => 'A bilingual company website presenting import and export services, company information and contact options.',
            'scope_ar' => 'تطوير واجهة الموقع باستخدام React، مع صفحات للخدمات ومن نحن والتواصل، ومحتوى عربي وإنجليزي وروابط تواصل.',
            'scope_en' => 'React frontend development with services, about and contact pages, Arabic and English content, and contact links.',
            'body_ar' => 'Italiano988 موقع منشور لشركة تعمل في الاستيراد والتصدير. يعرض الخدمات ومعلومات الشركة بواجهتين عربية وإنجليزية. الرابط يتيح مشاهدة النسخة الحية؛ لا ننسب للمشروع نتائج تجارية أو وظائف لم تُتحقق منها.',
            'body_en' => 'Italiano988 is a live import and export company website. It presents services and company information in Arabic and English. The live link shows the published interface; no unverified business outcomes or features are claimed.',
            'illustration_path' => 'assets/projects/italiano988.png',
            'technologies' => ['React', 'Bootstrap'],
            'demo_url' => 'https://italiano988.com/',
            'demo_status' => 'ready',
            'project_type' => 'client',
            'display_permission_confirmed' => true,
            'featured' => true,
            'featured_in_demos' => false,
            'published' => true,
            'sort_order' => 0,
        ]);

        Project::query()->firstOrCreate(['slug' => 'kamal-orabi-portfolio'], [
            'title_ar' => 'د. كمال عرابي — الموقع الشخصي والمهني',
            'title_en' => 'Dr. Kamal Orabi — Professional website',
            'summary_ar' => 'موقع شخصي ثنائي اللغة يعرّف بخبرة د. كمال عرابي ومجالات عمله ويعرض الشركة والصور والفيديوهات.',
            'summary_en' => 'A bilingual professional website presenting Dr. Kamal Orabi, his areas of expertise, company, gallery and videos.',
            'scope_ar' => 'تطوير واجهة React للموقع مع صفحات ومقاطع تعريفية، عرض المهارات والأعمال، معرض صور وفيديوهات، ودعم العربية والإنجليزية.',
            'scope_en' => 'React frontend with profile and company sections, expertise, photo and video galleries, and Arabic and English content.',
            'body_ar' => 'موقع منشور لد. كمال عرابي يعرض ملفه المهني ومجالات عمله في الاستشارات وتطوير الأعمال، إلى جانب معلومات عن TradeMark Groups. الرابط يفتح موقع العميل الحي.',
            'body_en' => 'A live professional website for Dr. Kamal Orabi, presenting his business consulting and development work alongside TradeMark Groups. The link opens the client website.',
            'illustration_path' => 'assets/projects/kamalorabi.jpg',
            'technologies' => ['React', 'Vite'],
            'demo_url' => 'https://kamalorabi.com/',
            'demo_status' => 'ready',
            'project_type' => 'client',
            'display_permission_confirmed' => true,
            'featured' => true,
            'featured_in_demos' => false,
            'published' => true,
            'sort_order' => 1,
        ]);
    }
}
