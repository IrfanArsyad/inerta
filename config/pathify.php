<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Route Filtering - Only
    |--------------------------------------------------------------------------
    |
    | Whitelist of route name patterns to include. Supports wildcards.
    | Leave empty to include all routes (except those in 'except').
    |
    */
    'only' => [],

    /*
    |--------------------------------------------------------------------------
    | Route Filtering - Except
    |--------------------------------------------------------------------------
    |
    | Blacklist of route name patterns to exclude. Supports wildcards.
    |
    */
    'except' => [
        '_debugbar.*',
        'horizon.*',
        'telescope.*',
        'sanctum.*',
        'ignition.*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Groups
    |--------------------------------------------------------------------------
    |
    | Define route groups for selective loading in Blade/Inertia.
    |
    */
    'groups' => [],

    /*
    |--------------------------------------------------------------------------
    | Include Middleware Information
    |--------------------------------------------------------------------------
    |
    | When enabled, route middleware will be included in the output.
    | Useful for permission checking on the frontend.
    |
    */
    'include_middleware' => true,

    /*
    |--------------------------------------------------------------------------
    | Include Permissions
    |--------------------------------------------------------------------------
    |
    | Extract permissions from middleware (e.g., 'can:view-posts').
    | Enables route.can() method in JavaScript.
    |
    */
    'include_permissions' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Define navigation menus that can be consumed in JavaScript.
    | Items are automatically filtered based on user permissions.
    |
    | Example:
    | 'navigation' => [
    |     'main' => [
    |         ['route' => 'home', 'label' => 'Home', 'icon' => 'home'],
    |         ['route' => 'dashboard', 'label' => 'Dashboard', 'auth' => true],
    |         [
    |             'label' => 'Admin',
    |             'permission' => 'access-admin',
    |             'children' => [
    |                 ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
    |                 ['route' => 'admin.users', 'label' => 'Users'],
    |             ],
    |         ],
    |     ],
    | ],
    |
    */
    'navigation' => [],

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs
    |--------------------------------------------------------------------------
    |
    | Define breadcrumb mappings for routes.
    | If not defined, breadcrumbs will be auto-generated from route hierarchy.
    |
    | Example:
    | 'breadcrumbs' => [
    |     'posts.show' => [
    |         ['route' => 'home', 'label' => 'Home'],
    |         ['route' => 'posts.index', 'label' => 'Posts'],
    |         ['label' => ':title'], // Dynamic from route params
    |     ],
    | ],
    |
    */
    'breadcrumbs' => [],

    /*
    |--------------------------------------------------------------------------
    | Prefetch Configuration
    |--------------------------------------------------------------------------
    |
    | Configure intelligent route prefetching for faster navigation.
    |
    */
    'prefetch' => [
        'enabled' => false,
        'strategy' => 'hover', // 'hover', 'viewport', 'idle', 'none'
        'delay' => 100,        // Delay in ms before prefetching
        'routes' => ['*.index', '*.show'], // Routes to prefetch
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Multi-language URL support.
    |
    */
    'localization' => [
        'enabled' => false,
        'locales' => ['en'],
        'default' => 'en',
        'hide_default' => true, // Hide default locale from URL
    ],

    /*
    |--------------------------------------------------------------------------
    | Inertia Integration
    |--------------------------------------------------------------------------
    |
    | Settings for Inertia.js integration.
    |
    */
    'inertia' => [
        'enabled' => true,
        'share_as' => 'pathify', // Key name in Inertia shared data
    ],

    /*
    |--------------------------------------------------------------------------
    | Output Path
    |--------------------------------------------------------------------------
    */
    'output' => [
        'path' => resource_path('js'),
        'filename' => 'pathify.js',
        'types_filename' => 'pathify.d.ts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Cache routes for better performance in production.
    |
    */
    'cache' => [
        'enabled' => env('PATHIFY_CACHE', false),
        'ttl' => 3600,
        'key' => 'pathify_routes',
    ],
];
