<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CustomPage;
use App\Models\Faq;
use App\Models\LegalPage;
use App\Models\Package;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicContentSearchController
{
    public function __invoke(Request $request): JsonResponse
    {
        $input = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:200'],
            'locale' => ['sometimes', 'in:ar,en'],
        ]);

        $locale = $input['locale'] ?? 'ar';
        $terms = array_slice(array_unique(preg_split('/[^\p{L}\p{N}]+/u', trim($input['q']), -1, PREG_SPLIT_NO_EMPTY) ?: []), 0, 8);
        $terms = array_values(array_filter($terms, fn (string $term): bool => mb_strlen($term) >= 2));
        if ($terms === []) {
            return response()->json(['items' => []]);
        }

        $items = [];
        $groupWords = [
            'article' => ['مقال', 'مدون', 'article', 'blog'],
            'project' => ['مشروع', 'مشاريع', 'مشروعات', 'أعمال', 'project', 'portfolio'],
            'service' => ['خدم', 'service'],
            'package' => ['سعر', 'أسعار', 'اسعار', 'تكلف', 'باق', 'price', 'pricing', 'package'],
            'page' => ['صفحة', 'صفحات', 'page'],
            'legal' => ['خصوص', 'شروط', 'privacy', 'terms'],
            'faq' => ['سؤال', 'أسئلة', 'اسئلة', 'faq'],
        ];
        $sources = [
            ['article', Article::query()->publiclyVisible(), ['title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en'], 'title', 'summary', 'body', 'article'],
            ['project', Project::query()->publiclyVisible(), ['title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en'], 'title', 'summary', 'body', 'project'],
            ['service', Service::query()->where('published', true), ['title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en'], 'title', 'summary', 'body', 'service'],
            ['package', Package::query()->where('published', true)->whereHas('service', fn (Builder $query) => $query->where('published', true)), ['name_ar', 'name_en', 'description_ar', 'description_en'], 'name', 'description', null, 'pricing'],
            ['page', CustomPage::query()->publiclyVisible(), ['title_ar', 'title_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en'], 'title', 'summary', 'body', 'custom-page'],
            ['legal', LegalPage::query()->publiclyVisible(), ['title_ar', 'title_en', 'body_ar', 'body_en'], 'title', null, 'body', 'legal'],
            ['faq', Faq::query()->where('published', true), ['question_ar', 'question_en', 'answer_ar', 'answer_en'], 'question', null, 'answer', 'faqs'],
        ];

        foreach ($sources as [$type, $query, $fields, $titleField, $summaryField, $bodyField, $route]) {
            $groupMatch = collect($groupWords[$type])->contains(fn (string $word): bool => mb_stripos($input['q'], $word) !== false);
            $searchTerms = $groupMatch
                ? array_values(array_filter($terms, fn (string $term): bool => ! collect($groupWords[$type])->contains(
                    fn (string $word): bool => mb_stripos($term, $word) !== false,
                )))
                : $terms;

            if ($searchTerms !== []) {
                $query->where(function (Builder $query) use ($fields, $searchTerms): void {
                    foreach ($searchTerms as $term) {
                        $pattern = '%'.addcslashes($term, '%_\\').'%';
                        foreach ($fields as $field) {
                            $query->orWhere($field, 'like', $pattern);
                        }
                    }
                });

                $scoreSql = [];
                $bindings = [];
                foreach ($searchTerms as $term) {
                    $pattern = '%'.addcslashes($term, '%_\\').'%';
                    $titleColumns = [$titleField.'_ar', $titleField.'_en'];
                    $otherColumns = array_values(array_diff($fields, $titleColumns));
                    $scoreSql[] = '(CASE WHEN '.implode(' OR ', array_map(fn (string $field): string => "$field LIKE ?", $titleColumns)).' THEN 5'
                        .($otherColumns !== [] ? ' WHEN '.implode(' OR ', array_map(fn (string $field): string => "$field LIKE ?", $otherColumns)).' THEN 1' : '')
                        .' ELSE 0 END)';
                    array_push($bindings, ...array_fill(0, count($titleColumns) + count($otherColumns), $pattern));
                }
                $query->orderByRaw(implode(' + ', $scoreSql).' DESC', $bindings);
            }
            $query->orderByDesc('id');

            foreach ($query->limit(60)->get() as $record) {
                $title = (string) ($record->{$titleField.'_'.$locale} ?: $record->{$titleField.'_ar'});
                $summary = $summaryField ? (string) ($record->{$summaryField.'_'.$locale} ?: '') : '';
                $body = $bodyField ? (string) ($record->{$bodyField.'_'.$locale} ?: '') : '';
                $copy = trim(preg_replace('/\s+/u', ' ', strip_tags($summary.' '.$body)) ?? '');
                if ($type === 'package') {
                    $features = $locale === 'en' ? $record->included_features_en : $record->included_features;
                    $copy .= ' '.($locale === 'en' ? 'Starting price' : 'السعر يبدأ من').' '.$record->base_price_egp.' EGP. '.implode('، ', is_array($features) ? $features : []);
                }
                $haystack = mb_strtolower($title.' '.$copy);
                $score = $groupMatch ? 2 : 0;
                foreach ($searchTerms as $term) {
                    $needle = mb_strtolower($term);
                    if (mb_stripos($title, $needle) !== false) {
                        $score += 5;
                    } elseif (mb_stripos($haystack, $needle) !== false) {
                        $score += 1;
                    }
                }
                $parameters = ['locale' => $locale];
                if ($route === 'legal') {
                    $parameters['type'] = $record->type;
                } elseif (in_array($route, ['article', 'project', 'service', 'custom-page'], true)) {
                    $parameters['slug'] = $record->slug;
                }
                $items[] = [
                    'type' => $type,
                    'title' => $title,
                    'excerpt' => Str::limit($copy, 700),
                    'url' => route($route, $parameters),
                    'score' => $score,
                ];
            }
        }

        usort($items, fn (array $a, array $b): int => $b['score'] <=> $a['score']);

        return response()->json([
            'counts' => [
                'articles' => Article::query()->publiclyVisible()->count(),
                'projects' => Project::query()->publiclyVisible()->count(),
            ],
            'items' => array_map(function (array $item): array {
                unset($item['score']);

                return $item;
            }, array_slice($items, 0, 6)),
        ])->header('Cache-Control', 'public, max-age=60');
    }
}
