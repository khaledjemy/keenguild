<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('featured_on_home')->default(false);
        });

        DB::table('services')->whereIn('slug', ['websites', 'ecommerce', 'web-apps', 'integrations'])
            ->update(['featured_on_home' => true]);
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('featured_on_home');
        });
    }
};
