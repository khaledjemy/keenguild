<?php

namespace App\Filament\Pages;

use App\Filament\AdminNavigationGroups;
use App\Models\InquirySetting;
use App\Models\SeoSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class SiteSettings extends Page
{
    protected static ?string $navigationLabel = 'إعدادات الموقع';

    protected static string | \UnitEnum | null $navigationGroup = AdminNavigationGroups::SETTINGS;

    protected static ?int $navigationSort = 1;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $slug = 'site-settings';

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'إعدادات الموقع';
    }

    public function mount(): void
    {
        $seo = SeoSetting::current();

        $this->form->fill([
            'intake_requested' => app(\App\Services\InquiryAvailability::class)->requested(),
            'allow_indexing' => $seo?->allow_indexing ?? true,
            'home_title_ar' => $seo?->home_title_ar,
            'home_title_en' => $seo?->home_title_en,
            'default_description_ar' => $seo?->default_description_ar,
            'default_description_en' => $seo?->default_description_en,
            'social_image_path' => $seo?->social_image_path,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('تشغيل وإيقاف')->description('مفاتيح تشغيل الموقع، وليست سجلات قابلة للإضافة.')
                ->schema([
                    Toggle::make('intake_requested')->label('استقبال طلبات المشاريع')->helperText('عند الإيقاف لا تُقبل طلبات جديدة. وعند التشغيل يلزم وجود سياسة خصوصية صالحة قبل عرض النموذج.'),
                    Toggle::make('allow_indexing')->label('السماح بفهرسة الموقع')->helperText('إيقاف الفهرسة يضيف noindex ويمنع الزحف؛ لا يضمن إزالة نتائج ظهرت سابقًا.'),
                ])->columns(2),
            Section::make('بيانات الظهور والمشاركة')->description('العناوين والوصف الافتراضي المستخدمان في نتائج البحث ومشاركة الروابط.')
                ->schema([
                    TextInput::make('home_title_ar')->label('عنوان الرئيسية بالعربية')->maxLength(70),
                    TextInput::make('home_title_en')->label('عنوان الرئيسية بالإنجليزية')->maxLength(70),
                    Textarea::make('default_description_ar')->label('الوصف الافتراضي بالعربية')->maxLength(180),
                    Textarea::make('default_description_en')->label('الوصف الافتراضي بالإنجليزية')->maxLength(180),
                    FileUpload::make('social_image_path')->label('صورة مشاركة الرابط')->image()->disk('public')->directory('seo')->maxSize(4096)->columnSpanFull(),
                ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data): void {
            $inquiry = InquirySetting::current() ?? new InquirySetting;
            $inquiry->intake_requested = (bool) $data['intake_requested'];
            $inquiry->save();

            $seo = SeoSetting::current() ?? new SeoSetting;
            $seo->fill(collect($data)->except('intake_requested')->all());
            $seo->save();
        });

        Notification::make()->title('تم حفظ إعدادات الموقع')->success()->send();
    }
}
