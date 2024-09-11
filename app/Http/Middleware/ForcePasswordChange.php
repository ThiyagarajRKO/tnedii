<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if($user){
             if(setting('enable_force_pwd_change') && setting('enable_force_pwd_change_roles')){                 
                if(array_intersect($user->role_ids,setting('enable_force_pwd_change_roles')) && !$user->last_login){
                    return redirect('admin/force-change/'.$user->id);
                }
            }
        }
        return $next($request);
        
    }
}
