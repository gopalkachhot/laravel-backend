<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Game\BlockResource;
use App\Api\V1\Resources\Game\MPanelGameListResource;
use App\Betting;
use App\Expose;
use App\Game;
use App\Runner;
use App\SubGame;
use App\User;
use Carbon\Carbon;
use App\UserSetButton;
use Illuminate\Http\Request;

class MPanelGameController extends ApiController
{

    public function listGameData(Request $request)
    {
        $game_data = Game::leftjoin('tournaments', 'tournaments.id', '=', 'games.tournament_id')->where(function ($query) {
            $query->whereStatus('Active');
            if (\Request::get('in_play')) {
                $query->whereInPlay(\Request::get('in_play'));
            };
            if (\Request::get('sport_id')) {
                $query->whereSportId(\Request::get('sport_id'));
            };
        })->select('games.name', 'games.status', 'games.id', 'games.in_play', 'tournaments.sport_id')->get();
        $soccer = array();
        $tennis = array();
        $cricket = array();
        $horse_riding = array();
        foreach ($game_data as $game) {
            if ($game['sport_id'] == 1) {
                $soccer[] = $game;
            } else if ($game['sport_id'] == 2) {
                $tennis[] = $game;
            } else if ($game['sport_id'] == 4) {
                $cricket[] = $game;
            } else if ($game['sport_id'] == 7) {
                $horse_riding[] = $game;
            }
        }

        $res['soccer'] = $soccer;
        $res['tennis'] = $tennis;
        $res['cricket'] = $cricket;
        $res['horse_riding'] = $horse_riding;

        return response(['data' => $res, 'meta' => ['message' => \Lang::get('api.game_list_success'), 'status_code' => 200]], 200);
    }

