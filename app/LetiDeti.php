<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\LetiDeti
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property float $amount
 * @property float $from_user_balance
 * @property float $to_user_balance
 * @property int|null $sub_game_id
 * @property string $type
 * @property string $remark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $fromUser
 * @property-read \App\Game $game
 * @property-read \App\SubGame $subGame
 * @property-read \App\User $toUser
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\LetiDeti onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereFromUserBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereSubGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereToUserBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LetiDeti withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\LetiDeti withoutTrashed()
 * @mixin \Eloquent
 * @property float $upper_level_expense
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LetiDeti whereUpperLevelExpense($value)
 */
class LetiDeti extends Model
{
    protected $table = 'leti_deti';
    use SoftDeletes;
    //
    protected $dates = ['deleted_at'];

    public function fromUser(){
        return $this->hasOne(User::class,'id','from_user_id');
    }
    public function toUser(){
        return $this->hasOne(User::class,'id','to_user_id');
    }
    public function game(){
        return $this->hasOne(Game::class,'id','game_id');
    }

    public function subGame(){
        return $this->hasOne(SubGame::class,'id','sub_game_id');
    }


}
