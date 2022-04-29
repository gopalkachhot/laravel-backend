<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Game\AllRunnerListResource;
use App\Api\V1\Resources\Game\BookieGameGetResource;
use App\Api\V1\Resources\Game\GameDoneResource;
use App\Api\V1\Resources\Game\GameListResource;
use App\Api\V1\Resources\Game\GameAddEditResource;
use App\Api\V1\Resources\Game\GameResource;
use App\Api\V1\Resources\SubGame\SubGameDetailsResource;
use App\Api\V1\Resources\SubGame\SubGameListResource;
use App\Api\V1\Resources\Tournament\TournamentResource;
use App\Betting;
use App\Bookie;
use App\BookieGame;
use App\Game;
use App\Runner;
use App\SubGame;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends ApiController
{

//    public function __construct()
//    {
//        $this->middleware('auth:api');
//    }

    public function listGame(Request $request)
    {
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $page = $request->has('page') ? $request->get('page') : 1;
        $game_list = Game::orderBy('updated_at', 'desc')->where('status','!=','Completed')->with('tournament')->where(function ($query)
        {
            if (\Request::get('search', null))
            {
                $query->orWhere('id', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('name', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('start_time', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('end_time', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('created_at', 'like', '%' . \Request::get('search') . '%');
                $query->orWhere('updated_at', 'like', '%' . \Request::get('search') . '%');
                $query->OrWhereHas('tournament', function ($q)
                {
                    $q->where('name', 'like', '%' . \Request::get('search') . '%');
                });
                $query->OrWhereHas('tournament.sport', function ($q)
                {
                    $q->where('name', 'like', '%' . \Request::get('search') . '%');
                });
            };
        })->paginate($perpage);
        return GameListResource::collection($game_list)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.game_list_success')
        ]]);
    }


    public function addEditGame(Request $request)
    {
        $id = $request->get('id');
        $this->validateRequest('add-edit-game');

        DB::beginTransaction();
        try
        {
            $game = Game::findOrNew($id);
            $game->tournament_id = $request->get('tournament_id');
            $game->name = $request->get('name');
            $game->game_date = Carbon::parse($request->get('game_date'))->format('Y-m-d H:i:s');
            $start_time = $request->get('game_date') . ' ' . $request->get('start_time');
            $game->start_time = Carbon::parse($start_time)->format('Y-m-d H:i:00');
            $game->market_id = $request->get('market_id');
            $game->event_id = (int)$request->get('event_id');
            $game->accept_unmatched = $request->get('accept_unmatched');
            if(!$id)$game->status = 'Inactive';
            $game->save();
            $insertedId = $game->id;
            if ($id)
            {
                $sub_game = SubGame::whereGameId($id)->whereType('Match')->first();
            } else
            {
                $sub_game = new SubGame();
            }
            $runnerType = $request->get('type');
            $sub_game->game_id = $insertedId;
            $sub_game->type = $request->get('type');
            if($runnerType == 'Match'){
                $sub_game->status = 'Active';
                $sub_game->max_profit = $request->get('max_profit');
            }
            $sub_game->name = $request->get('name');
            $sub_game->bookie_id = null;
            $sub_game->save();
            foreach ($request->get('runners') as $key => $value)
            {
                if (isset($value['id']))
                {
                    $runner = Runner::findOrNew($value['id']);
                } else
                {
                    $runner = new Runner();
                }
                $runner->sub_game_id = $sub_game->id;
                $runner->lay2 = '';
                $runner->name = $value['name'];
                $runner->betfair_runner_id = $value['selection_id'] != '' ? $value['selection_id'] : null;
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
                $runner->delay = $request->get('delay');
                $runner->min_bet = $request->get('min_bet');
                $runner->max_bet = $request->get('max_bet');
                $runner->extra_delay = $request->get('extra_delay');
                $runner->extra_delay_rate = $request->get('extra_delay_rate');
                $runner->save();
            }

            BookieGame::whereGameId($insertedId)->forceDelete();
            foreach ($request->get('bookie_id') as $key => $value)
            {
                /*if(isset($value['id'])){
                    $bookie = BookieGame::findOrNew($value['id']);
                }else{
                    $bookie = new BookieGame();
                }*/
                $bookie = new BookieGame();
                $bookie->game_id = $insertedId;
                $bookie->bookie_id = $value;
                $bookie->save();
            }
            DB::commit();

        } catch (\Exception $e)
        {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }

        return GameAddEditResource::make($game)->additional(['meta' => [
            'status_code' => 200,
            'message' => $id ? \Lang::get('api.game_edit_success') : \Lang::get('api.game_add_success')
        ]]);
    }

    public function getGame(Request $request)
    {
        $requestPara = $request->only('game_id');
        $this->validateRequest('get-game');
        $game = Game::whereId($request->get('game_id'))->first();
        if (!$game)
        {
            return response(['message' => \Lang::get('api.no_game_found'), 'status_code' => 400], 200);
        }
        return GameAddEditResource::make($game)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_game_details')
        ]]);
    }

    public function getGames(Request $request)
    {
        $requestPara = $request->only('game_id');
        $this->validateRequest('get-game');
        $game = Game::whereId($request->get('game_id'))->with('tournament')->get();
        if (!$game)
        {
            return response(['message' => \Lang::get('api.no_game_found'), 'status_code' => 400], 200);
        }
        return BookieGameGetResource::collection($game)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.game_list_success')
        ]]);
    }

    public function deleteGame(Request $request)
    {
        $this->validateRequest('delete-game');
        DB::beginTransaction();
        try {
            $sub_games = SubGame::whereGameId($request->get('id'))->pluck('id')->toArray();
            $runners = Runner::whereIn('sub_game_id',$sub_games)->get()->pluck('id')->toArray();
            $betting = Betting::whereIn('runner_id',$runners)->get();
            if ($betting->count() > 0){
                return response(['message' => 'You can\'t delete active game.', 'status_code' => 400], 400);
            }
            $game = Game::findOrFail($request->get('id'))->delete();
            $this->publishGameListData('deleteGame');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if ($game) {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.game_delete_success'), 'status_code' => 200]], 200);
        } else {
            return response(['message' => \Lang::get('api.invalid_game_id'), 'status_code' => 400], 400);
        }
    }

    public function getTournamentBySportId(Request $request)
    {
        $requestPara = $request->only('sport_id');
        $this->validateRequest('get-tournament-by-sport');
        $tournament = Tournament::whereSportId($request->get('sport_id'))->get();
        if (!$tournament)
        {
            return response(['message' => \Lang::get('api.no_tournament_found'), 'status_code' => 400], 200);
        }
        return TournamentResource::collection($tournament)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_tournament_details')
        ]]);
    }

    public function getAllRunerByGameId(Request $request)
    {
        $requestPara = $request->only('game_id');
        $this->validateRequest('all-runner-list-by-game');
        $game_data = SubGame::whereGameId($request->get('game_id'))->with('runners')->get();
        if (count($game_data))
        {
            return AllRunnerListResource::collection($game_data)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.runner_list_success')
            ]]);
        } else
        {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.invalid_game'), 'status_code' => 200]], 200);
        }
    }

    public function getGamesByTournaments(Request $request)
    {
        $tournament_ids = $request->id;
        $games = Game::whereIn('tournament_id', $tournament_ids)->orderByDesc('created_at')->get();
        return GameListResource::collection($games)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.game_list_success')
        ]]);
    }

    public function gameDone(Request $request)
    {
        $user_details = \Auth::user();
        $is_admin = $user_details->is_admin;
        if ($is_admin == 'Yes')
        {
            $requestPara = $request->only('game_id');
            $this->validateRequest('game-done');
            $games_runners = SubGame::leftjoin('runners', 'runners.sub_game_id', '=', 'sub_game.id')->where(function ($query)
            {
                if (\Request::get('game_id'))
                {
                    $query->whereGameId(\Request::get('game_id'));
                };
            })->select('runners.*', 'sub_game.id as sid', 'sub_game.game_id', 'sub_game.name as sub_game_name', 'sub_game.type')->get();
            $game_data = Game::whereId(\Request::get('game_id'))->with(['tournament', 'tournament.sport'])->first();
            $game_data->start_time = Carbon::parse($game_data->start_time)->format('l jS \\of F Y h:i A');
            $res = [];
            $res['match'] = array();
            $res['oddEven'] = array();
            $res['fancy'] = array();
            $res['dabba'] = array();
            $res['toss'] = array();

            foreach ($games_runners as $game)
            {
                $game->max_bet_empty = '';
                if ($game['type'] == 'Match')
                {
                    $res['match'] = $game;
                } else if ($game['type'] == 'OddEven')
                {
                    $res['oddEven'] = $game;
                } else if ($game['type'] == 'Fancy')
                {
                    $res['fancy'] = $game;
                } else if ($game['type'] == 'Dabba')
                {
                    $res['dabba'] = $game;
                } else if ($game['type'] == 'Toss')
                {
                    $res['toss'] = $game;
                }
            }
            $runner['runners'] = $res;
            $result = [
                $game_data,
                $runner
            ];
            return response(['data' => $result, 'meta' => ['message' => \Lang::get('api.game_done_success'), 'status_code' => 200]], 200);
        } else
        {
            return response(['data' => '', 'meta' => ['message' => \Lang::get('api.unauthorized_user'), 'status_code' => 200]], 200);
        }
    }

    public function getGameData(Request $request)
    {
        $requestPara = $request->only('game_id');
        $this->validateRequest('get-game');
        $game = Game::whereId($request->get('game_id'))->where('status', '=', 'Active')->first();
        if (!$game)
        {
            return response(['message' => \Lang::get('api.no_game_found'), 'status_code' => 400], 400);
        }
        return GameResource::make($game)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_game_details')
        ]]);
    }

    public function inPlayStatusChange(Request $request)
    {
        $this->validateRequest('in-play-status-change');
        $game = Game::whereId($request->get('game_id'))->first();
        if ($game)
        {
            $game->in_play = $request->get('in_play');
            $game->save();

            $this->publishGameListData('inPlayStatusChange');

            return response(['message' => \Lang::get('api.in_play_status_change_success'), 'status_code' => 200], 200);
        }
        else
        {
            return response(['message' => \Lang::get('api.no_game_found'), 'status_code' => 400], 400);
        }
    }

    public function gameStatusChange(Request $request)
    {
        $this->validateRequest('game-status-change');
        $game = Game::whereId($request->get('game_id'))->first();
        if ($game)
        {
            $game->status = $request->get('status');
            $game->save();

            $result = [
                'id' => (int)$game->id,
                'name' => (string)$game->name,
                'status' => (string)$game->status,
            ];

            \App\PhpMqtt::publish('gameListSubAdmin', json_encode($result, true));

            $this->publishGameListData('gameStatusChange');

            return response(['message' => \Lang::get('api.game_status_change_success'), 'status_code' => 200], 200);
        }
        else
        {
            return response(['message' => \Lang::get('api.no_game_found'), 'status_code' => 400], 400);
        }
    }

    public function publishGameListData($apiName)
    {
        $game_data = Game::leftjoin('tournaments', 'tournaments.id', '=', 'games.tournament_id')->select('games.name','games.status','games.id','games.in_play', 'games.game_date', 'tournaments.sport_id')->get();

        $soccer = array();
        $tennis = array();
        $cricket = array();
        $horse_riding = array();

        foreach ($game_data as $game) {
            $subGame = collect($game->subGame)->filter(function (SubGame $subGame){
                return $subGame->type == 'Match';
            })->first;
            if ($subGame && collect($subGame->runners)->count()){
                $game->runners = collect($subGame->runners)->toArray()['runners'];
            }
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

        \App\PhpMqtt::publish('gameList', json_encode($res, true));

        return true;
    }

    public function recallGameList(Request $request){
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $sub_games = SubGame::orderByDesc('id')->whereStatus('Done')
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('name', 'like', '%' . \Request::get('search') . '%');
                }
            });
        $sub_games = $sub_games->paginate($perpage);
        return SubGameDetailsResource::collection($sub_games)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.sub_game_list_success')
        ]]);
    }
}
