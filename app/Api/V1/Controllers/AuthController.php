<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Locking;
use App\LogReport;
use App\PhpMqtt;
use Illuminate\Http\Request;
use App\Api\V1\Resources\User\UserResource;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Created by PhpStorm.
 * User: Hardik
 * Date: 09/07/18
 * Time: 11:00 AM
 */
class AuthController extends ApiController
{


    public function login(Request $request)
    {
        $this->validateRequest('login');
        $requestPara = $request->only('user_name', 'password', 'admin');
        $user = User::whereUserName($requestPara['user_name'])->whereUserId(0)->first();
        if ($user) {
            if (Hash::check($requestPara['password'], $user->password)) {
                $token = $this->generateAccessToken($user)->accessToken;
                $this->saveLogReport($user, $request);
                /*PhpMqtt::publish('user/' . $user->id, json_encode([
                    'type' => 'logout',
                    'data' => [],
                    'user_id' => $user->id,
                    'message' => 'You are logged into another device.'
                ]));*/
                return UserResource::make($user)->additional(['meta' => [
                    'token' => $token,
                    'status_code' => 200,
                    'message' => \Lang::get('api.login_success')
                ]]);
            } else {
                return response(['data' => null, 'message' => \Lang::get('api.invalid_credential'), 'status_code' => 400], 200);
            }
        }
        return response(['data' => null, 'message' => \Lang::get('api.user_name_not_registered'), 'status_code' => 400], 200);
    }

    public function mPanelLogin(Request $request)
    {
        $this->validateRequest('login');
        $requestPara = $request->only('user_name', 'password', 'admin');
        $user = User::whereUserName($requestPara['user_name'])->where('user_id', '!=', 0)->first();
        if ($user) {
            User::getParentsUsers($user, $users);
            $checkLocking = Locking::whereIn('user_id',collect($users)->pluck('id')->toArray())->whereType('User')->get();
            if ($checkLocking->count() > 0){
                return response(['data'=> null,'message' => 'Your account has been locked by admin!', 'status_code' => 400], 200);
            }
            if (Hash::check($requestPara['password'], $user->password)) {
                $token = $this->generateAccessToken($user)->accessToken;
                $this->saveLogReport($user, $request);
                PhpMqtt::publish('user/' . $user->id, json_encode([
                    'type' => 'logout',
                    'data' => [],
                    'user_id' => $user->id,
                    'message' => 'You are logged into another device.'
                ]));
                return UserResource::make($user)->additional(['meta' => [
                    'token' => $token,
                    'status_code' => 200,
                    'message' => \Lang::get('api.login_success')
                ]]);
            } else {
                return response(['data'=>null,'message' => \Lang::get('api.invalid_credential'), 'status_code' => 400], 200);
            }
        }
        return response(['data'=>null,'message' => \Lang::get('api.user_name_not_registered'), 'status_code' => 400], 200);
    }

    public function saveLogReport($user, $request)
    {
        $logreport = new LogReport();
        $logreport->user_id = $user->id;
        $logreport->ip_address = $request->server->get('REMOTE_ADDR');
        $logreport->detail = $request->header('User-Agent');
        $logreport->save();
    }

}
