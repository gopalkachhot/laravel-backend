<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Facades\DB;

/**
 * App\User
 *
 * @property int $id
 * @property int $user_id
 * @property string $domain
 * @property string $name
 * @property string $user_name
 * @property string $email
 * @property string $password
 * @property string $mobile
 * @property string $city
 * @property float $partnership
 * @property string|null $expose
 * @property float $limit
 * @property float $used_limit
 * @property float $expense
 * @property float|null $extra_delay
 * @property float|null $min_bet
 * @property float|null $max_bet
 * @property float|null $expose_limit
 * @property string $is_admin
 * @property string $is_betting_now
 * @property int|null $level
 * @property int $bet_stack
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Betting[] $betting
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Locking[] $locks
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSetButton[] $userSetButton
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereBetStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereExpense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereExpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereExposeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereExtraDelay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIsBettingNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereMaxBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereMinBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePartnership($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsedLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUserName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 * @mixin \Eloquent
 * @property float $upper_level_expense
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpperLevelExpense($value)
 * @property float $un_match_expose
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUnMatchExpose($value)
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userSetButton(){
        return $this->hasMany(UserSetButton::class);
    }

    public function betting(){
        return $this->hasMany(Betting::class,'user_id','id');
    }

    public function locks(){
        return $this->hasMany(Locking::class,'user_id','id');
    }

    public static function getParentsUsers(User $user, &$users = []){
        $users[] = $user;
        return $user->user_id == 0 ? true : User::getParentsUsers(User::find($user->user_id), $users);
    }

    public static function getChildUsers(User $user, &$users = []){
        $users[] = $user;
        User::whereUserId($user->id)->get()->map(function ($data) use (&$users){
            return $data->is_admin == 'Yes' ? User::getChildUsers($data,$users) : $users[] = $data;
        });
    }

    /**
     * @param Runner $runner
     * @return bool
     */
    public function isBetLockForRunner(Runner $runner){
        User::getParentsUsers($this, $users);
        return (Locking::whereType('Bet')
            ->whereIn('user_id', collect($users)->map(function ($u){ return $u->id; })->toArray())
            ->where(function ($query) use ($runner){
                /** @var Builder $query */
                $query->where(function ($q) use ($runner) {
                    /** @var Builder $q */
                    $q->where('tournament_id', null)->orWhere('tournament_id', $runner->SubGame->game->tournament_id);
                })->where(function ($q) use ($runner) {
                    /** @var Builder $q */
                    $q->where('sport_id', null)->orWhere('sport_id', $runner->SubGame->game->tournament->sport_id);
                })->where(function ($q) use ($runner) {
                    /** @var Builder $q */
                    $q->where('game_id', null)->orWhere('game_id', $runner->SubGame->game->id);
                })->where(function ($q) use ($runner) {
                    /** @var Builder $q */
                    $q->where('sub_game_id', null)->orWhere('sub_game_id', $runner->sub_game_id);
                });
        })->count() > 0);
    }

    public function declareResultForGameLetiDetiTable($expense, SubGame $subGame, $result){
        if($subGame->type === 'Match' || $subGame->type === 'Dabba') $result = Runner::find($result)->name;
        User::getParentsUsers($this, $users);
        if($expense != 0){
            $tmpExpense = $expense;
            collect($users)->each(function (User $user, $key) use ($expense, $users, $subGame, $result, &$tmpExpense){
                $letiDeti = LetiDeti::whereFromUserId($user->id)->whereSubGameId($subGame->id)->first();
                if($key > 0) {
                    $userExpense = (($expense*-1) * ($user->partnership-($key===1?0:$users[$key-1]->partnership)))/100;
                    $tmpExpense -= $userExpense*-1;
                } else $userExpense = $expense;

                if($letiDeti){
                    $letiDeti->amount += $userExpense;
                } else {
                    $letiDeti = new LetiDeti();
                    $letiDeti->amount = $userExpense;
                    $letiDeti->from_user_id = $user->id;
                    $letiDeti->to_user_id = 0;
                    $letiDeti->sub_game_id = $subGame->id;
                    $letiDeti->type = 'CRDR';
                    $letiDeti->remark = $subGame->name.' ('.$subGame->type.')/'.$result;
                    $letiDeti->to_user_balance = 0;
                }

                if(count($users) > $key+1) {
                    $user->upper_level_expense += $tmpExpense*-1;
                    $letiDeti->upper_level_expense += $tmpExpense*-1;
                    $user->save();
                }

                $fromUser = $letiDeti->fromUser;

                $fromUser->expense += $userExpense*-1;

                $expose = Expose::whereUserId($fromUser->id)
                    ->whereIn('runners_id', $subGame->runners->pluck('id')->toArray());
                $fromUser->expose -= $expose->get()->min('expose') * -1;
                $expose->delete();
                $fromUser->save();

                $letiDeti->from_user_balance = $fromUser->limit - $fromUser->expense;
                $letiDeti->save();
            });
            return true;
        } else {
            collect($users)->each(function (User $user) use ($subGame, $result) {
                $letiDeti = LetiDeti::whereFromUserId($user->id)->whereSubGameId($subGame->id)->first();
                if (!$letiDeti) {
                    $letiDeti = new LetiDeti();
                    $letiDeti->amount = 0;
                    $letiDeti->from_user_id = $user->id;
                    $letiDeti->to_user_id = 0;
                    $letiDeti->sub_game_id = $subGame->id;
                    $letiDeti->type = 'CRDR';
                    $letiDeti->remark = $subGame->name . ' (' . $subGame->type . ')/' . $result;
                    $letiDeti->to_user_balance = 0;
                    $letiDeti->from_user_balance = $user->limit - $user->expense;
                    $letiDeti->save();
                }
            });
        }
    }

    public function matchExposeReCount($lossAmount, $winAmount, Runner $runner, $type, $isBetDelete = false){
        User::getParentsUsers($this, $users);
        collect($users)->each(function (User $user, $key) use ($lossAmount, $winAmount, $runner, $type, $users, $isBetDelete){
            $oldExpose = Expose::whereUserId($user->id)->whereIn('runners_id', $runner->SubGame->runners->pluck('id')->toArray())->get()->min('expose');
            $runner->SubGame->runners->each(function (Runner $runn) use ($runner, $type, &$winAmount, $lossAmount, $key, $user, $users, $isBetDelete){
                if($key == 0){
                    if ($type == 'Back')
                        $amount = $runner->id == $runn->id ? $winAmount*-1 : $lossAmount;
                    else
                        $amount = $runner->id == $runn->id ? $lossAmount : $winAmount*-1;
                } else {
                    if ($type == 'Lay'){
                        $amount = $runner->id == $runn->id ? $lossAmount*-1 : $winAmount;
                        $amount = (($user->partnership - ($key > 1 ? $users[$key-1]->partnership : 0)) * $amount) / 100;
                    }else{
                        $amount = $runner->id == $runn->id ? $winAmount : $lossAmount*-1;
                        $amount = (($user->partnership - ($key > 1 ? $users[$key-1]->partnership : 0)) * $amount) / 100;
                    }
                }
                $expose = Expose::whereUserId($user->id)->whereRunnersId($runn->id)->first();
                if(!$expose){
                    $expose = new Expose();
                    $expose->user_id = $user->id;
                    $expose->runners_id = $runn->id;
                }
                if($isBetDelete) $expose->expose -= $amount*-1;
                else $expose->expose += $amount*-1;
                $expose->save();
            });
            $newExpose = Expose::whereUserId($user->id)->whereIn('runners_id', $runner->SubGame->runners->pluck('id')->toArray())->get()->min('expose');
            $user->expose -= $oldExpose*-1;
            $user->expose += $newExpose*-1;
            $user->save();
        });
    }

    public function fancyExposeReCount($lossAmount, $winAmount, $type, $runs, Runner $runner){
        User::getParentsUsers($this, $users);
        collect($users)->each(function (User $user, $key) use ($lossAmount, $winAmount, $type, $runner, $runs, $users){
            $tmpLoss = (($user->partnership - ($key > 1 ? $users[$key-1]->partnership : 0)) * $lossAmount) / 100;
            $tmpWin = (($user->partnership - ($key > 1 ? $users[$key-1]->partnership : 0)) * $winAmount) / 100;

            /** @var Expose $expose */
            $expose = Expose::whereUserId($user->id)->whereRunnersId($runner->id)->first();
            if(!$expose){
                $expose = new Expose();
                $expose->user_id = $user->id;
                $expose->runners_id = $runner->id;
            }
            $tmpData = collect($expose && $expose->book_chart && is_array(json_decode($expose->book_chart)) ? json_decode($expose->book_chart) : array_fill(0, 999, 0))
                ->map(function ($v, $k) use ($tmpLoss, $tmpWin, $runs, $type, $key){
                if($key === 0){
                    if($type == 'Lay') $v += ($k < $runs ? $tmpWin : $tmpLoss*-1);
                    if($type == 'Back') $v += ($k < $runs ? $tmpLoss*-1 : $tmpWin);
                } else {
                    if($type == 'Lay') $v += ($k < $runs ? $tmpWin*-1 : $tmpLoss);
                    if($type == 'Back') $v += ($k < $runs ? $tmpLoss : $tmpWin*-1);
                }
                return $v;
            });

            $expose->book_chart = json_encode($tmpData->toArray());
            $user->expose -= $expose->expose*-1;
            $user->expose += $tmpData->min()*-1;
            $expose->expose = $tmpData->min();
            $expose->save();
            $user->save();
            //Todo:: Implement MQTT publish code
        });
    }
}
