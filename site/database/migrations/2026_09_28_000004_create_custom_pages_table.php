<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('title_ar', 160);
            $table->string('title_en', 160);
            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('body_ar');
            $table->longText('body_en');
            $table->string('meta_description_ar', 180)->nullable();
            $table->string('meta_description_en', 180)->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_pages');
    }
};
