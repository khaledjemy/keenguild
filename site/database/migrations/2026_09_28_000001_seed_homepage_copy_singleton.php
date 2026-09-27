<?php

use App\Services\HomepageCopyDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_contents')) {
            return;
        }

        $defaults = app(HomepageCopyDefaults::class)->all();
        if ($defaults === []) {
            throw new RuntimeException('Homepage source copy is missing; cannot initialize its editor.');
        }

        $record = DB::table('homepage_contents')->orderBy('id')->first();
        if ($record === null) {
            DB::table('homepage_contents')->insert([
                'content' => json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        $saved = json_decode($record->content ?? '{}', true);
        $saved = is_array($saved) ? $saved : [];
        DB::table('homepage_contents')->where('id', $record->id)->update([
            'content' => json_encode(array_replace($defaults, $saved), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Preserve owner-authored copy on rollback.
    }
};
