<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pricing_settings')->insertOrIgnore([
            'key' => 'estimate_range_percent',
            'value' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pricing_settings')->where('key', 'estimate_range_percent')->delete();
    }
};
