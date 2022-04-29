<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Expose
 *
 * @property int $id
 * @property int $user_id
 * @property int $runners_id
 * @property float $expose
 * @property string|null $book_chart
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Expose onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereBookChart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereExpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereRunnersId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Expose whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expose withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Expose withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Runner $runner
 */
class Expose extends Model
{
    protected $table = 'expose';
    use SoftDeletes;
    //
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function runner()
    {
        return $this->hasOne(Runner::class, 'id', 'runners_id');
    }
}
