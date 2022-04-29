<?php


namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Betting\AllBetListResource;
use App\Api\V1\Resources\Betting\BettingResource;
use App\Api\V1\Resources\Locking\LockingResource;
use App\Betting;
use App\Expose;
use App\Game;
use App\Jobs\PublishBetToUser;
use App\Jobs\PublishExposeAndBalanceToUser;
use App\Jobs\PublishExposeToUser;
use App\Locking;
use App\Runner;
use App\SubGame;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\Null_;


class BettingController extends ApiController
{

    public function listBettings(Request $request)
    {
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $betting = Betting::orderByDesc('id')
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('type', \Request::get('search'));

                    $query->OrWhereHas('user', function ($q) {
                        $q->where('user_name', 'like', '%' . \Request::get('search') . '%');
                    });

                    $query->OrWhereHas('runner', function ($q) {
                        $q->where('name', 'like', '%' . \Request::get('search') . '%');
                    });

                    $query->OrWhereHas('runner', function ($q) {
                        $q->where('name', 'like', '%' . \Request::get('search') . '%');
                        $q->where('type', 'like', '%' . \Request::get('search') . '%');
                    });

                    $query->OrWhereHas('game', function ($q) {
                        $q->where('name', 'like', '%' . \Request::get('search') . '%');
                    });

                    $query->OrWhereHas('tournament', function ($q) {
                        $q->where('name', 'like', '%' . \Request::get('search') . '%');
                    });
                }
            });
        $betting = $betting->paginate($perpage);
        return BettingResource::collection($betting)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.betting_list_success')
        ]]);
    }

    //Save Expose
    //Save Bet Data
    public function saveBetData($userId, $runner_id, $lossamount, $winamount, $type, $side_rate, $side_value, $bet_amount)
    {
        DB::beginTransaction();
        try {
            $bet = new Betting();
            $bet->user_id = $userId;
            $bet->runner_id = $runner_id;
            $bet->loss_amount = $lossamount;
            $bet->win_amount = $winamount;
            $bet->type = $type;
            $bet->rate = $side_rate;
            $bet->value = $side_value;
            $bet->amount = $bet_amount;
            $bet->unmatch_to_match_time = Carbon::now();
            $bet->bet_status = 'Pending';
            $bet->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return $bet;
    }

    //Check user lock
    public function checkUserLock($user_id, $sport_id, $tournament_id, $game_id, $sub_game_id)
    {
        if ($user_id) {
            $parent_users = ApiController::getParentUsers($user_id);
            $ids = array_column($parent_users, 'id');
            $userLocks = Locking::whereIn('user_id', $ids)->get();
            if ($userLocks) {
                $flag = true;
                foreach ($userLocks as $userLock) {
                    if ($userLock['type'] == 'User') {
                        $flag = false;
                        $data['data'] = $userLock;
                        $data['message'] = "You have locked by admin";
                        $data['status_code'] = 200;
                        return $data;
                    } else if ($userLock['type'] == 'Bet') {
                        if ($userLock['sport_id'] == $sport_id) {
                            $flag = false;
                            $data['data'] = $userLock;
                            $data['message'] = "You have locked for this sport.";
                            $data['status_code'] = 200;
                            return $data;
                        } else if ($userLock['tournament_id'] == $tournament_id) {
                            $flag = false;
                            $data['data'] = $userLock;
                            $data['message'] = "You have locked for this tournament.";
                            $data['status_code'] = 200;
                            return $data;
                        } else if ($userLock['game_id'] == $game_id) {
                            $flag = false;
                            $data['data'] = $userLock;
                            $data['message'] = "You have locked for this game";
                            $data['status_code'] = 200;
                            return $data;
                        } else if ($userLock['sub_game_id'] == $sub_game_id) {
                            $flag = false;
                            $data['data'] = $userLock;
                            $data['message'] = "You have locked for this runner";
                            $data['status_code'] = 200;
                            return $data;
                        } else if ($userLock['sub_game_id'] == null && $userLock['game_id'] == null && $userLock['sport_id'] == null && $userLock['tournament_id'] == null) {
                            $flag = false;
                            $data['data'] = $userLock;
                            $data['message'] = "You have locked for all sport";
                            $data['status_code'] = 200;
                            return $data;
                        }
                    }
                }
                if ($flag) {
                    $data['data'] = null;
                    $data['message'] = \Lang::get('api.no_lock_user');
                    $data['status_code'] = 400;
                    return $data;
                }
            } else {
                $data['data'] = null;
                $data['message'] = \Lang::get('api.no_lock_user');
                $data['status_code'] = 400;
                return $data;
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function betPlace(Request $request)
    {
        DB::beginTransaction();
        $this->validateRequest('bet-place');
        /** @var User $user */
        $user = \Auth::user();
        if ($user->is_betting_now == 'Yes')
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.your_bet_is_running'), 'status_code' => 400]], 200);

        $user->is_betting_now = 'Yes';
        $user->save();

        try {
            $runner = Runner::find($request->get('runner_id'));
            $isUnMatch = false;

            //Check Validation
            if ($user->is_admin == 'Yes' || $runner->SubGame->status != 'Active') {
                DB::rollBack();
                return response(['data' => null, 'meta' => ['message' => \Lang::get('api.bet_save_error'), 'status_code' => 400]], 200);
            }
            if (($runner->lay > $request->get('rate') && $request->get('type') == 'Lay') || ($runner->back < $request->get('rate') && $request->get('type') == 'Back')) {
                if ($runner->SubGame->game->accept_unmatched == 'Yes' && $runner->SubGame->type == 'Match') {
                    $isUnMatch = true;
                } else {
                    DB::rollBack();
                    return response(['data' => null, 'meta' => ['message' => 'Odds are changed.', 'status_code' => 400]], 200);
                }
            }
            if ($user->isBetLockForRunner($runner)) {
                DB::rollBack();
                return response(['data' => null, 'meta' => ['message' => 'You are bet locked.', 'status_code' => 400]], 200);
            }

            if($isUnMatch) $rate = $request->get('rate');
            else $rate = ($request->get('type') == 'Lay' ? ($request->get('rate') == $runner->lay ? $runner->lay : $runner->lay+0.01) : ($request->get('rate') == $runner->back ? $runner->back : $runner->back-0.01));

            $lossAmount = $winAmount = $request->get('amount');
            if (($runner->SubGame->type == 'Match' || $runner->SubGame->type == 'Dabba' || $runner->SubGame->type == 'Toss') && $request->get('type') == 'Back')
                $winAmount = $request->get('amount') * ($rate - ($runner->SubGame->type == 'Match' ? 1 : 0));
            if (($runner->SubGame->type == 'Match' || $runner->SubGame->type == 'Dabba' || $runner->SubGame->type == 'Toss') && $request->get('type') == 'Lay')
                $lossAmount = $request->get('amount') * ($rate - ($runner->SubGame->type == 'Match' ? 1 : 0));
            if ($runner->SubGame->type == 'Fancy' && $request->get('type') == 'Back')
                $winAmount = $request->get('amount') * ($request->get('rate_value') / 100);
            if ($runner->SubGame->type == 'Fancy' && $request->get('type') == 'Lay')
                $lossAmount = $request->get('amount') * ($request->get('rate_value') / 100);

            if (!$isUnMatch) {
                if ($runner->SubGame->type == 'Match' || $runner->SubGame->type == 'Dabba' || $runner->SubGame->type == 'Toss') {
                    $newExpose = $user->matchExposeReCount($lossAmount, $winAmount, $runner, $request->get('type'));
                }
                if ($runner->SubGame->type == 'Fancy') {
                    $runner->SubGame->runners->each(function (Runner $runn, $key) use ($runner, $winAmount, $lossAmount, $user, $request, $rate) {
                        $user->fancyExposeReCount($runn->id == $runner->id ? $lossAmount : $winAmount, $runn->id == $runner->id ? $winAmount : $lossAmount, $request->get('type'), $rate, $runn);
                    });
                }
            } else {
                $user->un_match_expose += $lossAmount;
            }
            if ((User::find($user->id)->expose + $user->un_match_expose) > ($user->limit - $user->expense)) {
                DB::rollBack();
                return response(['data' => null, 'meta' => ['message' => 'Expose limit.', 'status_code' => 400]], 200);
            }
            sleep($runner->delay + $runner->extra_delay + ($runner->SubGame->type == 'Match' ? 5 : 0));
            $betting = new Betting();
            $betting->user_id = $user->id;
            $betting->runner_id = $runner->id;
            $betting->loss_amount = $lossAmount;
            $betting->win_amount = $winAmount;
            $betting->type = $request->get('type');
            $betting->rate = $rate;
            $betting->value = $request->get('rate_value');
            $betting->amount = $request->get('amount');
            $betting->bet_status = 'Pending';
            $betting->is_in_unmatch = $isUnMatch ? 'Yes' : 'No';
            $betting->unmatch_to_match_time = $isUnMatch ? null : Carbon::now()->toDateTimeString();
            $betting->ip_address = $request->server->get('REMOTE_ADDR');
            $betting->browser_detail = $request->header('User-Agent');
            $betting->save();
            $user->is_betting_now = 'No';
            $user->save();

            $res = [
                'id' => $betting->id,
                'user_name' => $betting->user->user_name,
                'runner_id' => $betting->runner_id,
                'runner_name' => $betting->runner->name,
                'runner_type' => $betting->runner->subGame->type,
                'game_name' => $betting->runner->subGame->game->name,
                'tournament_name' => $betting->runner->subGame->game->tournament->name,
                'sport_name' => $betting->runner->subGame->game->tournament->sport->name,
                'loss_amount' => number_format($betting->loss_amount, 2),
                'win_amount' => number_format($betting->win_amount, 2),
                'type' => $betting->type,
                'rate' => number_format($betting->rate, 2),
                'value' => number_format($betting->value, 2),
                'amount' => number_format($betting->amount, 2),
                'unmatch_to_match_time' => Carbon::parse($betting->unmatch_to_match_time)->format('Y-m-d H:i:s'),
                'bet_status' => $betting->bet_status,
                'created_at' => Carbon::parse($betting->created_at)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($betting->updated_at)->format('Y-m-d H:i:s'),
            ];
            $user->is_betting_now = 'No';
            $user->save();
            DB::commit();

            \App\PhpMqtt::publish('bet_place/' . $runner->SubGame->id, json_encode($res));

            PublishExposeToUser::dispatch($betting->user, $runner);
            PublishBetToUser::dispatch($betting->user, $betting, 'add');
            PublishExposeAndBalanceToUser::dispatch($betting->user);

            return response([
                'data' => null,
                'meta' => ['message' => $isUnMatch ? 'Bet Added in Un-Match.' : 'Bet accepted successfully.', 'status_code' => 200]
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception);
        }
    }

    public function getAllBet(Request $request)
    {
        $user_details = \Auth::user();
        User::getChildUsers(User::whereId($user_details->id)->first(),$users);
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $allBet = Betting::orderByDesc('id')->whereIn('user_id', collect($users)->pluck('id'))->where('bet_status', '=', 'Pending')->paginate($perpage);
        return AllBetListResource::collection($allBet)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_all_bet')
        ]]);
    }

    public function getAllMatchBet(Request $request)
    {
        $user_details = \Auth::user();
        User::getChildUsers(User::whereId($user_details->id)->first(),$users);
        $subGame = SubGame::whereGameId($request->get('game_id'))->get()->pluck('id');
        $runner = Runner::whereIn('sub_game_id', $subGame)->get()->pluck('id');
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $allMatchBet = Betting::orderByDesc('id')->whereIn('user_id', collect($users)->pluck('id'))->where('bet_status', '=', 'Pending')
            ->whereIn('runner_id', $runner)
            ->where('unmatch_to_match_time', '!=', null)
            ->with('runner')->paginate($perpage);
        return AllBetListResource::collection($allMatchBet)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_all_match_bet')
        ]]);
    }

    public function getAllUnmatchBet(Request $request)
    {
        $user_details = \Auth::user();
        User::getChildUsers(User::whereId($user_details->id)->first(),$users);
        $subGame = SubGame::whereGameId($request->get('game_id'))->get()->pluck('id');
        $runner = Runner::whereIn('sub_game_id', $subGame)->get()->pluck('id');
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $allUnMatchBet = Betting::orderByDesc('id')->whereIn('user_id', collect($users)->pluck('id')->toArray())->where('bet_status', '=', 'Pending')
            ->where('is_in_unmatch', '=', 'Yes')
            ->WhereIn('runner_id', $runner)
            ->where('unmatch_to_match_time', '=', null)
            ->with('runner')->paginate($perpage);
        return AllBetListResource::collection($allUnMatchBet)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_all_unmatch_bet')
        ]]);
    }

    public function getAccountStatementBets(Request $request){
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $user = \Auth::user();
        if ($request->get('sub_game_id') && SubGame::whereId($request->get('sub_game_id'))->first()){
            $runners = Runner::whereSubGameId($request->get('sub_game_id'))->get()->pluck('id')->toArray();
            User::getChildUsers(User::whereId($user->id)->first(),$users);
            $bets = Betting::orderByDesc('id')
                ->whereIn('user_id',collect($users)->pluck('id')->toArray())
                ->whereIn('runner_id',$runners)->paginate($perpage);
            return AllBetListResource::collection($bets)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.get_all_match_bet')
            ]]);
        }else{
            return response(['message' => \Lang::get('api.enter_sub_game_id'), 'status_code' => 400], 400);
        }
    }

    public function deleteUnMatchedBet(Request $request){
        if ($request->get('type') == 'all'){
            $betDetail = Betting::whereUserId(\Auth::user()->id)->whereIsInUnmatch('Yes')->whereUnmatchToMatchTime(null)->get();
        }else{
            $betDetail = Betting::whereId($request->get('id'))->whereIsInUnmatch('Yes')->whereUnmatchToMatchTime(null)->get();
        }
        if ($betDetail->count() > 0){
            foreach ($betDetail as $data){
                /** @var User $user */
                $user = $data->user;
                $user->un_match_expose -= $data->loss_amount;
                $user->save();
                $data->delete();
                PublishExposeToUser::dispatch($data->user, $data->runner);
                PublishBetToUser::dispatch($data->user, $data,'delete');
                PublishExposeAndBalanceToUser::dispatch($data->user);
            }
            return response([
                'data' => null,
                'meta' => ['message' => 'Bet has been deleted successfully.', 'status_code' => 200]
            ], 200);
        }else{
            return response(['message' => 'You don\'t have any unmatched bets.', 'status_code' => 400], 400);
        }
    }

    public function unMatchToMatch(Request $request){
        if($request->get('password') === 'DFSDF%$#%ASDFSDG#$%$#$' && $request->get('back') && $request->get('lay')) {
            Betting::whereBetStatus('Pending')->whereIsInUnmatch('Yes')->whereUnmatchToMatchTime(null)->where(function ($q) use ($request){
                $q->where('type', 'Lay')->where('rate', $request->get('lay'));
                $q->orWhere(function ($qq) use ($request){
                    $qq->where('type', 'Back')->where('rate', $request->get('back'));
                });
            })->get()->each(function (Betting $betting) {
                if (($betting->type == 'Lay' && $betting->runner->lay == $betting->rate) || ($betting->type == 'Back' && $betting->runner->back == $betting->rate)) {
                    if (!$betting->user->isBetLockForRunner($betting->runner)) {
                        $betting->user->matchExposeReCount($betting->loss_amount, $betting->win_amount, $betting->runner, $betting->type);
                        $betting->unmatch_to_match_time = Carbon::now()->toDateTimeString();
                        $betting->save();
                        $user = $betting->user;
                        $user->un_match_expose -= $betting->loss_amount;
                        $user->save();
                        PublishExposeToUser::dispatch($betting->user, $betting->runner);
                    }
                }
            });
        }
    }
}
