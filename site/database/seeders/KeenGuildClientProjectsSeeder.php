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
    }
}
