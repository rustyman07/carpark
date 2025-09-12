<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
       return array_merge(parent::share($request), [
           'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            // 'success' => fn () => $request->session()->pull('success'), // Corrected line
            // 'error' => fn () => $request->session()->pull('error'),   // Corrected line
            'success' => fn () => $request->session()->get('success'), // Corrected line
            'error' => fn () => $request->session()->get('error'),   // Corrected line
        ],
            // 💡 Add this section to share Ziggy routes
         'ziggy' => function () use ($request) {
            return array_merge((new Ziggy)->toArray(), [
                'currentRouteName' => $request->route() ? $request->route()->getName() : null,
            ]);
        },
        ]);
    }
}
