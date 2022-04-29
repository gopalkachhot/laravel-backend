<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\AssignedFancy
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\AssignedFancy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy whereBookieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AssignedFancy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AssignedFancy withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\AssignedFancy withoutTrashed()
 * @mixin \Eloquent
 */
class AssignedFancy extends Model
{
    protected $table = 'assigned_fancy';
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function bookie()
    {
        return $this->belongsTo('App\Bookie');
    }

    public function game(){
        return $this->belongsTo('App\Game');
    }
}
