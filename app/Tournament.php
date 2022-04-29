<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Tournament
 *
 * @property int $id
 * @property int $sport_id
 * @property string $name
 * @property int $tournament_id betfair's competition id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $game
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Locking[] $lock
 * @property-read \App\Sport $sport
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Tournament onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tournament whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tournament withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Tournament withoutTrashed()
 * @mixin \Eloquent
 */
class Tournament extends Model
{
    protected $table = 'tournaments';
    use SoftDeletes;
    //
    protected $dates = ['deleted_at'];

    public function sport()
    {
        return $this->belongsTo('App\Sport');
    }

    public function game(){
        return $this->hasMany(Game::class,'tournament_id','id');
    }

    public function lock(){
        return $this->hasMany(Locking::class,'tournament_id','id');
    }

}
