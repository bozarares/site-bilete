<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $userArray = null;
        $user = $request->user();

        if ($user) {
            $userArray = $user->toArray(); // convert the user object to an array
            $userArray['cart_item_count'] = $user->getCartItemCount(); // add the cart item count to the array
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userArray, // pass the array instead of the user object
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy())->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'urlPrev' => function () {
                if (
                    url()->previous() !== route('login') &&
                    url()->previous() !== route('register') &&
                    url()->previous() !== '' &&
                    url()->previous() !== url()->current()
                ) {
                    return url()->previous();
                } else {
                    return route('welcome');
                }
            },
            'message' => session('message'),
        ]);
    }
}
