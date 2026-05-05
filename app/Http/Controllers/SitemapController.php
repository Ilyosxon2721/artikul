<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $base = rtrim(config('app.url') ?: url('/'), '/');

        foreach (['/', '/tasks', '/sellers', '/about', '/how-it-works', '/terms', '/privacy'] as $path) {
            $urls[] = ['loc' => $base.$path, 'lastmod' => now()->toAtomString(), 'priority' => '0.8'];
        }

        Task::query()
            ->where('status', TaskStatus::Published)
            ->latest('published_at')
            ->limit(5000)
            ->get(['slug', 'updated_at'])
            ->each(function (Task $task) use (&$urls, $base): void {
                $urls[] = ['loc' => $base.'/tasks/'.$task->slug, 'lastmod' => $task->updated_at?->toAtomString(), 'priority' => '0.7'];
            });

        User::query()
            ->whereNotNull('username')
            ->where('is_banned', false)
            ->whereHas('sellerProfile', fn ($q) => $q->where('is_searchable', true))
            ->limit(5000)
            ->get(['username', 'updated_at'])
            ->each(function (User $user) use (&$urls, $base): void {
                $urls[] = ['loc' => $base.'/sellers/'.$user->username, 'lastmod' => $user->updated_at?->toAtomString(), 'priority' => '0.6'];
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
