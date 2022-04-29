<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Foundation\Testing\HttpException;

class IsAdmin{

    public function handle($request, Closure $next){
        /** @var User $user */
        $user = \Auth::user();
        if($user->is_admin == 'Yes'){
            return $next($request);
        } else {
            return response(['message' => \Lang::get('api.unauthorized_user'),'status_code' => 401],401);
        }
    }
}
