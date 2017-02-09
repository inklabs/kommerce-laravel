<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use inklabs\kommerce\EntityDTO\UserDTO;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $session = app('session');

        if (! $session->has('user')) {
            return redirect()->route('admin.login');
        }

        /** @var UserDTO $user */
        $user = $session->get('user');
        foreach ($user->userRoles as $userRole) {
            if ($userRole->userRoleType->isAdmin) {
                return $next($request);
            }
        }

        return response('Access Denied.', 401);
    }
}
