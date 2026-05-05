<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Specialization;
use Illuminate\Console\Command;
use RuntimeException;

class ImportReferenceDataCommand extends Command
{
    protected $signature = 'artikul:reference:import {kind : marketplaces|categories|specializations} {file : Path to CSV} {--update : Update existing rows by unique key}';

    protected $description = 'Import marketplaces / categories / specializations from CSV.';

    public function handle(): int
    {
        $kind = (string) $this->argument('kind');
        $file = (string) $this->argument('file');

        if (! is_file($file)) {
            $this->error("File not found: {$file}");

            return Command::FAILURE;
        }

        $rows = $this->readCsv($file);
        if ($rows === []) {
            $this->warn('No rows in the CSV.');

            return Command::SUCCESS;
        }

        try {
            $count = match ($kind) {
                'marketplaces' => $this->importMarketplaces($rows),
                'categories' => $this->importCategories($rows),
                'specializations' => $this->importSpecializations($rows),
                default => throw new RuntimeException('Unknown kind: '.$kind),
            };

            $this->info("Imported / updated {$count} rows.");

            return Command::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function readCsv(string $file): array
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return [];
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);

            return [];
        }

        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($header, $line);
        }
        fclose($handle);

        return $rows;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function importMarketplaces(array $rows): int
    {
        $count = 0;
        foreach ($rows as $row) {
            $values = [
                'name_ru' => $row['name_ru'] ?? '',
                'name_uz' => $row['name_uz'] ?? ($row['name_ru'] ?? ''),
                'name_en' => $row['name_en'] ?? ($row['name_ru'] ?? ''),
                'country_code' => $row['country_code'] ?? null,
                'color' => $row['color'] ?? null,
                'is_active' => (bool) ($row['is_active'] ?? true),
                'sort_order' => (int) ($row['sort_order'] ?? 0),
            ];

            Marketplace::query()->updateOrCreate(['code' => $row['code']], $values);
            $count++;
        }

        return $count;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function importCategories(array $rows): int
    {
        $count = 0;
        foreach ($rows as $row) {
            $values = [
                'parent_id' => $row['parent_id'] !== '' ? (int) $row['parent_id'] : null,
                'name_ru' => $row['name_ru'] ?? '',
                'name_uz' => $row['name_uz'] ?? ($row['name_ru'] ?? ''),
                'name_en' => $row['name_en'] ?? ($row['name_ru'] ?? ''),
                'icon' => $row['icon'] ?? null,
                'is_active' => (bool) ($row['is_active'] ?? true),
                'sort_order' => (int) ($row['sort_order'] ?? 0),
            ];

            Category::query()->updateOrCreate(['slug' => $row['slug']], $values);
            $count++;
        }

        return $count;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    private function importSpecializations(array $rows): int
    {
        $count = 0;
        foreach ($rows as $row) {
            $values = [
                'category_id' => $row['category_id'] !== '' ? (int) $row['category_id'] : null,
                'name_ru' => $row['name_ru'] ?? '',
                'name_uz' => $row['name_uz'] ?? ($row['name_ru'] ?? ''),
                'name_en' => $row['name_en'] ?? ($row['name_ru'] ?? ''),
                'icon' => $row['icon'] ?? null,
                'is_active' => (bool) ($row['is_active'] ?? true),
                'sort_order' => (int) ($row['sort_order'] ?? 0),
            ];

            Specialization::query()->updateOrCreate(['slug' => $row['slug']], $values);
            $count++;
        }

        return $count;
    }
}
