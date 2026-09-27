<?php

namespace App\Services;

use DOMDocument;
use DOMNode;
use DOMXPath;

class HomepageCopyDefaults
{
    public function all(): array
    {
        $path = base_path('../dist/index.html');
        if (! is_file($path)) {
            return [];
        }

        $source = file_get_contents($path);
        if ($source === false) {
            return [];
        }

        $copies = [];
        foreach (['ar' => $source, 'en' => app(HomeLocalization::class)->english($source, $source, '', '')] as $locale => $html) {
            $document = new DOMDocument;
            $previous = libxml_use_internal_errors(true);
            try {
                $document->loadHTML('<?xml encoding="UTF-8" ?>'.$html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previous);
            }
            $xpath = new DOMXPath($document);
            foreach ($this->selectors($locale) as $key => $selector) {
                $nodes = $xpath->query($selector);
                $value = $nodes instanceof \DOMNodeList ? $nodes->item(0) : null;
                if ($value instanceof DOMNode) {
                    $raw = $value instanceof \DOMElement
                        ? preg_replace('~<br\s*/?>~i', ' ', $document->saveHTML($value) ?: '')
                        : $value->textContent;
                    $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
                    if ($text !== '') {
                        $copies[$key.'_'.$locale] = $text;
                    }
                }
            }
        }

        // The source button is a visual preview. The managed action labels the
        // real quote link only when intake and privacy requirements are met.
        $copies['contact_button_ar'] = $copies['nav_cta_ar'] ?? 'ابدأ مشروعك ↗';
        $copies['contact_button_en'] = $copies['nav_cta_en'] ?? 'Start a project ↗';
        $copies['reel_kicker_ar'] = '06 / شاهد العمل يتحرك';
        $copies['reel_heading_ar'] = 'شاهد الأفكار تصبح واقعًا.';

        return $copies;
    }

    private function selectors(string $locale): array
    {
        $intro = fn (string $section): string => '//section[@id="'.$section.'"]//div[contains(@class,"justify-between")][1]';
        $fields = [
            'nav_studio' => '//*[@id="siteHeader"]//div[contains(@class,"hidden lg:flex")]/span[1]',
            'nav_availability' => '//*[@id="siteHeader"]//div[contains(@class,"hidden lg:flex")]/span[3]',
            'nav_cta' => '//*[@id="navCta"]',
            'orbit_kicker' => '//section[@id="orbit"]//span[contains(@class,"orbit-kicker")]/span[contains(@class,"'.($locale === 'ar' ? 'arabic-copy' : 'english-copy').'")]',
            'menu_heading' => '//*[@id="menuPanel"]//h2[1]',
            'menu_description' => '//*[@id="menuPanel"]//div[contains(@class,"pt-5")]/p[1]',
            'menu_cta' => '//*[@id="menuPanel"]//div[contains(@class,"pt-5")]/a[1]',
            'proof_kicker' => $intro('proof').'//span[1]',
            'proof_heading' => $intro('proof').'//h2[1]',
            'proof_description' => $intro('proof').'/p[1]',
            'services_kicker' => $intro('services').'//span[1]',
            'services_heading' => $intro('services').'//h2[1]',
            'services_description' => '//*[@id="servicesAiCopy"]/@data-'.$locale,
            'demos_kicker' => $intro('demos').'//span[1]',
            'demos_heading' => $intro('demos').'//h2[1]',
            'demos_description' => $intro('demos').'/p[1]',
            'work_kicker' => '//section[@id="work"]//div[contains(@class,"mb-14")][1]//span[1]',
            'work_heading' => '//section[@id="work"]//div[contains(@class,"mb-14")][1]//h2[1]',
            'journal_kicker' => $intro('journal').'//span[1]',
            'journal_heading' => $intro('journal').'//h2[1]',
            'reel_kicker' => '//section[@id="reel"]//span[contains(@class,"reel-kicker")][1]',
            'reel_heading' => '//section[@id="reel"]//h2[1]',
            'reel_description' => '//section[@id="reel"]//div[contains(@class,"video-copy")]/p[1]',
            'reel_button' => '//section[@id="reel"]//button[contains(@class,"video-play")]/span[2]',
            'contact_kicker' => $intro('contact').'//span[1]',
            'contact_heading' => $intro('contact').'//h2[1]',
            'contact_description' => $intro('contact').'//p[1]',
            'contact_button' => $intro('contact').'//button[1]',
            'footer_tagline' => '//footer//p[1]',
        ];

        foreach (range(1, 4) as $i) {
            $card = '(//*[@id="menuPanel"]//a[contains(@class,"menu-card")])['.$i.']';
            $fields['menu_'.$i.'_title'] = $card.'//h3[1]';
            $fields['menu_'.$i.'_description'] = $card.'//p[1]';
            $proof = '(//section[@id="proof"]//article)['.$i.']';
            $fields['proof_'.$i.'_title'] = $proof.'//h3[1]';
            $fields['proof_'.$i.'_description'] = $proof.'//p[1]';
        }
        foreach (range(1, 3) as $i) {
            $demo = '(//section[@id="demos"]//article)['.$i.']';
            $fields['demo_'.$i.'_title'] = $demo.'//h3[1]';
            $fields['demo_'.$i.'_description'] = $demo.'//p[1]';
        }

        return $fields;
    }
}
