<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonException;
use Throwable;

class TransferSiteContent extends Command
{
    protected $signature = 'keenguild:content-transfer {direction : export or import} {path : Private JSON file path} {--replace-migration-defaults : Only for a newly migrated database; replace generated navigation, homepage and pricing defaults}';

    protected $description = 'Transfer public site content between databases without users, sessions, cache or customer inquiries';

    private const TABLES = [
        'services', 'packages', 'price_options', 'pricing_settings',
        'article_categories', 'articles', 'projects', 'faqs',
        'contact_channels', 'legal_pages', 'homepage_contents',
        'homepage_heroes', 'homepage_videos', 'navigation_items',
        'page_contents', 'seo_settings', 'inquiry_settings',
        'custom_pages', 'catalog_approvals',
    ];

    private const MIGRATION_DEFAULT_TABLES = ['pricing_settings', 'homepage_contents', 'navigation_items'];

    public function handle(): int
    {
        $direction = $this->argument('direction');
        $path = $this->argument('path');
        if (! in_array($direction, ['export', 'import'], true)) {
            $this->error('Direction must be export or import.');

            return self::FAILURE;
        }

        $directory = realpath(dirname($path));
        $public = realpath(public_path());
        if (! $directory || ! is_dir($directory) || ($public && ($directory === $public || str_starts_with($directory, $public.DIRECTORY_SEPARATOR)))) {
            $this->error('The JSON file must be in an existing private directory outside the public web root.');

            return self::FAILURE;
        }

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Missing table {$table}; run migrations first.");

                return self::FAILURE;
            }
        }

        return $direction === 'export' ? $this->export($path) : $this->import($path);
    }

    private function export(string $path): int
    {
        if (file_exists($path)) {
            $this->error('Export file already exists; refusing to overwrite it.');

            return self::FAILURE;
        }

        $tables = [];
        foreach (self::TABLES as $table) {
            $columns = Schema::getColumnListing($table);
            $order = in_array('id', $columns, true) ? 'id' : 'key';
            $tables[$table] = DB::table($table)->orderBy($order)->get()
                ->map(fn (object $row): array => (array) $row)->all();
        }

        try {
            $canonical = json_encode($tables, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $json = json_encode([
                'version' => 1,
                'exported_at' => now()->toIso8601String(),
                'sha256' => hash('sha256', $canonical),
                'tables' => $tables,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (JsonException $e) {
            $this->error('Could not encode content: '.$e->getMessage());

            return self::FAILURE;
        }

        if (file_put_contents($path, $json, LOCK_EX) === false) {
            $this->error('Could not write export file.');

            return self::FAILURE;
        }

        @chmod($path, 0600);
        foreach ($tables as $table => $rows) {
            $this->line("{$table}: ".count($rows));
        }
        $this->info('Private content export created. Users, inquiries and operational tables were excluded.');

        return self::SUCCESS;
    }

    private function import(string $path): int
    {
        if (! is_file($path) || ! is_readable($path)) {
            $this->error('Import file is missing or unreadable.');

            return self::FAILURE;
        }

        try {
            $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($payload) || ($payload['version'] ?? null) !== 1 || ! is_array($payload['tables'] ?? null)) {
                throw new JsonException('Unsupported content export format.');
            }
            $canonical = json_encode($payload['tables'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            if (! is_string($payload['sha256'] ?? null) || ! hash_equals($payload['sha256'], hash('sha256', $canonical))) {
                throw new JsonException('Content export checksum does not match.');
            }
        } catch (JsonException $e) {
            $this->error('Invalid content export: '.$e->getMessage());

            return self::FAILURE;
        }

        if (array_keys($payload['tables']) !== self::TABLES) {
            $this->error('The export has an unexpected table set or order.');

            return self::FAILURE;
        }

        $replaceDefaults = (bool) $this->option('replace-migration-defaults');
        if ($replaceDefaults && ((Schema::hasTable('users') && DB::table('users')->exists())
            || (Schema::hasTable('quote_requests') && DB::table('quote_requests')->exists()))) {
            $this->error('Replacement is only allowed before creating users or collecting inquiries.');

            return self::FAILURE;
        }

        foreach (self::TABLES as $table) {
            $rows = $payload['tables'][$table];
            if (! is_array($rows) || ! array_is_list($rows)) {
                $this->error("Invalid rows for {$table}.");

                return self::FAILURE;
            }
            if (DB::table($table)->exists() && ! ($replaceDefaults && in_array($table, self::MIGRATION_DEFAULT_TABLES, true))) {
                $this->error("Target table {$table} is not empty; refusing to overwrite existing data.");

                return self::FAILURE;
            }
            $allowedColumns = Schema::getColumnListing($table);
            foreach ($rows as $row) {
                if (! is_array($row) || array_diff(array_keys($row), $allowedColumns)) {
                    $this->error("Unexpected columns in {$table}; source and target migrations must match.");

                    return self::FAILURE;
                }
            }
        }

        try {
            DB::transaction(function () use ($payload, $replaceDefaults): void {
                if ($replaceDefaults) {
                    foreach (self::MIGRATION_DEFAULT_TABLES as $table) {
                        DB::table($table)->delete();
                    }
                }
                foreach (self::TABLES as $table) {
                    foreach (array_chunk($payload['tables'][$table], 100) as $chunk) {
                        if ($table === 'projects') {
                            $chunk = array_map(function (array $row): array {
                                $row['source_url_valid'] = ($row['project_type'] ?? null) === 'external'
                                    && Project::isPublicDemoUrl($row['source_url'] ?? null);

                                return $row;
                            }, $chunk);
                        }
                        DB::table($table)->insert($chunk);
                    }
                }
            });
        } catch (Throwable $e) {
            $this->error('Import rolled back: '.$e->getMessage());

            return self::FAILURE;
        }

        foreach (self::TABLES as $table) {
            $this->line("{$table}: ".DB::table($table)->count());
        }
        $this->info('Content import completed. Create a new administrator on this server.');

        return self::SUCCESS;
    }
}
