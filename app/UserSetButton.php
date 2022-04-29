<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * App\UserSetButton
 *
 * @property int $id
 * @property int $user_id
 * @property string $button_value
 * @property string $button_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\UserSetButton onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereButtonName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereButtonValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserSetButton whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserSetButton withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\UserSetButton withoutTrashed()
 * @mixin \Eloquent
 */
class UserSetButton extends Model
{
    protected $table = 'user_set_button';
    use SoftDeletes;
    protected $dates = ['deleted_at'];


}