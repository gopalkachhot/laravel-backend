<?php



namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Rules
 *
 * @property int $id
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Rules onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rules whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Rules withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Rules withoutTrashed()
 * @mixin \Eloquent
 */
class Rules extends Model
{
    protected $table = 'rules';
    use SoftDeletes;
    //
    protected $dates = ['deleted_at'];


}
