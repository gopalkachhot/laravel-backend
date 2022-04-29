<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\User\AdminResource;
use App\Api\V1\Resources\User\SearchResource;
use App\Api\V1\Resources\User\UserResource;
use App\Game;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\PhpMqtt;


class UserController extends ApiController
{

    public function listUser(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $this->validateRequest('list-user');
        $requestPara = $request->only('type', 'search', 'user_id');
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;

        $users = User::orderBy('updated_at', 'desc')
            ->whereUserId($userId)
            ->where(function ($q) use ($requestPara) {
                ($requestPara['type'] == 'Admin') ? $q->where('is_admin', 'Yes') : $q->where('is_admin', 'No');
            })->where('id','!=',$userId)
            ->where(function ($query) use ($requestPara) {
                if (isset($requestPara['search'])) {
                    $query->orWhere('name', 'like', '%' . $requestPara['search'] . '%');
                    $query->orWhere('domain', 'like', '%' . $requestPara['search'] . '%');
                    $query->orWhere('email', 'like', '%' . $requestPara['search'] . '%');
                    $query->orWhere('mobile', 'like', '%' . $requestPara['search'] . '%');
                }
                if (isset($requestPara['user_id'])) {
                    $query->where('user_id', $requestPara['user_id']);
                }
            });
        $users = $users->paginate($perpage);
        return UserResource::collection($users)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.users_list_success')
        ]]);
    }

    public function listUserByAdmin(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $users = User::orderByDesc('id')->where('user_id', $userId)
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('user_name', 'like', '%' . \Request::get('search', null) . '%');
                    $query->orWhere('email', 'like', '%' . \Request::get('search', null) . '%');
                }
            });
        $users = $users->paginate($perpage);
        return UserResource::collection($users)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.users_list_success')
        ]]);
    }

    public function getUser(Request $request)
    {
        $requestPara = $request->only('user_id');
        $user_details = \Auth::user();
        $userId = $user_details->id;
        if (isset($requestPara['user_id'])) {
            $userId = $requestPara['user_id'];
        }
        $user = User::whereId($userId)->first();
        if (!$user) {
            return response(['message' => \Lang::get('api.no_user_found'), 'status_code' => 400], 400);
        }
        return AdminResource::make($user)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.profile_update')
        ]]);
    }

    public function getUserData(Request $request){
        $requestPara = $request->only('user_id');
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $user = User::whereId($requestPara['user_id'])->whereUserId($userId)->first();
        if (!$user) {
            return response(['message' => \Lang::get('api.no_user_found'), 'status_code' => 400], 200);
        }
        return AdminResource::make($user)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_user_details')
        ]]);
    }

    /*Change password*/
    public function changePassword(Request $request)
    {
        $this->validateRequest('change-password');
        $user_details = \Auth::user();
        $userId = $user_details->id;
        DB::beginTransaction();
        try {
            $user = User::whereId($userId)->first();
            $user->password = \Hash::make($request->get('password'));
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return (['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.change_password')
        ]]);

    }

    public function getAllUser(Request $request)
    {
        $users = User::get();
        if ($users) {
            return UserResource::collection($users)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.get_user_details')
            ]]);
        } else {
            return response(['message' => \Lang::get('api.no_user_found'), 'status_code' => 400], 400);
        }
    }

    public function getUsersSubUser(Request $request)
    {
        $user_details = \Auth::user();
        $users = User::whereUserId($user_details->id)->get();
        if (count($users) > 0) {
            return UserResource::collection($users)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.get_user_details')
            ]]);
        } else {
            return response(['message' => \Lang::get('api.no_user_found'), 'status_code' => 400], 400);
        }
    }

    public function editProfile(Request $request)
    {
        $this->validateRequest('edit-profile');

        $userProfile = User::findOrFail($request->get('id'));

        DB::beginTransaction();
        try {
            $userProfile->name = $request->get('name');
//            $userProfile->domain = $request->get('domain');
            $userProfile->email = $request->get('email');
            $userProfile->mobile = $request->get('mobile');
            $userProfile->city = $request->get('city');
            $userProfile->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return UserResource::make($userProfile)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.user_update_success')
        ]]);
    }

    public function addEditUser(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $from_user = User::whereId($userId)->first();
        $id = $request->get('id');
        if ($id != null) {
            $this->validateRequest('edit-Admin');
            if($request->get('is_admin') =='Yes'){
                $this->validate($request, [
                    'partnership' => 'required',
                ], [
                    'partnership.required' => 'Please enter partnership.',
                ]);
            }
            $user_id = $request->get('id');
//            $password = $request->get('update_password');
            $exitUser = User::whereUserName($request->get('user_name'))->whereNotIn('id', [$user_id])->first();
            if ($exitUser) {
                return response([
                    'message' => \Lang::get('api.user_name_exist'),
                    'status_code' => 400
                ]);
            }

            $userProfile = User::findOrFail($request->get('id'));
            /*if(!$password){
                $publish = false;
                $update_password = $userProfile->password;
            }else{
                $update_password= Hash::make($request->get('update_password'));
                $publish = true;
            }*/
            DB::beginTransaction();
            try {
                $userProfile->name = $request->get('name');
                $userProfile->domain = $user_details->domain;
                $userProfile->user_name = $request->get('user_name');
                $userProfile->email = ($request->get('email') ? $request->get('email') : '');
//                $userProfile->password = $update_password;
                $userProfile->mobile = ($request->get('mobile')) ? $request->get('mobile') : '';
                $userProfile->city = ($request->get('city')) ? $request->get('city') : '';
                $userProfile->is_admin = $request->get('is_admin');
                $userProfile->partnership = ($request->get('partnership')) ? $request->get('partnership') : 100;
                $userProfile->save();
                DB::commit();
                /*if($publish) {
                    $data = [
                        'type' => 'change_password',
                        'data' => 'Yes',
                        'message' => 'Your have been locked by admin.'
                    ];
                    \App\PhpMqtt::publish('user/' . $userProfile->id, json_encode($data));
                }*/

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage(), 400);
            }
            return AdminResource::make($userProfile)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.user_update_success')
            ]]);
        } else {
            if($request->get('is_admin') =='Yes'){
                $this->validate($request, [
                    'partnership' => 'required',
                ], [
                    'partnership.required' => 'Please enter partnership.',
                ]);
            }
            $this->validateRequest('add-admin');

            try {
                if (($user_details->limit - $user_details->used_limit) >= $request->get('limit')){
                    $from_user->used_limit = $from_user->used_limit + $request->get('limit');
                    $from_user->save();
                }else{
                    return response(['message' => \Lang::get('api.insufficient_balance'), 'status_code' => 400], 200);
                }
                if ($user_details->level == 9 && $request->get('is_admin') == 'Yes'){
                    return response(['message' => \Lang::get('api.level_end'), 'status_code' => 400], 200);
                }
                $user = new User();
                $user->user_id = $userId;
                $user->name = $request->get('name');
                $user->domain = $user_details->domain;
                $user->user_name = $request->get('user_name');
                $user->email = ($request->get('email') ? $request->get('email') : '');
                $user->password = Hash::make($request->get('password'));
                $user->mobile = ($request->get('mobile')) ? $request->get('mobile') : '';
                $user->city = ($request->get('city')) ? $request->get('city') : '';
                $user->partnership = ($request->get('partnership')) ? $request->get('partnership') : 100;
                $user->is_admin = $request->get('is_admin');
                $user->is_betting_now = $request->get('is_betting_now', 'No');
                $user->limit = $request->get('limit');
                //$user->balance = $request->get('limit');
                $user->used_limit = 0;
                $user->expense = 0;
                $user->level = $user_details->level + 1;
                $user->save();
                ApiController::saveLetiDeti($from_user->id,$user->id,'increase_limit',$request->get('limit'),'First time credit limit');
                if($request->get('is_admin') == 'No'){
                    ApiController::setUserButtonDefault($user->id);
                }
                DB::commit();
                $token = $this->generateAccessToken($user)->accessToken;

                return AdminResource::make($user)->additional(['meta' => [
                    'token' => $token,
                    'status_code' => 200,
                    'message' => \Lang::get('api.user_save_success')
                ]]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage(), 400);
            }
        }
    }
    public function search(Request $request){
        $user_details = \Auth::user();
        $userId = $user_details->id;
        if($request->search == ''){
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.game_delete_success'), 'status_code' => 200]], 200);
        }
        if($request->type == 'No'){
                $allData = Game::where('name','like','%'.\Request::get('search') . '%')->where('in_play','True')->selectRaw('id,name,"users" as type')->get();
        }else if($request->type == 'Yes'){
                $game = Game::where('name', 'like', '%' . \Request::get('search') . '%')->where('in_play','True')->selectRaw("id,name,'game' as type,tournament_id as tournament_id");
                $allData = User::whereUserId($userId)->where('name', 'like', '%' . \Request::get('search') . '%')->selectRaw("id,name,'user' as type, null as tournament_id")->union($game)->get();
        }
        return SearchResource::collection($allData)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.search_success')
        ]]);
    }

    /*public function testMqtt(Request $request){
        $user = User::whereId(8)->first();
        $res = [
            'id' => $request->get('id'),
            'user_name' => 'user1',
            'game_id' => 1,
            'runner_id' => 1,
            'runner_name' => $request->get('name'),
            'runner_type' => 'Match',
            'game_name' => 'Lancashire Thunder',
            'tournament_name' => 'Lancashire Thunder',
            'sport_name' => 'Cricket',
            'loss_amount' => number_format(100, 2),
            'win_amount' => number_format(50, 2),
            'type' => 'Back',
            'rate' => number_format(11, 2),
            'value' => number_format(98, 2),
            'amount' => number_format(1000, 2),
            'is_in_unmatch' => $request->get('unmatch'),
            'unmatch_to_match_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'bet_status' => 'Pending',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted' => false
        ];
        User::getParentsUsers($user, $users);
        collect($users)->each(function (User $user) use (&$res){
            PhpMqtt::publish('user/' . $user->id, json_encode([
                'type' => 'match_unmatch_bet',
                'data' => $res,
                'user_id' => $user->id
            ]));
        });
        return [
            'status' => 200
        ];
    }*/
}
