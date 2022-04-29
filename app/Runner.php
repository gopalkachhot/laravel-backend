<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Runner
 *
 * @property int $id
 * @property int $sub_game_id
 * @property string $name
 * @property int|null $betfair_runner_id
 * @property string $lay2
 * @property string $lay2_value
 * @property string $lay1
 * @property string $lay1_value
 * @property string $lay
 * @property string $lay_value
 * @property string $back
 * @property string $back_value
 * @property string $back1
 * @property string $back1_value
 * @property string $back2
 * @property string $back2_value
 * @property float|null $delay
 * @property int|null $min_bet
 * @property int|null $max_bet
 * @property float|null $extra_delay_rate
 * @property float|null $extra_delay
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $status
 * @property-read \App\SubGame $SubGame
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Betting[] $betting
 * @property-read \App\Game $game
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Runner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBack1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBack1Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBack2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBack2Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBackValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereBetfairRunnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereDelay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereExtraDelay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereExtraDelayRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereLay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereLay1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereLay1Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereLay2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereLay2Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereLayValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereMaxBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereMinBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereSubGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Runner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Runner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Runner withoutTrashed()
 * @mixin \Eloquent
 */
class Runner extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function game(){
        return $this->belongsTo('App\Game');
    }

    public function betting(){
        return $this->hasMany(Betting::class,'id','runner_id');
    }

    public function SubGame(){
        return $this->belongsTo('App\SubGame');
    }
}
