<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category')->default('web');
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('body_ar')->nullable();
            $table->longText('body_en')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('base_price_egp')->nullable();
            $table->string('pricing_mode')->default('estimate');
            $table->unsignedSmallInteger('included_pages')->default(0);
            $table->json('included_features')->nullable();
            $table->json('excluded_costs')->nullable();
            $table->boolean('published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['service_id', 'slug']);
        });

        Schema::create('price_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('label_ar');
            $table->string('label_en');
            $table->string('calculation_type')->default('fixed');
            $table->unsignedInteger('amount_egp')->default(0);
            $table->unsignedSmallInteger('percent')->default(0);
            $table->unsignedSmallInteger('max_quantity')->default(1);
            $table->boolean('published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['package_id', 'code']);
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('body_ar')->nullable();
            $table->longText('body_en')->nullable();
            $table->string('cover_path')->nullable();
            $table->json('gallery_paths')->nullable();
            $table->json('technologies')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('demo_status')->default('unavailable');
            $table->boolean('featured')->default(false);
            $table->boolean('published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pricing_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->decimal('value', 8, 3);
            $table->timestamps();
        });

        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('project_brief');
            $table->json('selected_options')->nullable();
            $table->unsignedInteger('indicative_total_egp')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
        Schema::dropIfExists('pricing_settings');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('price_options');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('services');
    }
};
