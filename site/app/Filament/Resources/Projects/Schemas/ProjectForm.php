<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')->alphaDash()
                    ->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('title_ar')
                    ->required(),
                TextInput::make('title_en')
                    ->required(),
                Textarea::make('summary_ar')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('summary_en')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('scope_ar')->label('نطاق العمل بالعربية — ما الذي نُفذ فعلًا؟')->columnSpanFull(),
                Textarea::make('scope_en')->label('نطاق العمل بالإنجليزية')->columnSpanFull(),
                Textarea::make('body_ar')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                Textarea::make('body_en')
                    ->required(fn (Get $get): bool => (bool) $get('published'))
                    ->columnSpanFull(),
                FileUpload::make('cover_path')->label('صورة الغلاف — ترفعها هنا لتستبدل الصورة الافتراضية')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(8192)->disk('public')->directory('projects'),
                Select::make('illustration_path')->label('صورة المثال الافتراضية')->options([
                    'assets/demos/store.png' => 'متجر',
                    'assets/demos/restaurant.png' => 'مطعم',
                    'assets/demos/booking.png' => 'حجوزات',
                    'assets/demos/real-estate.png' => 'عقارات',
                    'assets/demos/learning.png' => 'تعليم',
                    'assets/demos/analytics.png' => 'تحليلات',
                ])->visible(fn (Get $get): bool => $get('project_type') === 'external'),
                FileUpload::make('gallery_paths')->label('صور المشروع')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->maxSize(8192)->multiple()->maxFiles(10)->disk('public')->directory('projects')
                    ->columnSpanFull(),
                TagsInput::make('technologies')->label('التقنيات')
                    ->columnSpanFull(),
                Select::make('project_type')->label('نوع العرض')->options([
                    'concept' => 'نموذج تصميم أو تجربة تجريبية',
                    'client' => 'مشروع عميل منفذ بإذن العرض',
                    'external' => 'مثال خارجي من جهة أخرى — ليس من أعمال KeenGuild',
                ])->required()->default('concept')->live(),
                TextInput::make('source_name')->label('اسم صاحب المثال الخارجي')
                    ->required(fn (Get $get): bool => $get('project_type') === 'external')
                    ->visible(fn (Get $get): bool => $get('project_type') === 'external'),
                TextInput::make('source_url')->label('رابط المصدر الأصلي')
                    ->url()->rule('starts_with:https://')->maxLength(2048)
                    ->rule(fn (Get $get) => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                        if ($get('project_type') === 'external' && ! Project::isPublicDemoUrl(is_string($value) ? $value : null)) {
                            $fail('استخدم رابط HTTPS عامًا حقيقيًا للمصدر الأصلي.');
                        }
                    })
                    ->required(fn (Get $get): bool => $get('project_type') === 'external')
                    ->visible(fn (Get $get): bool => $get('project_type') === 'external'),
                Toggle::make('display_permission_confirmed')->label('تأكيد وجود إذن من العميل لعرض هذا المشروع')
                    ->helperText('لا يظهر مشروع العميل للزوار حتى لو كان منشورًا ما لم تؤكد الإذن هنا. احتفظ بإثبات الموافقة خارج الموقع.')
                    ->default(false)->visible(fn (Get $get): bool => $get('project_type') === 'client'),
                TextInput::make('demo_url')->label('رابط الديمو')
                    ->url()->rule('starts_with:https://')->maxLength(2048)
                    ->rule(fn (Get $get) => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                        if ($get('demo_status') === 'ready' && ! Project::isPublicDemoUrl(is_string($value) ? $value : null)) {
                            $fail('استخدم رابط HTTPS عامًا حقيقيًا، بدون بيانات دخول في الرابط أو عنوان محلي أو تجريبي مثل .test.');
                        }
                    })
                    ->helperText('المعاينة الداخلية بالعربية والإنجليزية تعمل من قائمة المشاريع حتى قبل النشر، وتعرض النص والصور. الديمو التفاعلي يحتاج رابط HTTPS منشورًا مستقلًا؛ لا تضع بيانات الدخول داخل الرابط.')
                    ->required(fn (Get $get): bool => $get('demo_status') === 'ready'),
                Select::make('demo_status')->label('حالة الديمو')->options([
                    'unavailable' => 'لا يوجد ديمو', 'ready' => 'ديمو جاهز',
                ])
                    ->required()
                    ->live()
                    ->default('unavailable'),
                Toggle::make('featured')
                    ->required(),
                Toggle::make('featured_in_demos')->label('عرض في قسم جرّب بنفسك بالرئيسية')
                    ->helperText('يظهر فقط إذا نُشر المشروع واكتمل وصفه وكان له رابط ديمو HTTPS جاهز. تظهر حتى ثلاث بطاقات حسب الترتيب.')
                    ->default(false),
                Toggle::make('published')->label('طلب نشر المشروع للزوار')->helperText('مشروعات العملاء تحتاج تأكيد إذن العرض؛ الأمثلة الخارجية تحتاج اسم ورابط المصدر.')->default(false),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
