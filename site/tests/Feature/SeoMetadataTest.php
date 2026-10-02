<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_www_requests_redirect_permanently_to_apex_with_path_and_query(): void
    {
        $this->get('https://www.keenguild.com/en/articles?page=2')
            ->assertStatus(301)
            ->assertRedirect('https://keenguild.com/en/articles?page=2');
    }

    public function test_catalog_pages_have_distinct_descriptions_and_social_metadata(): void
    {
        $work = $this->get('/en/work')->assertOk();
        $articles = $this->get('/en/articles')->assertOk();

        $work->assertSee('Explore KeenGuild projects and work', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('name="twitter:card"', false);
        $articles->assertSee('Read KeenGuild articles about website planning', false)
            ->assertSee('property="og:description"', false);
    }

    public function test_home_has_social_metadata_in_both_languages(): void
    {
        $this->get('/')->assertOk()->assertSee('property="og:title"', false)
            ->assertSee('name="twitter:card"', false);
        $this->get('/en')->assertOk()->assertSee('property="og:title"', false)
            ->assertSee('name="twitter:card"', false);
    }
}
