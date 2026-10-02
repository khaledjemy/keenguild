<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('source_url_valid')->default(false);
        });

        DB::table('projects')->where('project_type', 'external')->orderBy('id')->chunkById(100, function ($projects): void {
            foreach ($projects as $project) {
                if (Project::isPublicDemoUrl($project->source_url)) {
                    DB::table('projects')->where('id', $project->id)->update(['source_url_valid' => true]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('source_url_valid');
        });
    }
};
