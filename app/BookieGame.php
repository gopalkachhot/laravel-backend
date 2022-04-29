<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\BookieGame
 *
 * @property int $id
 * @property int $bookie_id
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Bookie $bookie
 * @property-read \App\Game $game
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\BookieGame onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame whereBookieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookieGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BookieGame withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\BookieGame withoutTrashed()
 * @mixin \Eloquent
 */
class BookieGame extends Model
{
    protected $table = 'bookie_game';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function game(){
        return $this->belongsTo('App\Game');
    }

    public function bookie(){
        return $this->belongsTo('App\Bookie');
    }
}
