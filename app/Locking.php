<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Locking
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int|null $sport_id
 * @property int|null $tournament_id
 * @property int|null $game_id
 * @property int|null $sub_game_id
 * @property int $locked_by_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Game|null $game
 * @property-read \App\Sport $sport
 * @property-read \App\SubGame $subGame
 * @property-read \App\Tournament $tournaments
 * @property-read \App\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Locking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereLockedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereSubGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Locking whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Locking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Locking withoutTrashed()
 * @mixin \Eloquent
 */
class Locking extends Model
{
    protected $table ='locking';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function sport()
    {
        return $this->hasOne(Sport::class,'id','sport_id');
    }

    public function tournaments()
    {
        return $this->hasOne(Tournament::class,'id','tournament_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function subGame(){
        return $this->hasOne(SubGame::class,'id','sub_game_id');
    }

}