<?php

declare(strict_types=1);

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

arch('models live in App\\Models')
    ->expect('App\\Models')
    ->toExtend(Model::class)
    ->ignoring(['App\\Models\\User', 'App\\Models\\Auth']);

arch('the User model extends Authenticatable')
    ->expect('App\\Models\\User')
    ->toExtend(Authenticatable::class);

arch('controllers do not query the DB facade directly')
    ->expect('App\\Http\\Controllers')
    ->not->toUse(DB::class)
    ->ignoring([
        'App\\Http\\Controllers\\HealthController',
        'App\\Http\\Controllers\\SitemapController',
        'App\\Http\\Controllers\\Auth\\GoogleController',
    ]);

arch('services live in App\\Services')
    ->expect('App\\Services')
    ->toBeClasses();

arch('enums are PHP backed enums')
    ->expect('App\\Enums')
    ->toBeEnums()
    ->toHaveSuffix('Status')
    ->ignoring([
        'App\\Enums\\BudgetType',
        'App\\Enums\\Currency',
        'App\\Enums\\MarketplaceCode',
        'App\\Enums\\ReviewRole',
        'App\\Enums\\SellerLevel',
        'App\\Enums\\TaskType',
        'App\\Enums\\UserMode',
        'App\\Enums\\UserRole',
    ]);

arch('strict types declared everywhere in app/')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debugging helpers leaked into app/')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();

arch('notifications must use Queueable')
    ->expect('App\\Notifications')
    ->classes()
    ->toUseTrait(Queueable::class)
    ->ignoring(['App\\Notifications\\Channels', 'App\\Notifications\\PreferenceAware']);

arch('policies live in App\\Policies')
    ->expect('App\\Policies')
    ->toHaveSuffix('Policy');

arch('broadcast events expose broadcastOn()')
    ->expect('App\\Events\\MessageSent')
    ->toImplement(ShouldBroadcast::class);
