<?php

namespace Impiger\ACL\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Session;

class Authenticate extends BaseAuthenticate
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $guards
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        // @customized Ramesh Esakki - Auto Login check
        Session::put('lastActive', date('U'));
        Session::forget('idleWarningDisplayed');
        Session::forget('logoutWarningDisplayed');
        /* @customized Sabari Shankar Parthiban - First login check start*/
            $user = $request->user();
            if($user && !in_array($request->path(), ['admin/system/users/change-password/'.$user->id,'admin/logout'])){
                 if(setting('enable_force_pwd_change') && setting('enable_force_pwd_change_roles')){                 
                    if(array_intersect($user->role_ids,setting('enable_force_pwd_change_roles')) && !$user->last_login){
                        return redirect('admin/force-change/'.$user->id);
                    }
                }
            }
        /* @customized Sabari Shankar Parthiban -  First login check end */
        if (!$guards) {
            $route = $request->route();
            $flag = $route->getAction('permission');
            if ($flag === null) {
                $flag = $route->getName();
            }

            $flag = preg_replace('/.create.store$/', '.create', $flag);
            $flag = preg_replace('/.edit.update$/', '.edit', $flag);

            if ($flag && !$request->user()->hasAnyPermission((array)$flag)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }
                return redirect()->route('dashboard.index');
            }
        }

        return $next($request);
    }
}
