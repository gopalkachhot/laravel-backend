<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LogReport
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property string $detail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LogReport whereUserId($value)
 * @mixin \Eloquent
 */
class LogReport extends Model
{
    protected $table ='log_report';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
