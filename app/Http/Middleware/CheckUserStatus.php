<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Foundation\Testing\HttpException;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = \Auth::user();
        if($user->user_id == 0){
            return $next($request);
        } else {
            return response(['message' => \Lang::get('api.unauthorized_user'),'status_code' => 401],401);
        }
    }
}
