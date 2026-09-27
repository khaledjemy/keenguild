<?php

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

        if (($content['contact_button_ar'] ?? null) === 'ابدأ محادثة قصيرة ↗') {
            $content['contact_button_ar'] = 'ابدأ مشروعك ↗';
        }
        if (($content['contact_button_en'] ?? null) === 'Start a short conversation ↗') {
            $content['contact_button_en'] = 'Start a project ↗';
        }

        DB::table('homepage_contents')->where('id', $record->id)->update([
            'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Do not erase content edited by the site owner.
    }
};
