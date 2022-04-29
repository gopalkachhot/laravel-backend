<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\SubGame
 *
 * @property int $id
 * @property int $game_id
 * @property int|null $bookie_id
 * @property string $type
 * @property string $name
 * @property string|null $result
 * @property float|null $max_stack
 * @property float|null $max_stack_amount
 * @property float|null $max_profit
 * @property string|null $message
 * @property string $status
 * @property string $get_data_from_betfair
 * @property int $order_in_list
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Game $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Locking[] $lock
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Runner[] $runners
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\SubGame onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereBookieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereGetDataFromBetfair($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereMaxProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereMaxStack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereMaxStackAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereOrderInList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubGame withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\SubGame withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $cards
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SubGame whereCards($value)
 */
class SubGame extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'sub_game';

    public function runners(){
        return $this->hasMany(Runner::class,'sub_game_id','id');
    }

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function lock(){
        return $this->hasMany(Locking::class,'sub_game_id','id');
    }
}
