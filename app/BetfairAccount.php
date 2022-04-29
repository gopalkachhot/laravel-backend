<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\BetfairAccount
 *
 * @property int $id
 * @property string $user_name
 * @property string $password
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\BetfairAccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BetfairAccount whereUserName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BetfairAccount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\BetfairAccount withoutTrashed()
 * @mixin \Eloquent
 */
class BetfairAccount extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
