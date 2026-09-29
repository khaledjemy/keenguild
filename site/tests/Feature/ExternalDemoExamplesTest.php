<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Support\ExternalDemoExamples;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalDemoExamplesTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_examples_are_attributed_and_not_pretended_to_be_client_work(): void
    {
        $this->assertCount(6, ExternalDemoExamples::all());

        foreach (ExternalDemoExamples::all() as $demo) {
            $this->assertFileExists(public_path($demo['image']));
        }

        foreach (['/', '/en', '/ar/work', '/en/work'] as $path) {
            $response = $this->get($path)->assertOk()
                ->assertSee('https://demo.vercel.store/', false)
                ->assertSee('https://demo.kitchenasty.com/', false)
                ->assertSee('https://bookatable.vercel.app/', false)
                ->assertSee('https://github.com/vercel/commerce', false);

            $home = in_array($path, ['/', '/en'], true);
            foreach ($home ? array_slice(ExternalDemoExamples::all(), 0, 3) : ExternalDemoExamples::all() as $demo) {
                $response->assertSee(asset($demo['image']), false);
            }

            if ($home) {
                $response->assertDontSee('https://demo.havenlytics.com/', false)
                    ->assertSee($path === '/en' ? 'Explore all demos' : 'شاهد كل النماذج');
            } else {
                $response->assertSee('https://demo.havenlytics.com/', false)
                    ->assertSee('https://knowsphere.billalbenz.com/', false)
                    ->assertSee('https://plausible.io/plausible.io', false);
            }

            if (str_contains($path, '/en')) {
                $response->assertSee('not projects built by KeenGuild');
            } else {
                $response->assertSee('وليست أعمالًا نفذتها KeenGuild');
            }
        }

        $this->assertSame(0, Project::count());
    }
}
