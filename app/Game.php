<?php

namespace App;

use App\Api\V1\Resources\Locking\LockingResource;
use Illuminate\Cache\Lock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Game
 *
 * @property int $id
 * @property int $tournament_id
 * @property string $name
 * @property int|null $winner_runner_id
 * @property int|null $score
 * @property string $game_date
 * @property string $start_time
 * @property string|null $end_time
 * @property string $market_id
 * @property string $event_id
 * @property string $status
 * @property string $accept_unmatched
 * @property string $in_play
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AssignedFancy[] $assignFancy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookieGame[] $bookieGame
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Locking[] $lock
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SubGame[] $subGame
 * @property-read \App\Tournament $tournament
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Game onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereAcceptUnmatched($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereGameDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereInPlay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereWinnerRunnerId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Game withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Game withoutTrashed()
 * @mixin \Eloquent
 */
class Game extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function assignFancy(){
        return $this->hasMany(AssignedFancy::class,'game_id','id');
    }

    public function tournament()
    {
        return $this->hasOne(Tournament::class,'id','tournament_id');
    }

    public function subGame(){
        return $this->hasMany(SubGame::class,'game_id','id');
    }

    public function bookieGame(){
        return $this->hasMany(BookieGame::class,'game_id','id');
    }

    public function lock(){
        return $this->hasMany(Locking::class,'game_id','id');
    }
}
