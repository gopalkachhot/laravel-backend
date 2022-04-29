<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Game\AllRunnerListResource;
use App\Api\V1\Resources\Runner\RunnerResource;
use App\Expose;
use App\Game;
use App\Runner;
use App\SubGame;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\PhpMqtt;

class RunnerController extends ApiController
{
//    public function __construct()
//    {
//        $this->middleware('auth:api');
//        //$this->middleware('auth:api')->except('addUser');
//    }

    public function addRunner(Request $request)
    {

        $requestPara = $request->only('id', 'game_id', 'type', 'name', 'lay2', 'lay2_value', 'lay1', 'lay1_value', 'lay', 'lay_value', 'back', 'back_value', 'back1', 'back1_value', 'back2', 'back2_value', 'delay', 'min_bet', 'max_bet', 'extra_delay_rate', 'extra_delay', 'status', 'max_stack');
        $name1 = $request->get('name1', null);
        $nameArray = array();
        $game_data = Game::whereId($request->get('game_id'))->first();
        array_push($nameArray, $request->get('name'));
        isset($name1) && $name1 ? array_push($nameArray, $request->get('name1')) : false;

        $back_value = $request->get('type') == 'Toss';
        $id = isset($request->id) && $request->id != '' && $request->id != null ? $request->id : null;
        $this->validateRequest('add-runner');
        $user_details = \Auth::user();

        $bookieId = isset($user_details->user_id) ? null : \Auth::user()->id;

        try {
            \DB::beginTransaction();
            if ($request->get('type') == 'Fancy') {
                $subGame = SubGame::orderByDesc('id')->whereGameId($request->get('game_id'))
                    ->whereType($request->get('type', 'Fancy'))->where('status', '!=', 'Done')
                    ->first();
                $order_in_list = $subGame ? $subGame->order_in_list + 1 : $request->get('order_in_list');
            }
            $sub_game = new SubGame();
            $sub_game->type = $request->get('type');
            $sub_game->game_id = $request->get('game_id');
            $sub_game->bookie_id = $bookieId;
            $sub_game->name = $game_data->name;
            $sub_game->status = 'Inactive';
            $sub_game->max_profit = $request->get('max_profit', null);
            $sub_game->max_stack = $request->get('max_stack');
            $sub_game->order_in_list = isset($order_in_list) ? $order_in_list : 0;
            $sub_game->save();
            foreach ($nameArray as $name) {
                $runner = Runner::findOrNew($id);
                $runner->sub_game_id = $sub_game->id;
                $runner->name = $name ? $name : '';
                $runner->lay2 = isset($request->lay2) ? $request->lay2 : '';
                $runner->lay2_value = isset($request->lay2_value) ? $request->lay2_value : '';
                $runner->lay1 = isset($request->lay1) ? $request->lay1 : '';
                $runner->lay1_value = isset($request->lay1_value) ? $request->lay1_value : '';
                $runner->lay = isset($request->lay) ? $request->lay : '';
                $runner->lay_value = isset($request->lay_value) ? $request->lay_value : '';
                if ($request->get('type') == 'Toss') {
                    $runner->back = isset($request->back) ? $request->back : 1.95;
                    $runner->back_value = isset($request->back_value) ? $request->back_value : 100;
                } else {
                    $runner->back = isset($request->back) ? $request->back : '';
                    $runner->back_value = isset($request->back_value) ? $request->back_value : '';
                }
                $runner->back1 = isset($request->back1) ? $request->back1 : '';
                $runner->back1_value = isset($request->back1_value) ? $request->back1_value : '';
                $runner->back2 = isset($request->back2) ? $request->back2 : '';
                $runner->back2_value = isset($request->back2_value) ? $request->back2_value : '';
                $runner->delay = isset($request->delay) ? $request->delay : null;
                $runner->min_bet = isset($request->min_bet) ? $request->min_bet : null;
                $runner->max_bet = isset($request->max_bet) ? $request->max_bet : null;
                $runner->extra_delay = isset($request->extra_delay) ? $request->extra_delay : 0;
                $runner->extra_delay_rate = isset($request->extra_delay_rate) ? $request->extra_delay_rate : null;
                //Status collum move to sub game table
                //$runner->status = isset($request->status) ? $request->status : 'Active';
                $runner->save();
                /*$game_data = SubGame::whereGameId($request->get('game_id'))->with('runners')->get();
                if (count($game_data))
                {
                    $gameDatas = AllRunnerListResource::collection($game_data);
                    \App\PhpMqtt::publish('add_new_runner/'.$request->get('game_id'), json_encode($gameDatas,true));
                }*/
                \DB::commit();
            }

            $res = [
                'id' => $sub_game->id,
                'title' => $sub_game->name,
                'message' => $sub_game->message ? $sub_game->message : '',
                'max_stack' => $sub_game->max_stack,
                'sub_game_status' => $sub_game->status,
                'type' => $sub_game->type,
                'name' => (string)$sub_game->name,
                'cards' => $sub_game->cards,
                'graphData' => [],
                'runners_data' => $sub_game->runners->map(function (Runner $runner) {
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
                        'status' => $runner->status
                    ];
                })
            ];

            \App\PhpMqtt::publish('game/' . $sub_game->game_id, json_encode($res, true));

        } catch (\Exception $e) {
            return $e->getMessage();
            \DB::rollBack();
        }
        return RunnerResource::make($runner)->additional(['meta' => [
            'status_code' => 200,
            'message' => $id ? \Lang::get('api.runner_update_success') : \Lang::get('api.runner_save_success')
        ]]);

    }

    public function getRunnerByGameId(Request $request)
    {
        $requestPara = $request->only('id', 'game_id', 'type');
        $this->validateRequest('list-runner-by-game');
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $sub_game_data = SubGame::whereGameId($request->get('game_id'))->whereType($request->get('type'))->first();
        $runner_data = Runner::whereSubGameId($sub_game_data->id)->paginate($perpage);
        return RunnerResource::collection($runner_data)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.runner_list_success')
        ]]);

    }

    public function editRunner(Request $request)
    {
        $sub_game_id = $request->get('id');
        $sub_game_title = $request->get('title');
        $sub_game_message = $request->get('message');
        /*if ($sub_game_title == null || $sub_game_title == ''){
            return response(['message' => \Lang::get('api.game_title_can_not_null'), 'status_code' => 400], 400);
        }*/
        $game_id = null;
        try {
            \DB::beginTransaction();
            $sub_game = SubGame::whereId($sub_game_id)->first();
            if ($sub_game && $sub_game->type == 'Fancy') {
                SubGame::whereGameId($sub_game->game_id)->whereType($request->get('type', 'Fancy'))
                    ->where('status', '!=', 'Done')
                    ->get()->map(function (SubGame $subGame) use ($request,$sub_game) {
                        if ($subGame->order_in_list == $request->get('order_in_list')) {
                            $subGame->order_in_list = $sub_game->order_in_list;
                        } elseif ($subGame->order_in_list > $request->get('order_in_list')){
                            $subGame->order_in_list += 1 ;
                        }
                        $subGame->save();
                    });
            }
            $game_id = $sub_game->game_id;
            $sub_game->name = isset($sub_game_title) && $sub_game_title ? $sub_game_title : $sub_game->name;
            $sub_game->message = isset($sub_game_message) && $sub_game_message ? $sub_game_message : '';
            $sub_game->max_stack = $request->has('max_stack') && $request->get('max_stack') ? $request->get('max_stack') : $sub_game->max_stack;
            $sub_game->max_stack_amount = 0;
            $sub_game->order_in_list = $request->has('order_in_list') && $request->get('order_in_list') ? $request->get('order_in_list', $sub_game->order_in_list) : 0;
            $sub_game->status = $sub_game->status == 'Inactive' ? 'Inactive' : 'Active';
            $sub_game->save();

            foreach ($request->runner_data as $runner_save_data) {
                if ($runner_save_data['name'] == null || $runner_save_data['name'] == '') {
                    return response(['message' => \Lang::get('api.runner_name_can_not_null'), 'status_code' => 400], 400);
                }
                $runnerData = Runner::whereId($runner_save_data['id'])->first();
                $runnerData->name = isset($runner_save_data['name']) && $runner_save_data['name'] ? $runner_save_data['name'] : $runnerData->name;
                $runnerData->lay2 = isset($runner_save_data['lay2']) && $runner_save_data['lay2'] ? $runner_save_data['lay2'] : '';
                $runnerData->lay2_value = isset($runner_save_data['lay2_value']) && $runner_save_data['lay2_value'] ? $runner_save_data['lay2_value'] : '';
                $runnerData->lay1 = isset($runner_save_data['lay1']) && $runner_save_data['lay1'] ? $runner_save_data['lay1'] : '';
                $runnerData->lay1_value = isset($runner_save_data['lay1_value']) && $runner_save_data['lay1_value'] ? $runner_save_data['lay1_value'] : '';
                $runnerData->lay = isset($runner_save_data['lay']) && $runner_save_data['lay'] ? $runner_save_data['lay'] : '';
                $runnerData->lay_value = isset($runner_save_data['lay_value']) && $runner_save_data['lay_value'] ? $runner_save_data['lay_value'] : '';
                $runnerData->back = isset($runner_save_data['back']) && $runner_save_data['back'] ? $runner_save_data['back'] : '';
                $runnerData->back_value = isset($runner_save_data['back_value']) && $runner_save_data['back_value'] ? $runner_save_data['back_value'] : '';
                $runnerData->back1 = isset($runner_save_data['back1']) && $runner_save_data['back1'] ? $runner_save_data['back1'] : '';
                $runnerData->back1_value = isset($runner_save_data['back1_value']) && $runner_save_data['back1_value'] ? $runner_save_data['back1_value'] : '';
                $runnerData->back2 = isset($runner_save_data['back2']) && $runner_save_data['back2'] ? $runner_save_data['back2'] : '';
                $runnerData->back2_value = isset($runner_save_data['back2_value']) && $runner_save_data['back2_value'] ? $runner_save_data['back2_value'] : '';
                $runnerData->delay = isset($runner_save_data['delay']) && $runner_save_data['delay'] ? $runner_save_data['delay'] : 0.00;
                $runnerData->status = isset($runner_save_data['status']) && $runner_save_data['status'] ? $runner_save_data['status'] : $runnerData->status;
                $runnerData->save();
                $subGame = $runnerData->SubGame;
                $res = [
                    'id' => $subGame->id,
                    'title' => $subGame->name,
                    'message' => $subGame->message ? $subGame->message : '',
                    'max_stack' => $subGame->max_stack,
                    'order_in_list' => $subGame->order_in_list ? $subGame->order_in_list : 0,
                    'sub_game_status' => $subGame->status,
                    'type' => $subGame->type,
                    //'get_data_from_betfair' => $subGame->get_data_from_betfair,
                    //'tournament_name' => $subGame->game->tournament->name,
                    //'sport_name' => $subGame->game->tournament->sport->name,
                    'name' => (string)$subGame->name,
                    'cards' => $subGame->cards,
                    /*'start_time' => Carbon::parse($subGame->game->start_time)->format('Y-m-d H:i:s'),
                    'market_id' => (double)$subGame->game->market_id,
                    'event_id' => (int)$subGame->game->event_id,
                    'accept_unmatched' => $subGame->game->accept_unmatched,
                    'min_bet' => count($subGame->runners) > 0 ? $subGame->runners[0]->min_bet : '',
                    'max_bet' => count($subGame->runners) > 0 ? $subGame->runners[0]->max_bet : '',
                    'delay' => count($subGame->runners) > 0 ? $subGame->runners[0]->delay : '',
                    'extra_delay' => count($subGame->runners) > 0 ? $subGame->runners[0]->extra_delay : '',
                    'extra_delay_rate' => count($subGame->runners) > 0 ? $subGame->runners[0]->extra_delay_rate : '',*/
                    'graphData' => [],
                    'runners_data' => $subGame->runners->map(function (Runner $runner) {
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
                            'status' => $runner->status
                        ];
                    })
                ];
            }
            \DB::commit();
            if ($sub_game->status != 'Inactive') {
                \App\PhpMqtt::publish('game/' . $subGame->game_id, json_encode($res, true));
            }
            return response(['data' => $res, 'message' => \Lang::get('api.runner_update_success'), 'status_code' => 200], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e->getMessage();
        }
    }

    public function getAdminExpose(Request $request)
    {
        $requestPara = $request->only('lay', 'runner_id');
        $this->validateRequest('get-admin-expose');
        if ($request->has('user_id') && $request->get('user_id')) {
            $user_details = User::find($request->get('user_id'));
        } else {
            $user_details = \Auth::user();
        }
        if ($user_details->user_id == 0 && $user_details->is_admin == 'Yes') {
            $exposeData = Expose::whereUserId($user_details->id)->whereRunnersId($requestPara['runner_id'])->first();
            if ($exposeData) {
                $bookChartArr = json_decode($exposeData->book_chart);

                if ($requestPara['lay'] == 0 || $requestPara['lay'] == 1) {
                    $matchArray = [0, 1, 2, 3, 4];
                } else if ($requestPara['lay'] == 999 || $requestPara['lay'] == 998) {
                    $matchArray = [995, 996, 997, 998, 999];
                } else {
                    $matchArray = [$requestPara['lay'] - 2, $requestPara['lay'] - 1, $requestPara['lay'], $requestPara['lay'] + 1, $requestPara['lay'] + 2];
                }
                $result = array_only($bookChartArr, $matchArray);
                return response(['data' => $result, 'meta' => ['message' => \Lang::get('api.expose_success'), 'status_code' => 200]], 200);
            } else {
                return response(['data' => '', 'meta' => ['message' => \Lang::get('api.no_data_found_book_chart'), 'status_code' => 200]], 200);
            }
        }
    }

    public function getExpose(Request $request)
    {
        $requestPara = $request->only('lay', 'runner_id');
        $this->validateRequest('get-admin-expose');

        $user_details = \Auth::user();

        $exposeData = Expose::whereUserId($user_details->id)->whereRunnersId($requestPara['runner_id'])->first();
        if ($exposeData) {
            $bookChartArr = json_decode($exposeData->book_chart);

            if ($requestPara['lay'] == 0 || $requestPara['lay'] == 1 || $requestPara['lay'] == 2 || $requestPara['lay'] == 3 || $requestPara['lay'] == 4) {
                $matchArray = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
            } else if ($requestPara['lay'] == 999 || $requestPara['lay'] == 998 || $requestPara['lay'] == 997 || $requestPara['lay'] == 996 || $requestPara['lay'] == 995) {
                $matchArray = [990, 991, 992, 993, 994, 995, 996, 997, 998, 999];
            } else {
                $matchArray = [$requestPara['lay'] - 5, $requestPara['lay'] - 4, $requestPara['lay'] - 3, $requestPara['lay'] - 2, $requestPara['lay'] - 1, $requestPara['lay'], $requestPara['lay'] + 1, $requestPara['lay'] + 2, $requestPara['lay'] + 3, $requestPara['lay'] + 4];
            }
            $result = array_only($bookChartArr, $matchArray);
            return response(['data' => $result, 'meta' => ['message' => \Lang::get('api.expose_success'), 'status_code' => 200]], 200);
        } else {
            return response(['data' => '', 'meta' => ['message' => \Lang::get('api.no_data_found_book_chart'), 'status_code' => 200]], 200);
        }
    }

    public function startCasinoGame(Request $request){
        $game_id = $request->get('id');
        SubGame::whereGameId($game_id)->whereStatus('Active')->get()->map(function (SubGame $subGame){
            $res = [
                'id' => $subGame->id,
                'title' => $subGame->name,
                'message' => $subGame->message ? $subGame->message : '',
                'max_stack' => $subGame->max_stack,
                'order_in_list' => $subGame->order_in_list ? $subGame->order_in_list : 0,
                'sub_game_status' => $subGame->status,
                'type' => $subGame->type,
                //'get_data_from_betfair' => $subGame->get_data_from_betfair,
                //'tournament_name' => $subGame->game->tournament->name,
                //'sport_name' => $subGame->game->tournament->sport->name,
                'name' => (string)$subGame->name,
                /*'start_time' => Carbon::parse($subGame->game->start_time)->format('Y-m-d H:i:s'),
                'market_id' => (double)$subGame->game->market_id,
                'event_id' => (int)$subGame->game->event_id,
                'accept_unmatched' => $subGame->game->accept_unmatched,
                'min_bet' => count($subGame->runners) > 0 ? $subGame->runners[0]->min_bet : '',
                'max_bet' => count($subGame->runners) > 0 ? $subGame->runners[0]->max_bet : '',
                'delay' => count($subGame->runners) > 0 ? $subGame->runners[0]->delay : '',
                'extra_delay' => count($subGame->runners) > 0 ? $subGame->runners[0]->extra_delay : '',
                'extra_delay_rate' => count($subGame->runners) > 0 ? $subGame->runners[0]->extra_delay_rate : '',*/
                'graphData' => [],
                'cards' => $subGame->cards,
                'runners_data' => $subGame->runners->map(function (Runner $runner) {
                    $runner->status = 'Active';
                    $runner->save();
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
                        'status' => 'Active',
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'current_time' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
                })
            ];
            \App\PhpMqtt::publish('game/' . $subGame->game_id, json_encode($res, true));
        });
        return response(['data' => null, 'message' => \Lang::get('api.runner_update_success'), 'status_code' => 200], 200);
    }

    public function changeRateCasinoGame(Request $request){
        $game_id = $request->get('id');
        $card = $request->get('card');
        $isDone = false;
        SubGame::whereGameId($game_id)->whereStatus('Active')->get()->map(function (SubGame $subGame) use ($card, $request, &$isDone){
            $subGame->cards = $subGame->cards ? $subGame->cards.' '.$card : $card;
            $subGame->save();
            $command = escapeshellcmd('python '.base_path('backend.py').' "'.$subGame->cards.'"');
            $output = shell_exec($command);
            $newRate = explode( "\n", $output);
            $newRate = [
                'Player A' => trim(explode(':', $newRate[0])[1]),
                'Draw' => trim(explode(':', $newRate[1])[1]),
                'Player B' => trim(explode(':', $newRate[2])[1])
            ];

            $res = [
                'id' => $subGame->id,
                'title' => $subGame->name,
                'message' => $subGame->message ? $subGame->message : '',
                'max_stack' => $subGame->max_stack,
                'order_in_list' => $subGame->order_in_list ? $subGame->order_in_list : 0,
                'sub_game_status' => $subGame->status,
                'type' => $subGame->type,
                'cards' => $subGame->cards,
                'name' => (string)$subGame->name,
                'graphData' => [],
                'runners_data' => []
            ];

            if ($newRate['Player A'] == '0.00' || $newRate['Draw'] == '0.00' || $newRate['Player B'] == '0.00'){
                $isDone = true;
                if($newRate['Draw'] == '0.00'){
                    // TODO:: Cancel all bets and revers exposer.
                } else {
                    $result = 0;
                    $subGame->runners->each(function (Runner $runner) use (&$result, $newRate) {
                        if($newRate[$runner->name] == '0.00') $result = $runner->id;
                    });
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://".$request->header('HOST')."/api/v1/declare-result-for-game",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\"result\":\"".$result."\",\"id\":".$subGame->id."}",
                        CURLOPT_HTTPHEADER => array(
                            "content-type: application/json",
                            "Authorization: ".$request->header('Authorization')
                        ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                }
            } else {
                $subGame->runners->map(function (Runner $runner) use ($newRate, &$res){
                    if($newRate[$runner->name] < 1){
                        $runner->status = 'Active';
                        $newR = $newRate[$runner->name] + 1;
                        if($newR < 2 && $newR > 1.7) {
                            $runner->back = $newR - 0.02;
                            $runner->lay = $newR + 0.02;
                        } elseif ($newR < 1.7 && $newR > 1.3) {
                            $runner->back = $newR - 0.02;
                            $runner->lay = $newR + 0.01;
                        } elseif ($newR < 1.3 && $newR > 1.1) {
                            $runner->back = $newR - 0.01;
                            $runner->lay = $newR + 0.01;
                        } elseif ($newR < 1.1 && $newR > 1.00) {
                            $runner->back = $newR - 0.005;
                            $runner->lay = $newR + 0.01;
                        }
                    } else {
                        $runner->status = 'Suspended';
                    }
                    $runner->back = number_format($runner->back, 2);
                    $runner->lay = number_format($runner->lay, 2);
                    $runner->save();
                    $res['runners_data'][] = [
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
                        'status' => $runner->status,
                        'updated_at' => Carbon::parse($runner->updated_at)->format('Y-m-d H:i:s'),
                        'current_time' => Carbon::now()->format('Y-m-d H:i:s')
                    ];
                });
                \App\PhpMqtt::publish('game/' . $subGame->game_id, json_encode($res, true));
            }
        });
        if ($isDone){
            $subGame = new SubGame();
            $subGame->game_id = $game_id;
            $subGame->name = \Carbon\Carbon::now()->format('YmdHis');
            $subGame->bookie_id = '1';
            $subGame->type = 'Dabba';
            $subGame->status = 'Active';
            $subGame->max_profit = '3';
            $subGame->max_stack = '10000';
            $subGame->max_stack_amount = '10000';
            $subGame->message = '';
            $subGame->order_in_list = '1';
            $subGame->save();

            $res = [
                'id' => $subGame->id,
                'title' => $subGame->name,
                'message' => $subGame->message ? $subGame->message : '',
                'max_stack' => $subGame->max_stack,
                'order_in_list' => $subGame->order_in_list ? $subGame->order_in_list : 0,
                'sub_game_status' => $subGame->status,
                'type' => $subGame->type,
                'cards' => $subGame->cards,
                'name' => (string)$subGame->name,
                'graphData' => [],
                'runners_data' => []
            ];

            $runners = [
                [
                    'sub_game_id' => $subGame->id,
                    'name' => 'Player A',
                    'lay' => 2.02,
                    'lay_value' => 10000,
                    'back' => 1.98,
                    'back_value' => 10000,
                    'delay' => 0,
                    'max_bet'=> 10000,
                    'status' => 'Suspended',
                    'min_bet'=> 0
                ],
                [
                    'sub_game_id' => $subGame->id,
                    'name' => 'Player B',
                    'lay' => 2.02,
                    'lay_value' => 10000,
                    'back' => 1.98,
                    'back_value' => 10000,
                    'delay' => 0,
                    'max_bet'=> 10000,
                    'status' => 'Suspended',
                    'min_bet'=> 0
                ],
            ];
            foreach ($runners as $runner){
                $runnerObj = new \App\Runner();
                $runnerObj->sub_game_id = $runner['sub_game_id'];
                $runnerObj->lay2 = '';
                $runnerObj->name = $runner['name'];
                $runnerObj->betfair_runner_id = null;
                $runnerObj->lay2_value = '';
                $runnerObj->lay1 = '';
                $runnerObj->lay1_value = '';
                $runnerObj->lay = $runner['lay'];
                $runnerObj->lay_value = $runner['lay_value'];
                $runnerObj->back = $runner['back'];
                $runnerObj->back_value = $runner['back_value'];
                $runnerObj->back1 = '';
                $runnerObj->back1_value = '';
                $runnerObj->back2 = '';
                $runnerObj->back2_value = '';
                $runnerObj->delay = $runner['delay'];
                $runnerObj->min_bet = $runner['min_bet'];
                $runnerObj->max_bet = $runner['max_bet'];
                $runnerObj->status = $runner['status'];
                $runnerObj->extra_delay = 0;
                $runnerObj->extra_delay_rate = 0;
                $runnerObj->save();

                $res['runners_data'][] = [
                    'id' => $runnerObj->id,
                    'name' => $runnerObj->name,
                    'lay2' => $runnerObj->lay2,
                    'lay2_value' => $runnerObj->lay2_value,
                    'lay1' => $runnerObj->lay1,
                    'lay1_value' => $runnerObj->lay1_value,
                    'lay' => $runnerObj->lay,
                    'lay_value' => $runnerObj->lay_value,
                    'back' => $runnerObj->back,
                    'back_value' => $runnerObj->back_value,
                    'back1' => $runnerObj->back1,
                    'back1_value' => $runnerObj->back1_value,
                    'back2' => $runnerObj->back2,
                    'back2_value' => $runnerObj->back2_value,
                    'delay' => $runnerObj->delay,
                    'min_bet' => $runnerObj->min_bet,
                    'max_bet' => $runnerObj->max_bet,
                    'status' => $runnerObj->status,
                    'updated_at' => Carbon::parse($runnerObj->updated_at)->format('Y-m-d H:i:s'),
                    'current_time' => Carbon::now()->format('Y-m-d H:i:s')
                ];
            }
            \App\PhpMqtt::publish('game/' . $subGame->game_id, json_encode($res, true));
        }
        return response(['data' => null, 'message' => \Lang::get('api.runner_update_success'), 'status_code' => 200], 200);
    }
}
