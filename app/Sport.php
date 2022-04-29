<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Sport
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Locking[] $lock
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tournament[] $tournaments
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Sport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Sport withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Sport withoutTrashed()
 * @mixin \Eloquent
 */
class Sport extends Model
{
    protected $table = 'sports';
	use SoftDeletes;
    //
    protected $dates = ['deleted_at'];

    public function tournaments()
    {
        return $this->hasMany('App\Tournament');
    }

    public function lock(){
        return $this->hasMany(Locking::class,'sport_id','id');
    }
}
