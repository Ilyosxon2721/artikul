<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Specialization;
use Illuminate\Console\Command;

class ExportReferenceDataCommand extends Command
{
    protected $signature = 'artikul:reference:export {kind : marketplaces|categories|specializations} {--out= : Output file path (defaults to storage/app)}';

    protected $description = 'Export marketplaces / categories / specializations to CSV.';

    public function handle(): int
    {
        $kind = (string) $this->argument('kind');
        $out = (string) ($this->option('out') ?: storage_path("app/reference-{$kind}-".now()->format('Ymd-His').'.csv'));

        $rows = match ($kind) {
            'marketplaces' => $this->marketplaces(),
            'categories' => $this->categories(),
            'specializations' => $this->specializations(),
            default => null,
        };

        if ($rows === null) {
            $this->error("Unknown kind {$kind}.");

            return Command::FAILURE;
        }

        $handle = fopen($out, 'w');
        if ($handle === false) {
            $this->error("Cannot write to {$out}");

            return Command::FAILURE;
        }

        fputcsv($handle, array_keys($rows[0] ?? ['id' => null]));
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        $this->info('Exported '.count($rows)." rows to {$out}");

        return Command::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function marketplaces(): array
    {
        return Marketplace::query()
            ->orderBy('sort_order')
            ->get(['code', 'name_ru', 'name_uz', 'name_en', 'country_code', 'color', 'is_active', 'sort_order'])
            ->map(fn ($m) => $m->only(['code', 'name_ru', 'name_uz', 'name_en', 'country_code', 'color', 'is_active', 'sort_order']))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function categories(): array
    {
        return Category::query()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get(['parent_id', 'slug', 'name_ru', 'name_uz', 'name_en', 'icon', 'is_active', 'sort_order'])
            ->map(fn ($c) => $c->only(['parent_id', 'slug', 'name_ru', 'name_uz', 'name_en', 'icon', 'is_active', 'sort_order']))
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function specializations(): array
    {
        return Specialization::query()
            ->orderBy('sort_order')
            ->get(['category_id', 'slug', 'name_ru', 'name_uz', 'name_en', 'icon', 'is_active', 'sort_order'])
            ->map(fn ($s) => $s->only(['category_id', 'slug', 'name_ru', 'name_uz', 'name_en', 'icon', 'is_active', 'sort_order']))
            ->all();
    }
}
