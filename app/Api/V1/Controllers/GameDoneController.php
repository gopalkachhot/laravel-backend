<?php

namespace App\Api\V1\Controllers;


use App\Api\ApiController;
use App\Betting;
use App\Expose;
use App\Game;
use App\LetiDeti;
use App\Rules;
use App\Runner;
use App\SubGame;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameDoneController extends ApiController
{

//    public function __construct()
//    {
//        $this->middleware('auth:api');
//    }

    public function getRunners(Request $request)
    {

        $this->validateRequest('all-runner-list-by-game');
//        $game_data = Game::whereId($request->get('game_id'))->first();
        $game_data = SubGame::whereGameId($request->get('game_id'))->whereType('Match')->with('runners')->get();
        if (count($game_data))
        {
            return response(['data' => $game_data, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
            /*return RunnerResource::collection($game_data)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.runner_list_success')
            ]]);*/
        } else
        {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
        }
    }

    public function getRunner(Request $request)
    {

        $runnerData = Runner::whereSubGameId($request->sub_game_id)->get();
        if (count($runnerData))
        {
            return response(['data' => $runnerData, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
            /*return RunnerResource::collection($game_data)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.runner_list_success')
            ]]);*/
        } else
        {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
        }
    }

    public function declareResultForGame(Request $request)
    {
        $this->validateRequest('declare-result-game');
        DB::beginTransaction();
        try{
            /** @var SubGame $subGame */
            $subGame = SubGame::find($request->get('id'));
            if($subGame->type == 'Match'){
                $subGameCount = $subGame->game->subGame->whereNotIn('type',['Match'])->count();
                $subGameDoneCount = $subGame->game->subGame->whereIn('status',['Done'])->whereNotIn('type',['Match'])->count();
                if($subGameCount != $subGameDoneCount)
                    return response(['data' => null, 'meta' => ['message' => \Lang::get('api.done_all_subgame_then_do_it'), 'status_code' => 400]], 200);
            }

            $runnerIds = $subGame->runners->map(function (Runner $runner){
                return $runner->id;
            })->toArray();

            if($subGame->status == 'Done'){
                Betting::whereBetStatus('Completed')->where('unmatch_to_match_time', '!=', null)->whereIn('runner_id', $runnerIds)->update(['bet_status' => 'Pending']);
                Expose::withTrashed()->whereIn('runners_id', $runnerIds)->update(['deleted_at' => null]);
                LetiDeti::whereSubGameId($subGame->id)->get()->each(function (LetiDeti $letiDeti) use ($runnerIds){
                    $fromUser = $letiDeti->fromUser;
                    $fromUser->expense -= $letiDeti->amount;
                    $fromUser->upper_level_expense += $letiDeti->upper_level_expense*-1;
                    $expose = Expose::whereUserId($fromUser->id)
                        ->whereIn('runners_id', $runnerIds);
                    $fromUser->expose += $expose->get()->min('expose') * -1;
                    $fromUser->save();
                    $letiDeti->amount = 0;
                    $letiDeti->from_user_balance = $fromUser->limit - $fromUser->expense;
                    $letiDeti->upper_level_expense = 0;
                    $letiDeti->save();
                });
            }

            if($subGame->type == 'Match') {
                //Remove un-matched bets
                Betting::whereBetStatus('Pending')
                    ->whereIn('runner_id', $runnerIds)
                    ->where('unmatch_to_match_time', '=', null)
                    ->get()->each(function (Betting $betting){
                        $user = $betting->user;
                        $user->un_match_expose -= $betting->loss_amount;
                        $user->save();
                    });
            }

            Betting::whereBetStatus('Pending')
                ->select('user_id', DB::raw('count(*) as total'))
                ->groupBy('user_id')
                ->whereIn('runner_id', $runnerIds)
                ->where('unmatch_to_match_time', '!=', null)
                ->get()
                ->each(function ($betting) use ($request, $subGame, $runnerIds){
                    /** @var User $user */
                    $user = User::find($betting->user_id);
                    $expense = 0;
                    if ($subGame->type == 'Match' || $subGame->type == 'Dabba' || $subGame->type == 'Toss'){
                        $expense = Expose::whereRunnersId($request->get('result'))->whereUserId($user->id)->first()->expose;
                    }
                    if ($subGame->type == 'Fancy'){
                        $expense = json_decode(Expose::whereRunnersId($runnerIds[0])->whereUserId($user->id)->first()->book_chart, true)[$request->get('result')];
                    }

                    if ($expense != 0)
                        $user->declareResultForGameLetiDetiTable($expense,$subGame, $request->get('result'));
                });

            $subGame->result = $request->get('result');
            $subGame->status = 'Done';
            $subGame->save();

            Betting::whereBetStatus('Pending')->whereIn('runner_id', $runnerIds)->update(['bet_status' => 'Completed']);

            if($subGame->type == 'Match'){
                $gameData = $subGame->game;
                $gameData->winner_runner_id = $request->get('result');
                $gameData->in_play = 'False';
                $gameData->status = 'Completed';
                $gameData->save();
            }
            DB::commit();

            $res = [
                    'id' => $subGame->id,
                    'name' => $subGame->name,
                    'type' => $subGame->type,
                    'title' => $subGame->name,
                    'sub_game_status' => $subGame->status,
                    'max_stack' => $subGame->max_stack,
                    'message' => $subGame->message,
                    'cards' => $subGame->cards,
                    'graphData' => [],
                    'runners_data' => $subGame->runners->map(function (Runner $runner){
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
                    }),
                ];


            \App\PhpMqtt::publish('game/'.$subGame->game_id, json_encode($res, true));

            if($subGame->type == 'Match') {
                $gameController = new GameController();
                $gameController->publishGameListData('GameDone');
            }

            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.game_done_success'), 'status_code' => 200]], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
    }
}