    public function gameRunners(Request $request)
    {
        $this->validateRequest('game-runners');
        $game = Game::find($request->get('game_id'));
        $res = $game->subGame->filter(function (SubGame $subGame) use ($game){
            return !($game->tournament->sport_id == 5 && $subGame->status != 'Active');
        })->map(function (SubGame $subGame) {
            return [
                'id' => $subGame->id,
                'name' => $subGame->name,
                'type' => $subGame->type,
                'title' => $subGame->name,
                'max_profit' => $subGame->max_profit,
                'max_stack' => $subGame->max_stack,
                'sub_game_status' => $subGame->status,
                'message' => $subGame->message,
                'cards' => $subGame->cards,
                'order_in_list' => $subGame->order_in_list ? $subGame->order_in_list : 0,
                'graphData' => [],
                'runners_data' => $subGame->runners->map(function (Runner $runner) {
                    $runner_expose = Expose::whereUserId(\Auth::user()->id)->whereRunnersId($runner->id)->first();
                    return [
                        'id' => $runner->id,
                        'name' => $runner->name,
                        'lay2' => $runner->lay2,
                        'lay2_value' => $runner->lay2_value,
                        'lay1' => $runner->lay1,
                        'lay1_value' => $runner->lay1_value,
                        'lay' => $runner->lay,
                        'lay_value' => $runner->lay_value,
                        'back' => $runner->back,
                        'back_value' => $runner->back_value,
                        'back1' => $runner->back1,
                        'back1_value' => $runner->back1_value,
                        'back2' => $runner->back2,
                        'back2_value' => $runner->back2_value,
                        'min_bet' => $runner->min_bet,
                        'max_bet' => $runner->max_bet,
                        'runner_expose' => $runner_expose ? (int)$runner_expose->expose : 0,
                        'status' => $runner->status,
                        'updated_at' => Carbon::parse($runner->updated_at)->format('Y-m-d H:i:s'),
                        'current_time' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
                }),
            ];
        })->values();

        return response(['data' => $res, 'meta' => ['message' => \Lang::get('api.runner_list_success'), 'status_code' => 200]], 200);
    }

    public function userSetButton(Request $request)
    {
        $all = $request->get('btn_data');
        $user_details = \Auth::user();
        $userId = $user_details->id;
        if ($userId && $request->get('bet_stack')) {
            User::whereId($userId)->update(['bet_stack' => $request->get('bet_stack')]);
        }
        $user_button = UserSetButton::where('user_id', $userId)->get();
        foreach ($all as $value) {
            if ($value['set_button_id'] == null) {
                $user_set_button = new UserSetButton();
                $user_set_button->user_id = $userId;
            } else {
                $user_set_button = UserSetButton::where('id', $value['set_button_id'])->first();
            }
            $user_set_button->button_name = $value['button_name'];
            $user_set_button->button_value = $value['button_value'];
            $user_set_button->save();
        }
//        $this->validateRequest('user-set-button');
//        $requestPara = $request->only('button_value', 'button_name');
        return response(['data' => '', 'meta' => ['message' => count($user_button) ? \Lang::get('api.user_set_button_edit_success') : \Lang::get('api.user_set_button_save_success'), 'status_code' => 200]], 200);
    }

    public function getSetButton(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $user = UserSetButton::where('user_id', $userId)->get();
        $bet_stack = User::whereId($userId)->first();
        return response(['data' => $user, 'meta' => ['bet_stack' => $bet_stack && $bet_stack->bet_stack ? $bet_stack->bet_stack : 0, 'message' => \Lang::get('api.user_set_button_save_success'), 'status_code' => 200]], 200);
    }

    public function booksData(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $sub_game = SubGame::whereGameId($request->game_id)->whereType($request->type)->get()->pluck('id');
        $runners = Runner::whereIn('sub_game_id', $sub_game)->get()->pluck('id');
        User::getChildUsers(User::whereId($userId)->first(),$users);
        $expose = Expose::whereIn('runners_id', $runners)->with('runner')->with('user')->whereIn('user_id', collect($users)->pluck('id')->toArray())
            ->get()
            ->groupby('user_id')->values();
        return response(['data' => $expose, 'meta' => ['message' => 'Books expose found successfully.', 'status_code' => 200]], 200);

    }

    public function getColumns(Request $request)
    {
        $sub_game = SubGame::whereGameId($request->game_id)->whereType($request->type)->get()->pluck('id');
        $runners = Runner::whereIn('sub_game_id', $sub_game)->get();
        return response(['data' => $runners, 'meta' => ['message' => 'Runner found successfully.', 'status_code' => 200]], 200);

    }
    public function getParent(Request $request){
        $allParentUser = ApiController::getParentUsers($request->get('id'));
        if($allParentUser) {
            return response(['data' => $allParentUser, 'meta' => ['message' => 'Parent data found successfully.', 'status_code' => 200]], 200);
        }
    }
    public function getCasinoWinnerList(Request $request){
        $this->validateRequest('casino-winner-list');
        $requestPara = $request->only('game_id', 'record_type');
        $game_data = SubGame::whereGameId($request->get('game_id'))->orderBy('created_at', 'DESC')->where('status', '!=', 'Active');
        // record_type === 1(get last 10 data)
        if($request->get('record_type') == 1) {
            $game_data =    $game_data->oldest();
            $game_data =  $game_data->limit(10);
        }
        $game_data = $game_data->with('runners')->get();
        $res =  $game_data->map(function($subGame, $key) {
            return [
                'id' => $subGame->id,
                'name' => $subGame->name,
                'type' => $subGame->type,
                'result' => $subGame->result,
                'cards' => $subGame->cards,
                'runners_data' => $subGame->runners->filter(function (Runner $runner) use($subGame){
                            return  ($runner->id == $subGame->result );
                        })->map(function (Runner $runner) {
                                return [
                                    'id' => $runner->id,
                                    'name' => $runner->name,
                                    'lay2' => $runner->lay2,
                                    'lay2_value' => $runner->lay2_value,
                                    'lay1' => $runner->lay1,
                                    'lay1_value' => $runner->lay1_value,
                                    'lay' => $runner->lay,
                                    'lay_value' => $runner->lay_value,
                                    'back' => $runner->back,
                                    'back_value' => $runner->back_value,
                                    'back1' => $runner->back1,
                                    'back1_value' => $runner->back1_value,
                                    'back2' => $runner->back2,
                                    'back2_value' => $runner->back2_value,
                                    'min_bet' => $runner->min_bet,
                                    'max_bet' => $runner->max_bet,
                                ];

                })->values(),
            ];
        });

        if (count($res)) {
            return response(['data' => $res, 'meta' => ['message' => 'Data found successfully.', 'status_code' => 200]], 200);
        } else {
            return response(['data' => null, 'meta' => ['message' => 'Invalid game', 'status_code' => 200]], 200);
        }
    }
}
