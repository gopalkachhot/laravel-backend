<?php


namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Locking\LockingResource;
use App\Locking;
use App\User;
use Illuminate\Cache\Lock;
use Illuminate\Http\Request;
use function PHPSTORM_META\type;


class LockingController extends ApiController{

//    public function __construct()
//    {
//        $this->middleware('auth:api');
//        //$this->middleware('auth:api')->except('addUser');
//    }

    public function lockingListByAdmin(Request $request){
        $user_data = \Auth::user();
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $locking_list = Locking::whereLockedByUserId($user_data->id)->orderBy('updated_at', 'desc')->where(function ($query) {
            if (\Request::get('search', null)) {
                $query->orWhere('id', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('type',\Request::get('search'));
                $query->orWhere('created_at', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('updated_at', 'like', '%' . \Request::get('search') . '%');
                $query->OrWhereHas('user', function ($q) {
                    $q->where('user_name', 'like', '%' . \Request::get('search') . '%');
                });
                $query->OrWhereHas('sport', function ($q) {
                    $q->where('name', 'like', '%' . \Request::get('search') . '%');
                });
                $query->OrWhereHas('tournaments', function ($q) {
                    $q->where('name', 'like', '%' . \Request::get('search') . '%');
                });
                $query->OrWhereHas('game', function ($q) {
                    $q->where('name', 'like', '%' . \Request::get('search') . '%');
                });
                $query->OrWhereHas('subGame', function ($q) {
                    $q->where('name', 'like', '%' . \Request::get('search') . '%');
                });
            };
        })->paginate($perpage);
        return LockingResource::collection($locking_list)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.lock_list_success')
        ]]);
    }

    public function lock(Request $request){
        $user = \Auth::user();
        \DB::beginTransaction();
        try {
            collect($request->get('data', []))->each(function ($value) use ($user) {
                if ($user->id == User::find($value['user_id'])->user_id) {
                    $locking = Locking::whereType($value['type'])->whereUserId($value['user_id'])
                        ->whereLockedByUserId($user->id);
                    $alreadyExist = $locking->get()->filter(function (Locking $data) use ($value) {
                        return (
                            ($data->sport_id == null || $data->sport_id == $value['sport_id']) &&
                            ($data->tournament_id == null || $data->tournament_id == $value['tournament_id']) &&
                            ($data->game_id == null || $data->game_id == $value['game_id']) &&
                            ($data->sub_game_id == null || $data->sub_game_id == $value['sub_game_id'])
                        );
                    });
                    if ($alreadyExist->count() == 0) {
                        if ($value['sport_id'] != null)
                            $locking = $locking->whereSportId($value['sport_id']);
                        if ($value['tournament_id'] != null)
                            $locking = $locking->whereTournamentId($value['tournament_id']);
                        if ($value['game_id'] != null)
                            $locking = $locking->whereGameId($value['game_id']);
                        if ($value['sub_game_id'] != null)
                            $locking = $locking->whereSubGameId($value['sub_game_id']);
                        $locking = $locking->first();
                        if (!$locking) {
                            $locking = new Locking();
                            $locking->user_id = $value['user_id'];
                            $locking->locked_by_user_id = $user->id;
                            $locking->type = $value['type'];
                        }
                        $locking->sport_id = $value['sport_id'];
                        $locking->tournament_id = $value['tournament_id'];
                        $locking->game_id = $value['game_id'];
                        $locking->sub_game_id = $value['sub_game_id'];
                        $locking->save();
                        if ($locking->type == 'User'){
                            $child_users = User::whereUserId($locking->id)->get()->pluck('id')->toArray();
                            array_push($child_users,$locking->id);
                            foreach ($child_users as $user){
                                $data = [
                                    'type' => 'lock',
                                    'data' => 'Yes',
                                    'message' => 'You have been locked by admin.'
                                ];
                                \App\PhpMqtt::publish('user/'.$user, json_encode($data));
                            }
                        }
                    }
                } 
            });
            \DB::commit();
            return [
                'data' => [],
                'meta' => [
                    'status_code' => 200,
                    'message' => \Lang::get('api.user_lock_success')
                ]
            ];
        }catch (\Exception $e){
            \DB::rollBack();
            return $this->error($e->getMessage(), 400);
        }
    }

    public function saveLockData($request,$lockData,$locked_by_user_id){
        $lockData->user_id = $request->get('user_id');
        $lockData->locked_by_user_id = $locked_by_user_id->id;
        $lockData->type = $request->get('type');
        $lockData->sport_id = $request->get('Bet') ? $request->get('sport_id',null) : null;
        $lockData->game_id = $request->get('Bet') ? $request->get('game_id',null) : null;
        $lockData->sub_game_id = $request->get('Bet') ? $request->get('sub_game_id',null) : null;
        $lockData->save();
        return $lockData;
    }

    public function checkUserLock(Request $request)
    {
        $user_id = $request->get('user_id');
        if($user_id){
            $userLock = Locking::where('user_id', $user_id)->first();
            if($userLock){
                return LockingResource::make($userLock)->additional(['meta' => [
                    'status_code' => 200,
                    'message' => \Lang::get('api.user_lock_success')
                ]]);
            }
            else{
                return response(['message' => \Lang::get('api.no_lock_user'), 'status_code' => 400], 400);
            }
        }
    }

    public function deleteLock(Request $request){
        $this->validateRequest('delete-lock');
        \DB::beginTransaction();
        try {
            $lock = Locking::findOrFail($request->get('id'))->delete();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if ($lock) {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.lock_delete_success'), 'status_code' => 200]], 200);
        } else {
            return response(['message' => \Lang::get('api.invalid_lock_id'), 'status_code' => 400], 400);
        }
    }
}
