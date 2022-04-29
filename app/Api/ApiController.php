<?php

namespace App\Api;

use App\Bookie;
use App\Http\Controllers\Controller;
use App\LetiDeti;
use App\OauthToken;
use App\User;
use App\UserSetButton;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use mysql_xdevapi\Collection;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;


/**
 * Created by PhpStorm.
 * User: Hardik
 * Date: 09/07/18
 * Time: 11:00 AM
 */
class ApiController extends Controller
{

    public static $DEFAULT_PER_PAGE            = 10; //for pagination
    public static $SUCCESS_STATUS              = 200;
    public static $ERROR_STATUS                = 400;
    public static $UNVERIFIED                  = 402;
    public static $VALIDATION_FAILED_HTTP_CODE = Response::HTTP_BAD_REQUEST;
    public static $UNAUTHORIZED_USER           = Response::HTTP_UNAUTHORIZED;
    public static $parent_users = array();
    public static $child_users = array();

    /**
     * @param $api
     * @throws ValidationException
     */
    public function validateRequest($api)
    {
        $version = \Request::segment(2);
        $rules = Config::get("api_validations.{$api}.{$version}.rules");
        if (!$rules) {
            $rules = Config::get("api_validations.{$api}.v1.rules");
        }

        $messages = Config::get("api_validations.{$api}.{$version}.messages");

        if (!$messages) {
            $messages = Config::get("api_validations.{$api}.v1.messages");
        }

        if ($rules && $messages) {
            $messages = collect($messages)->map(function ($message) {
                return __($message);
            })->toArray();

            $payload = \Request::only(array_keys($rules));
            $validator = Validator::make($payload, $rules, $messages);
            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator,static::$VALIDATION_FAILED_HTTP_CODE);
                //$this->error($validator->errors()->first(), 400);
            }
        }
    }

    /**
     * @param array $codes
     * @return string
     */
    public static function generateVerificationToken(&$codes = [])
    {
        $code = str_random(20);
        $user = User::whereVerificationToken($code)->first();
        if ($user && in_array($code, $codes)) {
            return ApiController::generateVerificationToken($codes[] = $code);
        } else {
            return $code;
        }
    }

    public function generateAccessToken(User $user){
        return $user->createToken('DollarExch');
    }
    public function bookieGenerateAccessToken(Bookie $bookie){
        return $bookie->createToken('DollarExchBookie');
    }

    public static function getParentUsers($user_id){
        $user_data = User::whereId($user_id)->first();
        array_push(ApiController::$parent_users,$user_data);
        if ($user_data && $user_data->user_id != 0){
            $parent_user = User::whereId($user_data->user_id)->first();
            if ($parent_user){
                self::getParentUsers($parent_user->id);
            }
        }
        return ApiController::$parent_users;
    }

    public static function saveLetiDeti($from_user,$to_user,$type,$amount,$remark){
        $transaction_type = $type;
        if ($type == 'credit'){
            $type = 'CRDR';
        }elseif ($type == 'debit'){
            $type = 'CRDR';
            $amount = -($amount);
        }elseif ($type == 'increase_limit'){
            $type = 'Limit';
        }elseif ($type == 'decrease_limit'){
            $type = 'Limit';
            $amount = -($amount);
        }
        $leti_deti = new LetiDeti();
        $leti_deti->from_user_id = ($transaction_type == 'credit' ||  $transaction_type == 'increase_limit') ? $from_user : $to_user;
        $leti_deti->to_user_id = ($transaction_type == 'credit' ||  $transaction_type == 'increase_limit') ? $to_user : $from_user;
        $leti_deti->type = $type;
        $leti_deti->amount = $amount;
        $leti_deti->remark = $remark;
        $leti_deti->from_user_balance = $leti_deti->fromUser->limit - $leti_deti->fromUser->expense;
        $leti_deti->to_user_balance = $leti_deti->toUser->limit - $leti_deti->toUser->expense;
        $leti_deti->save();
        return true;
    }

    public static function setUserButtonDefault($user_id){
        $all = array(
            ["set_button_name"=>"1k","set_button_value"=>"1000"],
            ["set_button_name"=>"2k","set_button_value"=>"2000"],
            ["set_button_name"=>"3k","set_button_value"=>"3000"],
            ["set_button_name"=>"4k","set_button_value"=>"4000"],
            ["set_button_name"=>"5k","set_button_value"=>"5000"],
            ["set_button_name"=>"6k","set_button_value"=>"6000"],
        );
        foreach ($all as $value) {
            $user_set_button = new UserSetButton();
            $user_set_button->user_id = $user_id;
            $user_set_button->button_name = $value['set_button_name'];
            $user_set_button->button_value = $value['set_button_value'];
            $user_set_button->save();
        }
        return true;
    }

    public static function getAllChildUser(Array $ids){
        foreach ($ids as $key => $id){
            $user = User::whereId($id)->first();
            if ($user && $user->is_admin == 'Yes'){
                self::getAllChildUser(User::whereUserId($user->id)->get()->pluck('id')->toArray());
            }else if ($user){
                array_push(ApiController::$child_users,$user->id);
            }
        }
        return ApiController::$child_users;
    }
}
