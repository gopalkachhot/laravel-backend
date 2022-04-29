<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Game\AllRunnerListResource;
use App\Api\V1\Resources\SubGame\SubGameDetailsResource;
use App\Api\V1\Resources\SubGame\SubGameListResource;
use App\Game;
use App\Runner;
use App\SubGame;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubGameController extends ApiController
{

    public function getSubGamesByGames(Request $request){
        $game_ids = $request->id;
        $sub_games = SubGame::whereIn('game_id',$game_ids)->orderByDesc('created_at')->get();
        return SubGameListResource::collection($sub_games)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.sub_game_list_success')
        ]]);
    }

    public function changeSubgameStatus(Request $request)
    {
        $requestPara = $request->only('sub_game_id','status');
        $subgame = SubGame::find($requestPara['sub_game_id']);
        if($subgame)
        {
            $subgame->status = $requestPara['status'];
            $subgame->save();
            $res = [
                'id' => $subgame->id,
                'title' => $subgame->name,
                'message' => $subgame->message ? $subgame->message : '',
                'max_stack' => $subgame->max_stack,
                'sub_game_status' => $subgame->status,
                'type'=> $subgame->type,
                'name' => $subgame->name,
                'graphData' => [],
                'cards' => $subgame->cards,
                'runners_data' => $subgame->runners->map(function (Runner $runner){
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
                        'delay' => $runner->delay,
                        'min_bet' => $runner->min_bet,
                        'max_bet' => $runner->max_bet,
                    ];
                })
            ];
            \App\PhpMqtt::publish('game/'.$subgame->game_id, json_encode($res, true));

            return response(['message' => \Lang::get('api.status_change_success'), 'status_code' => 200], 200);
        }
        else
        {
            return response(['message' => \Lang::get('api.status_change_failed'), 'status_code' => 400], 400);
        }
    }


    public function updateGetDataFromBetfair(Request $request)
    {
        $requestPara = $request->only('sub_game_id','get_data_from_betfair');
        $subgame = SubGame::find($requestPara['sub_game_id']);
        if($subgame)
        {
            $subgame->get_data_from_betfair = $requestPara['get_data_from_betfair'];
            $subgame->save();

            if($subgame->get_data_from_betfair == 'No'){
                $subgame->runners->map(function (Runner $runner){
                    $runner->lay2 = '';
                    $runner->lay2_value = '';
                    $runner->lay1 = '';
                    $runner->lay1_value = '';
                    $runner->lay = '';
                    $runner->lay_value = '';
                    $runner->back = '';
                    $runner->back_value = '';
                    $runner->back1 = '';
                    $runner->back1_value = '';
                    $runner->back2 = '';
                    $runner->back2_value = '';
                    $runner->save();
                });
            }
            return response(['message' => \Lang::get('api.betfair_satus_change_success'), 'status_code' => 200], 200);
        }
        else
        {
            return response(['message' => \Lang::get('api.something_went_wrong'), 'status_code' => 400], 400);
        }
    }

    public function getSubgameData(Request $request){
        $subgame = SubGame::whereId($request->get('id'))->first();
        if ($subgame){
            return SubGameDetailsResource::make($subgame)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.sub_game_list_success')
            ]]);
        }
    }

}
