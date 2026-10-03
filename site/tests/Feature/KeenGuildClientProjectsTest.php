<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\KeenGuildClientProjectsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeenGuildClientProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_italiano988_is_presented_as_real_client_work_without_overwriting_admin_edits(): void
    {
        $this->seed(KeenGuildClientProjectsSeeder::class);

        $project = Project::query()->where('slug', 'italiano988-import-export')->firstOrFail();
        $this->assertTrue($project->isPubliclyVisible());
        $this->assertSame(asset('assets/projects/italiano988.png'), $project->coverUrl());
        $this->assertFileExists(public_path('assets/projects/italiano988.png'));

        $this->get('/')->assertOk()->assertSee('Italiano988')->assertSee('/ar/work/italiano988-import-export');
        $this->get('/en')->assertOk()->assertSee('Italiano988')->assertSee('data-home-route="project"', false);
        $this->get('/ar/work')->assertOk()->assertSee('Italiano988')->assertSee('مشروع عميل');
        $this->get('/en/work/italiano988-import-export')->assertOk()
            ->assertSee('Client project')
            ->assertSee('https://italiano988.com/', false)
            ->assertSee('React');

        $project->update(['title_en' => 'Updated by admin']);
        $this->seed(KeenGuildClientProjectsSeeder::class);
        $this->assertSame(1, Project::query()->where('slug', 'italiano988-import-export')->count());
        $this->assertSame('Updated by admin', $project->fresh()->title_en);
    }
}
