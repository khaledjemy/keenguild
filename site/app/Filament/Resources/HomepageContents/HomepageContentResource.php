<?php

namespace App\Filament\Resources\HomepageContents;

use App\Filament\Resources\HomepageContents\Pages\EditHomepageContent;
use App\Filament\Resources\HomepageContents\Pages\ListHomepageContents;
use App\Models\HomepageContent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomepageContentResource extends Resource
{
    protected static ?string $model = HomepageContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'نصوص الرئيسية';

    protected static ?string $modelLabel = 'نصوص الرئيسية';

    protected static ?string $pluralModelLabel = 'نصوص الرئيسية';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        $groups = [
            'الهيدر وهوية الاستوديو' => [
                'nav_studio' => ['وصف الاستوديو', false],
                'nav_availability' => ['حالة قبول المشاريع', false],
                'nav_cta' => ['زر ابدأ مشروعك', false],
                'orbit_kicker' => ['عبارة قسم اللوجو المتحرك', false],
            ],
            'القائمة المفتوحة' => [
                'menu_heading' => ['عنوان القائمة', false],
                'menu_description' => ['وصف القائمة', true],
                'menu_cta' => ['زر التواصل في القائمة', false],
                'menu_1_title' => ['بطاقة القائمة ١: العنوان', false],
                'menu_1_description' => ['بطاقة القائمة ١: الوصف', true],
                'menu_2_title' => ['بطاقة القائمة ٢: العنوان', false],
                'menu_2_description' => ['بطاقة القائمة ٢: الوصف', true],
                'menu_3_title' => ['بطاقة القائمة ٣: العنوان', false],
                'menu_3_description' => ['بطاقة القائمة ٣: الوصف', true],
                'menu_4_title' => ['بطاقة القائمة ٤: العنوان', false],
                'menu_4_description' => ['بطاقة القائمة ٤: الوصف', true],
            ],
            'لماذا KeenGuild؟' => [
                'proof_kicker' => ['النص الصغير', false],
                'proof_heading' => ['العنوان', false],
                'proof_description' => ['النص الجانبي', true],
                'proof_1_title' => ['البطاقة ١: العنوان', false],
                'proof_1_description' => ['البطاقة ١: الوصف', true],
                'proof_2_title' => ['البطاقة ٢: العنوان', false],
                'proof_2_description' => ['البطاقة ٢: الوصف', true],
                'proof_3_title' => ['البطاقة ٣: العنوان', false],
                'proof_3_description' => ['البطاقة ٣: الوصف', true],
                'proof_4_title' => ['البطاقة ٤: العنوان', false],
                'proof_4_description' => ['البطاقة ٤: الوصف', true],
            ],
            'الخدمات' => [
                'services_kicker' => ['النص الصغير', false],
                'services_heading' => ['العنوان', false],
                'services_description' => ['النص الجانبي المتحرك', true],
            ],
            'التجارب' => [
                'demos_kicker' => ['النص الصغير', false],
                'demos_heading' => ['العنوان', false],
                'demos_description' => ['الوصف', true],
                'demo_1_title' => ['قالب تجربة ١: العنوان', false],
                'demo_1_description' => ['قالب تجربة ١: الوصف', true],
                'demo_2_title' => ['قالب تجربة ٢: العنوان', false],
                'demo_2_description' => ['قالب تجربة ٢: الوصف', true],
                'demo_3_title' => ['قالب تجربة ٣: العنوان', false],
                'demo_3_description' => ['قالب تجربة ٣: الوصف', true],
            ],
            'الأعمال والمقالات' => [
                'work_kicker' => ['نص الأعمال الصغير', false],
                'work_heading' => ['عنوان الأعمال', false],
                'journal_kicker' => ['نص المقالات الصغير', false],
                'journal_heading' => ['عنوان المقالات', false],
            ],
            'الفيديو والتواصل' => [
                'reel_kicker' => ['نص الفيديو الصغير', false],
                'reel_heading' => ['عنوان الفيديو', false],
                'reel_description' => ['وصف الفيديو', true],
                'reel_button' => ['نص زر الفيديو', false],
                'contact_kicker' => ['نص التواصل الصغير', false],
                'contact_heading' => ['عنوان التواصل', false],
                'contact_description' => ['وصف التواصل', true],
                'contact_button' => ['نص زر التواصل', false],
                'footer_tagline' => ['عبارة التذييل', false],
            ],
        ];

        $sections = [];
        foreach ($groups as $group => $fields) {
            $components = [];
            foreach ($fields as $key => [$label, $long]) {
                foreach (['ar' => 'عربي', 'en' => 'English'] as $locale => $language) {
                    $name = $key.'_'.$locale;
                    $components[] = ($long ? Textarea::make($name) : TextInput::make($name))
                        ->label($label.' — '.$language)
                        ->maxLength($long ? 300 : 120)
                        ->helperText('اكتب النص الذي سيظهر، أو امسحه للرجوع إلى الافتراضي.');
                }
            }
            if ($group === 'القائمة المفتوحة') {
                for ($i = 1; $i <= 4; $i++) {
                    $components[] = Select::make('menu_'.$i.'_target')->label('بطاقة القائمة '.$i.': الوجهة')
                        ->options([
                            '#services' => 'قسم الخدمات', '#pricing' => 'صفحة الأسعار',
                            '#demos' => 'قسم التجارب', '#work' => 'قسم الأعمال',
                            '#journal' => 'قسم المقالات', '#reel' => 'قسم الفيديو',
                            '#contact' => 'قسم التواصل',
                        ])->placeholder('الوجهة الحالية');
                }
            }
            $section = Section::make($group)->schema($components)->columns(2)->collapsible();
            if ($group === 'التجارب') {
                $section->description('نصوص قوالب التجارب الافتراضية فقط؛ بيانات المشاريع الحقيقية تُدار من قسم المشاريع.');
            }
            $sections[] = $section;
        }

        return $schema->columns(1)->components([
            Section::make('الشعار والهوية')->schema([
                FileUpload::make('logo_path')->label('شعار الشريط العلوي والفوتر')->image()->disk('public')->directory('branding')->maxSize(4096)
                    ->helperText('يفضل PNG أو WebP بخلفية شفافة. تركه فارغًا يُبقي الشعار الحالي.'),
                FileUpload::make('favicon_path')->label('أيقونة التبويب')->image()->disk('public')->directory('branding')->maxSize(1024)
                    ->helperText('صورة مربعة واضحة بأبعاد صغيرة. تركها فارغة يُبقي الأيقونة الحالية.'),
                FileUpload::make('orbit_logo_path')->label('شعار قسم الحركة')->image()->acceptedFileTypes(['image/png', 'image/webp', 'image/avif'])->disk('public')->directory('branding')->maxSize(4096)
                    ->helperText('شعار شفاف في منتصف قسم الحركة. تركه فارغًا يُبقي التصميم الحالي.'),
                FileUpload::make('contact_background_path')->label('خلفية قسم التواصل')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->disk('public')->directory('homepage')->maxSize(8192)
                    ->helperText('خلفية عريضة؛ يظل التدرج الداكن فوقها لضمان وضوح النص. تركها فارغة يُبقي الحالية.'),
            ])->columns(2),
            Section::make('محتوى الصفحة الرئيسية')
                ->description('هذه النصوص محفوظة في قاعدة البيانات وتظهر على الرئيسية. عدّل العربية أو الإنجليزية واحفظ التغييرات. الخدمات والأسعار والمقالات والمشاريع والفيديو لها قوائم مستقلة.')
                ->schema($sections)
                ->statePath('content'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->label('السجل'),
            TextColumn::make('updated_at')->label('آخر تعديل')->dateTime(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomepageContents::route('/'),
            'edit' => EditHomepageContent::route('/{record}/edit'),
        ];
    }
}
