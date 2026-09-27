<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('home_title_ar')->nullable();
            $table->string('home_title_en')->nullable();
            $table->text('default_description_ar')->nullable();
            $table->text('default_description_en')->nullable();
            $table->string('social_image_path')->nullable();
            $table->boolean('allow_indexing')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
