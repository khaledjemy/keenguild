<?php

use App\Services\HomepageCopyDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $record = DB::table('homepage_contents')->orderBy('id')->first();
        if ($record === null) {
            return;
        }

        $content = json_decode($record->content ?? '{}', true);
        if (! is_array($content)) {
            return;
        }

        $previousDefaults = [
            'menu_heading_ar' => 'كل ما تحتاجهفي مكان واحد.',
            'menu_heading_en' => 'كل ما تحتاجهفي مكان واحد.',
            'proof_heading_ar' => 'من الفكرةإلى أثر حقيقي.',
            'proof_heading_en' => 'From ideaTo real impact.',
            'services_heading_ar' => 'أفكار كبيرة،تنفيذ أكثر هدوءًا.',
            'services_heading_en' => 'Big ideas,Quieter execution.',
            'demos_heading_ar' => 'تصورات تفاعلية.تجربة سريعة.',
            'demos_heading_en' => 'Interactive concepts.A quick experience.',
            'work_heading_ar' => 'تصميمات ممكنةلشغلك القادم.',
            'work_heading_en' => 'Possible designsFor your next project.',
            'journal_heading_ar' => 'أفكار تساعدكتبني بشكل أفضل.',
            'journal_heading_en' => 'Ideas to help youBuild better.',
            'reel_heading_ar' => 'Watch ideasbecome real.',
            'reel_heading_en' => 'Watch ideasbecome real.',
            'reel_kicker_ar' => '06 / SEE THE WORK IN MOTION',
            'contact_heading_ar' => 'عندك فكرة؟خلينا نبنيها.',
            'contact_heading_en' => 'Have an idea?Let’s build it.',
        ];
        $defaults = app(HomepageCopyDefaults::class)->all();
        $changed = false;

        foreach ($previousDefaults as $key => $oldValue) {
            if (($content[$key] ?? null) === $oldValue && isset($defaults[$key])) {
                $content[$key] = $defaults[$key];
                $changed = true;
            }
        }

        if ($changed) {
            DB::table('homepage_contents')->where('id', $record->id)->update([
                'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Preserve content edited by the site owner.
    }
};
