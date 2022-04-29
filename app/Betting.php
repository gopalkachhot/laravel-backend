<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * App\Betting
 *
 * @property int $id
 * @property int $user_id
 * @property int $runner_id
 * @property float $loss_amount
 * @property float $win_amount
 * @property string $type
 * @property float $rate
 * @property float $value
 * @property float $amount
 * @property string $is_in_unmatch
 * @property string|null $unmatch_to_match_time
 * @property string $bet_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Runner $runner
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereBetStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereIsInUnmatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereLossAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereRunnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereUnmatchToMatchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereWinAmount($value)
 * @mixin \Eloquent
 * @property string|null $ip_address
 * @property string|null $browser_detail
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereBrowserDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Betting whereIpAddress($value)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Betting onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Betting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Betting withoutTrashed()
 */
class Betting extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $table ='betting';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function runner()
    {
        return $this->belongsTo('App\Runner');
    }
}
