<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (empty($user)) {
            return redirect()->route('login');
        }

        if (empty($user->menu_permission)) {
            return $next($request);
        } else {
            $user_permitted_routes = json_decode($user->menu_permission, true);
            
            // $current_route  = app()->router->getCurrentRoute();
            $current_route = Route::currentRouteName();

            // If permissions are stored as literal "null", treat as no-permission list.
            if (!is_array($user_permitted_routes) || $user->menu_permission === 'null') {
                $user_permitted_routes = [];
            }

            if (in_array($current_route, $user_permitted_routes, true)) {
                return $next($request);
            } else {
                // Allow internal AJAX search endpoints if user can access any fee pages.
                // Otherwise Select2 will receive an HTML redirect and show empty results.
                if (in_array($current_route, ['admin.fees.search.students', 'admin.fees.search.guardians'], true)) {
                    $fee_parent_routes = [
                        'admin.fees.concessions',
                        'admin.fees.sibling_discounts',
                        'admin.fees.generator',
                        'admin.fees.class_fees',
                    ];

                    foreach ($fee_parent_routes as $r) {
                        if (in_array($r, $user_permitted_routes, true)) {
                            return $next($request);
                        }
                    }
                }

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }

                return redirect()->back();
            }
        }
    }
}
