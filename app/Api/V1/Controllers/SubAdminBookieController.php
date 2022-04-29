<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Betting\BettingResource;
use App\Api\V1\Resources\Bookie\BookieListResource;
use App\Api\V1\Resources\Bookie\BookieResource;
use App\Api\V1\Resources\Game\AllRunnerListResource;
use App\Api\V1\Resources\Game\BookieGameListResource;
use App\Api\V1\Resources\Game\GameListResource;
use App\Betting;
use App\Bookie;
use App\BookieGame;
use App\Game;
use App\PhpMqtt;
use App\Runner;
use App\SubGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SubAdminBookieController extends ApiController
{

//    public function __construct()
//    {
//        $this->middleware('auth:sub-admin')->except('bookieLogin');
//    }


    public function getBookie(Request $request)
    {
        $requestPara = $request->only('bookie_id');
        $bookie_details = auth()->guard('sub-admin')->user();

        $bookieId = $bookie_details->id;
        if (isset($requestPara['bookie_id'])) {
            $bookieId = $requestPara['bookie_id'];
        }
        $bookie = Bookie::whereId($bookieId)->first();
        if (!$bookie) {
            return response(['message' => \Lang::get('api.no_bookie_found'), 'status_code' => 400], 400);
        }
        return BookieResource::make($bookie)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_bookie_details')
        ]]);
    }


    public function bookieLogin(Request $request)
    {
        $this->validateRequest('bookie-login');
        $requestPara = $request->only('user_name', 'password');

        $bookie = Bookie::whereUserName($requestPara['user_name'])->first();

        if ($bookie) {
            if (Hash::check($requestPara['password'], $bookie->password)) {

                $token = $this->bookieGenerateAccessToken($bookie)->accessToken;
                $data = ['type' => 'logout', 'data' => 'Yes', 'message' => 'Your are logged into another device.'];
                \App\PhpMqtt::publish('bookie/' . $bookie->id, json_encode($data));
                return BookieResource::make($bookie)->additional(['meta' => [
                    'token' => $token,
                    'status_code' => 200,
                    'message' => \Lang::get('api.bookie_login_success')
                ]]);
            } else {
                return response(['data' => null, 'message' => \Lang::get('api.bookie_invalid_credential'), 'status_code' => 400], 200);
            }
        }
        return response(['data' => null, 'message' => \Lang::get('api.bookie_user_name_not_registered'), 'status_code' => 400], 200);
    }

    public function bookieChangePassword(Request $request)
    {
        $this->validateRequest('bookie-change-password');
        $bookie_details = \Auth::guard('sub-admin')->user();
        DB::beginTransaction();
        try {
            $bookie = Bookie::whereId($bookie_details->id)->first();
            $bookie->password = \Hash::make($request->get('password'));
            $bookie->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return (['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.bookie_change_password')
        ]]);

    }

    public function bookieGameList(Request $request)
    {
        $bookie_details = \Auth::guard('sub-admin')->user();
        $game_list = BookieGame::whereHas('game', function ($query){
            $query->where('status','=', 'Active');
        })->orderBy('updated_at', 'desc')->where('bookie_id', $bookie_details->id)->get();
        return BookieGameListResource::collection($game_list)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.game_list_success')
        ]]);
    }

    public function getAllRunerByGameId(Request $request)
    {
        $this->validateRequest('all-runner-list-by-game');
        $id = \Auth::guard('sub-admin')->user()->id;
        $bookie_game = BookieGame::whereBookieId($id)->where('game_id', '=', $request->get('game_id'))->first();
        if (!$bookie_game) {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
        }
        $tournament = Game::find($request->get('game_id'))->tournament;
        $game_data = SubGame::whereGameId($request->get('game_id'))
            ->where(function ($q) use ($tournament) {
                if($tournament->sport_id == 5){
                    $q->where('status', '=', 'Active');
                }
            })
            ->where(function ($q) use ($id) {
                $q->whereNull('bookie_id');
                $q->orWhere('bookie_id', $id);
            })->with('runners')->whereHas('game', function ($query){
                $query->where('status','=', 'Active');
            })->get();

        if (count($game_data)) {
            return AllRunnerListResource::collection($game_data)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.runner_list_success')
            ]]);
        } else {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
        }
    }




    public function editRunnerBookie(Request $request)
    {
        $requestPara = $request->only('id', 'name', 'type', 'lay2', 'lay2_value', 'lay1', 'lay1_value', 'lay', 'lay_value',
            'back', 'back_value', 'back1', 'back1_value', 'back2', 'back2_value');
        $runnerData = Runner::whereId($requestPara['id'])->first();
        if ($runnerData) {
            $runnerData->name = $request->get('name', $runnerData->name) ? $request->get('name', $runnerData->name) : '';
            $runnerData->lay2 = $request->get('lay2', $runnerData->lay2) ? $request->get('lay2', $runnerData->lay2) : '';
            $runnerData->lay2_value = $request->get('lay2_value', $runnerData->lay2_value) ? $request->get('lay2_value', $runnerData->lay2_value) : '';
            $runnerData->lay1 = $request->get('lay1', $runnerData->lay1) ? $request->get('lay1', $runnerData->lay1) : '';
            $runnerData->lay1_value = $request->get('lay1_value', $runnerData->lay1_value) ? $request->get('lay1_value', $runnerData->lay1_value) : '';
            $runnerData->lay = $request->get('lay', $runnerData->lay) ? $request->get('lay', $runnerData->lay) : '';
            $runnerData->lay_value = $request->get('lay_value', $runnerData->lay_value) ? $request->get('lay_value', $runnerData->lay_value) : '';
            $runnerData->back = $request->get('back', $runnerData->back) ? $request->get('back', $runnerData->back) : '';
            $runnerData->back_value = $request->get('back_value', $runnerData->back_value) ? $request->get('back_value', $runnerData->back_value) : '';
            $runnerData->back1 = $request->get('back1', $runnerData->back1) ? $request->get('back1', $runnerData->back1) : '';
            $runnerData->back1_value = $request->get('back1_value', $runnerData->back1_value) ? $request->get('back1_value', $runnerData->back1_value) : '';
            $runnerData->back2 = $request->get('back2', $runnerData->back2) ? $request->get('back2', $runnerData->back2) : '';
            $runnerData->back2_value = $request->get('back2_value', $runnerData->back2_value) ? $request->get('back2_value', $runnerData->back2_value) : '';
            $runnerData->save();
            return response(['message' => \Lang::get('api.runner_update_success'), 'status_code' => 200], 200);
        }
        return response(['message' => \Lang::get('api.runner_not_found'), 'status_code' => 400], 400);
    }

    public function editProfile(Request $request)
    {
        $this->validateRequest('edit-bookie-profile');
        $bookie_details = \Auth::guard('sub-admin')->user();
        $bookieId = $bookie_details->id;

        $exitUser = Bookie::whereUserName($request->get('user_name'))->whereNotIn('id', [$bookieId])->first();
        if ($exitUser) {
            return response([
                'message' => \Lang::get('api.bookie_user_name_exist'),
                'status_code' => 400
            ]);
        }

        $userProfile = Bookie::findOrFail($bookieId);
        DB::beginTransaction();
        try {
            $userProfile->name = $request->get('name');
            $userProfile->user_name = $request->get('user_name');
            $userProfile->email = $request->get('email');
            $userProfile->mobile = $request->get('mobile');
            $userProfile->city = $request->get('city');
            $userProfile->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return BookieResource::make($userProfile)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.profile_update_success')
        ]]);

    }

    public function viewBetByRunnerId(Request $request)
    {
        $this->validateRequest('view-bet-by-runner');
        $per_page = $request->has('per_page') ? $request->get('per_page') : 20;
        $runners = Runner::whereSubGameId($request->get('id'))->get()->pluck('id');

        $bet_list = Betting::whereIn('runner_id',$runners)->orderByDesc('id')->with('user')->with('runner')
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('id', \Request::get('search'));
                    $query->orWhereHas('user', function ($query) {
                        $query->where('user_name', 'like', '%' . \Request::get('search') . '%');
                    });
                    $query->orWhereHas('runner', function ($query) {
                        $query->where('name', 'like', '%' . \Request::get('search') . '%');
                    });
                    $query->orWhere('amount', \Request::get('search'));
                    $query->orWhere('rate', 'like', '%' . \Request::get('search') . '%');
                    $query->orWhere('value', \Request::get('search'));
                };
            });
        if (\Request::has('type') == 'latest') {
            $bet_list = $bet_list->limit(20);
        }
        $bet_list = $bet_list->paginate($per_page);
        return BettingResource::collection($bet_list)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.betting_list_success')
        ]]);
    }

}
