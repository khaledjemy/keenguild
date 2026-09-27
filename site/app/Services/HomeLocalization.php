<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class HomeLocalization
{
    public function english(string $html, string $source, string $title, string $description): string
    {
        // libxml rewrites markup-like strings in scripts and turns Unicode
        // characters in CSS content declarations into invalid HTML entities.
        // Keep both raw blocks intact while translating visible HTML text.
        $scripts = [];
        $html = preg_replace_callback('~<script\b[^>]*>.*?</script\s*>~is', function (array $match) use (&$scripts): string {
            $index = count($scripts);
            $scripts[] = $match[0];

            return '<script data-keenguild-preserve="'.$index.'"></script>';
        }, $html) ?? $html;
        $styles = [];
        $html = preg_replace_callback('~<style\b[^>]*>.*?</style\s*>~is', function (array $match) use (&$styles): string {
            $index = count($styles);
            $styles[] = $match[0];

            return '<style data-keenguild-preserve="'.$index.'"></style>';
        }, $html) ?? $html;

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8" ?>'.$html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $xpath = new DOMXPath($document);
        $root = $document->documentElement;
        $root->setAttribute('lang', 'en');
        $root->setAttribute('dir', 'ltr');
        $body = $document->getElementsByTagName('body')->item(0);
        if ($body instanceof DOMElement) {
            $body->setAttribute('class', trim($body->getAttribute('class').' english'));
        }

        foreach ($xpath->query('//*[@data-home-en or @data-catalog-en]') as $element) {
            if (! $element instanceof DOMElement) {
                continue;
            }
            $value = $element->getAttribute($element->hasAttribute('data-home-en') ? 'data-home-en' : 'data-catalog-en');
            if ($element->childNodes->length === 1 && $element->firstChild?->nodeType === XML_TEXT_NODE) {
                $element->firstChild->nodeValue = $value;
            }
        }

        $translations = $this->translations($source);
        $pattern = '~(?<![\pL\pN])('.implode('|', array_map(fn (string $text): string => preg_quote($text, '~'), array_keys($translations))).')(?![\pL\pN])~u';
        foreach ($xpath->query('//text()[not(ancestor::script) and not(ancestor::style)]') as $node) {
            if ($node instanceof DOMNode && $node->parentNode instanceof DOMElement
                && ! $node->parentNode->hasAttribute('data-home-en')
                && ! $node->parentNode->hasAttribute('data-catalog-en')) {
                $node->nodeValue = preg_replace_callback($pattern, fn (array $match): string => $translations[$match[1]], $node->nodeValue) ?? $node->nodeValue;
            }
        }

        foreach ($xpath->query('//a[@href]') as $anchor) {
            if (! $anchor instanceof DOMElement) {
                continue;
            }
            $href = $anchor->getAttribute('href');
            if ($href === '/' && $anchor->hasAttribute('data-catalog-en')) {
                $anchor->setAttribute('href', route('home.en', [], false));

                continue;
            }
            $anchor->setAttribute('href', preg_replace('~^/ar(?=/)~', '/en', $href) ?? $href);
        }
        foreach ($xpath->query('//meta[@name="description"]') as $meta) {
            $meta->setAttribute('content', $description);
            break;
        }
        foreach ($xpath->query('//*[@id="servicesAiCopy"]') as $paragraph) {
            if ($paragraph instanceof DOMElement && $paragraph->hasAttribute('data-en')) {
                $paragraph->textContent = $paragraph->getAttribute('data-en');
            }
        }
        foreach ($xpath->query('//title') as $element) {
            $element->textContent = $title;
            break;
        }
        foreach ($xpath->query('//link[@rel="canonical"]') as $link) {
            $link->setAttribute('href', route('home.en'));
            break;
        }
        foreach ($xpath->query('//*[@id="languageButton"]') as $button) {
            $button->textContent = 'AR';
            $button->setAttribute('aria-label', 'Switch to Arabic');
            break;
        }

        $localized = $document->saveHTML();
        $localized = preg_replace_callback('~<script data-keenguild-preserve="(\d+)"></script>~i',
            fn (array $match): string => $scripts[(int) $match[1]] ?? $match[0], $localized) ?? $localized;
        $localized = preg_replace_callback('~<style data-keenguild-preserve="(\d+)"></style>~i',
            fn (array $match): string => $styles[(int) $match[1]] ?? $match[0], $localized) ?? $localized;

        return str_replace(['<?xml encoding="UTF-8" ?>', '&mdash;'], ['', '—'], $localized);
    }

    private function translations(string $source): array
    {
        $start = strpos($source, 'const translations =');
        $end = $start === false ? false : strpos($source, 'function setLanguage(', $start);
        if ($start === false || $end === false) {
            return [];
        }

        preg_match_all("~'((?:\\\\.|[^'\\\\])*)'\\s*:\\s*'((?:\\\\.|[^'\\\\])*)'~u", substr($source, $start, $end - $start), $matches, PREG_SET_ORDER);
        $translations = [];
        foreach ($matches as $match) {
            $translations[str_replace("\\'", "'", $match[1])] = str_replace("\\'", "'", $match[2]);
        }
        $translations += [
            'كرسي Line' => 'Line chair',
            'مصباح Orb' => 'Orb lamp',
            'دقيقة واحدة تعرض كيف تتحول الفكرة إلى تجربة رقمية واضحة، جميلة، وقابلة للنمو.' => 'A short visual study of an idea becoming a clear, thoughtful digital experience.',
            'شاهد الفيديو' => 'Watch the film',
            'شاهد ما نبنيه' => 'See what we build',
            'استراتيجية، تصميم، وتطوير ينقل الفكرة إلى الواقع.' => 'Strategy, design, and development that bring ideas to life.',
            'لمنتجات ومواقع تشبه طموحك.' => 'For products and websites that match your ambition.',
            'كل ما تحتاجه في مكان واحد.' => 'Everything you need in one place.',
            'كل ما تحتاجه' => 'Everything you need',
            'في مكان واحد.' => 'in one place.',
            'من أول شرارة إلى منتج يعمل ويكبر.' => 'From the first spark to a product that works and grows.',
            'اختر المسار الذي تريد استكشافه.' => 'Choose the path you want to explore.',
            'لنتحدث عن فكرتك' => 'Let’s talk about your idea',
            'نماذج حية تساعدك ترى الفكرة قبل أن تستثمر فيها.' => 'Live concepts help you explore an idea before investing in it.',
            'جرّب الآن' => 'Try it now',
            'أفكار عملية عن المنتجات، التجربة، والنمو الذكي.' => 'Practical ideas about products, experience, and thoughtful growth.',
            'الأسعار' => 'Pricing',
            'إزاي نبدأ؟' => 'How do we start?',
            'تُرسل الرسائل إلى خدمة المساعد المستقلة ويُحفظ سجل المحادثة.' => 'Messages are sent to the separate assistant service and the conversation is saved.',
            'تصميم الواجهات' => 'Interface design',
            'جاري الاتصال بالمساعد' => 'Connecting to the assistant',
            'أهلاً بيك! احكيلي عن مشروعك أو سؤالك، وأنا أساعدك تحدد الخطوة المناسبة.' => 'Welcome! Tell me about your project or question, and I will help you find the right next step.',
            'معاينة تصميمية' => 'Design preview',
            'نموذج بصري فقط — لا يوجد ربط أو إرسال بيانات' => 'Visual concept only — no data is connected or sent',
        ];
        uksort($translations, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

        return $translations;
    }
}
