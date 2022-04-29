<?php

namespace App\Http\Middleware;

use App\Locking;
use App\User;
use Closure;

class checkLock
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        User::getParentsUsers($user, $users);
        $checkLocking = Locking::whereIn('user_id', collect($users)->pluck('id')->toArray())->whereType('User')->get();
        if ($checkLocking->count() > 0){
            return response(['message' => \Lang::get('api.user_locked'),'status_code' => 401],401);
        }else{
            return $next($request);
        }
    }
}
