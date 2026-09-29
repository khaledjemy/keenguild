<?php

namespace Tests\Feature;

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
}
