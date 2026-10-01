<?php

namespace Tests\Feature;

use App\Filament\Resources\Projects\Pages\EditProject;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\ExternalProjectExamplesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExternalProjectExamplesTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_examples_are_managed_projects_and_the_duplicate_section_is_gone(): void
    {
        $this->seed(ExternalProjectExamplesSeeder::class);

        $this->assertSame(6, Project::query()->where('project_type', 'external')->count());
        foreach (Project::query()->where('project_type', 'external')->get() as $project) {
            $this->assertTrue($project->isPubliclyVisible());
            $this->assertFileExists(public_path($project->illustration_path));
        }

        $this->get('/en/work')->assertOk()
            ->assertSee('E-commerce store')
            ->assertSee('Third-party example — not our work')
            ->assertSee('https://github.com/vercel/commerce', false)
            ->assertSee(asset('assets/demos/store.png'), false)
            ->assertDontSee('id="external-demos"', false);
        $this->get('/en/work/external-store')->assertOk()
            ->assertSee('Example by:')
            ->assertSee('Vercel')
            ->assertSee('Original source');
        $home = $this->get('/en')->assertOk()
            ->assertSee('Third-party example')
            ->assertDontSee('id="external-demos"', false)->getContent();
        preg_match('~<section id="demos".*?</section>~s', $home, $demos);
        preg_match('~<section id="work".*?</section>~s', $home, $work);
        $this->assertStringContainsString('external-store', $demos[0] ?? '');
        $this->assertStringContainsString('external-restaurant', $demos[0] ?? '');
        $this->assertStringNotContainsString('external-store', $work[0] ?? '');
        $this->assertStringNotContainsString('external-restaurant', $work[0] ?? '');
        $this->assertStringContainsString('external-real-estate', $work[0] ?? '');
    }

    public function test_import_does_not_overwrite_admin_edits_and_admin_can_manage_an_example(): void
    {
        $this->seed(ExternalProjectExamplesSeeder::class);
        $project = Project::query()->where('slug', 'external-store')->firstOrFail();
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)->test(EditProject::class, ['record' => $project->id])
            ->fillForm(['title_en' => 'Edited store example', 'sort_order' => 20])
            ->call('save')->assertHasNoFormErrors();

        $this->seed(ExternalProjectExamplesSeeder::class);
        $this->assertSame(6, Project::query()->where('project_type', 'external')->count());
        $this->assertSame('Edited store example', $project->fresh()->title_en);
        $this->get('/en/work')->assertSee('Edited store example');

        $project->update(['published' => false]);
        $this->get('/en/work')->assertDontSee('Edited store example');
    }
}
