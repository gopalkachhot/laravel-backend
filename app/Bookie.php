<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


/**
 * App\Bookie
 *
 * @property int $id
 * @property string $name
 * @property string $user_name
 * @property string $password
 * @property string $email
 * @property int $created_user_id
 * @property string $mobile
 * @property string $city
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookieGame[] $bookieGame
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Bookie onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereCreatedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookie whereUserName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bookie withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Bookie withoutTrashed()
 * @mixin \Eloquent
 */
class Bookie extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;
    protected $dates = ['deleted_at'];

    public function bookieHasManyGames(){
        $this->hasMany(AssignedFancy::class,'bookie_id','id');
    }

    public function bookieGame(){
        return $this->hasMany(BookieGame::class,'bookie_id','id');
    }
}
