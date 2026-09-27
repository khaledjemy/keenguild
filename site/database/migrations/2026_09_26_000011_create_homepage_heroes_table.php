<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('kicker_ar', 90);
            $table->string('kicker_en', 90);
            $table->string('headline_ar', 45);
            $table->string('headline_en', 45);
            $table->string('accent_ar', 28);
            $table->string('accent_en', 28);
            $table->string('description_ar', 210);
            $table->string('description_en', 210);
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_heroes');
    }
};
