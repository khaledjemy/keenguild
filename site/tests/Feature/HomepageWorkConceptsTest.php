<?php

namespace Tests\Feature;

use App\Models\PageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageWorkConceptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_three_honestly_labeled_interactive_concepts_when_no_projects_are_published(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('ليست مشاريع عملاء منفذة')
            ->assertSee('/ar/demos/flowboard')
            ->assertSee('/ar/demos/storefront')
            ->assertSee('/ar/demos/pulse')
            ->assertSee('/ar/work')
            ->assertDontSee("openWorkPreview('academy')");

        $this->get('/en')->assertOk()
            ->assertSee('not completed client projects')
            ->assertSee('/en/demos/flowboard')
            ->assertSee('/en/demos/storefront')
            ->assertSee('/en/demos/pulse');
    }

    public function test_interactive_concept_copy_and_visibility_follow_work_page_settings(): void
    {
        PageContent::create([
            'page_key' => 'work',
            'extra_copy' => [
                'concept_flowboard_name_ar' => 'لوحتنا الجديدة',
                'concept_flowboard_name_en' => 'Our new board',
                'concept_flowboard_copy_en' => 'A custom team board preview.',
                'concept_storefront_visible' => '0',
            ],
        ]);

        $this->get('/en')->assertOk()->assertSee('Our new board')->assertDontSee('/en/demos/storefront');
        $this->get('/en/work')->assertOk()->assertSee('A custom team board preview.')
            ->assertDontSee('/en/demos/storefront');
        $this->get('/ar/demos/flowboard')->assertOk()->assertSee('لوحتنا الجديدة');
        $this->get('/en/demos/storefront')->assertNotFound();
    }

    public function test_hiding_all_concepts_does_not_leave_empty_cards_or_public_demo_routes(): void
    {
        PageContent::create([
            'page_key' => 'work',
            'extra_copy' => [
                'concept_flowboard_visible' => '0',
                'concept_storefront_visible' => '0',
                'concept_pulse_visible' => '0',
            ],
        ]);

        $this->get('/en/work')->assertOk()->assertDontSee('Try interactive concepts');
        $this->get('/en')->assertOk()->assertSee('No interactive concepts are published right now.');
        $this->get('/en/demos/flowboard')->assertNotFound();
    }
}
