<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('location', 16);
            $table->string('target', 32);
            $table->string('label_ar', 80);
            $table->string('label_en', 80);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->index(['location', 'published', 'sort_order']);
        });

        $defaults = [
            ['header', 'section:services', 'الخدمات', 'Services'],
            ['header', 'section:demos', 'التجارب', 'Experiments'],
            ['header', 'section:work', 'أعمالنا', 'Our work'],
            ['header', 'articles', 'المقالات', 'Articles'],
            ['header', 'services', 'كل الخدمات', 'All services'],
            ['header', 'pricing', 'الأسعار', 'Pricing'],
            ['overlay', 'home', 'الرئيسية', 'Home'],
            ['overlay', 'about', 'من نحن', 'About'],
            ['overlay', 'services', 'كل الخدمات', 'All services'],
            ['overlay', 'pricing', 'الأسعار', 'Pricing'],
            ['overlay', 'work', 'الأعمال والديمو', 'Work and demos'],
            ['overlay', 'articles', 'المقالات', 'Articles'],
            ['overlay', 'faqs', 'الأسئلة الشائعة', 'FAQ'],
            ['overlay', 'contact', 'تواصل معنا', 'Contact'],
            ['footer', 'home', 'الرئيسية', 'Home'],
            ['footer', 'services', 'الخدمات', 'Services'],
            ['footer', 'pricing', 'الأسعار', 'Pricing'],
            ['footer', 'work', 'الأعمال', 'Work'],
            ['footer', 'articles', 'المقالات', 'Articles'],
            ['footer', 'contact', 'تواصل', 'Contact'],
            ['footer', 'about', 'من نحن', 'About'],
            ['footer', 'faqs', 'الأسئلة الشائعة', 'FAQ'],
        ];

        foreach ($defaults as $index => [$location, $target, $ar, $en]) {
            DB::table('navigation_items')->insert([
                'location' => $location, 'target' => $target, 'label_ar' => $ar, 'label_en' => $en,
                'sort_order' => $index, 'published' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
