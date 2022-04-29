<?php

namespace App\Api\V1\Resources\User;

use App\User;
use App\UserSetButton;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        $user_details = \Auth::user();
        return  [
            'id' => $this->id,
            'user_id' => $this->user_id,
//            'domain' => $this->domain,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'city' => $this->city,
            'balance' => round(($this->limit - $this->expose), 0),
            'expense' => round($this->expense*-1,0),
            'upper_level_expense' => round($this->upper_level_expense,0),
            'expose' => ($this->expose ? round($this->expose,0) : '-'),
            'limit' => $this->limit,
            'min_bet' => $this->min_bet,
            'max_bet' => $this->max_bet,
            'used_limit' => round($this->used_limit,0),
            'is_admin' => $this->is_admin,
            'partnership' => $this->partnership ? $this->partnership : null,
            'bet_stack' => $this->bet_stack ? $this->bet_stack : 0,
            'user_set_button' => $this->userSetButton ? $this->userSetButton->map(function (UserSetButton $userSetButton){
                return [
                    'id' => $userSetButton->id,
                    'button_name' => $userSetButton->button_name,
                    'button_value' => $userSetButton->button_value,
                ];
            }) : []
            //'is_betting_now' => $this->is_betting_now
            //'created_at' => Carbon::parse($this->created_at)->timestamp,
            //'updated_at' => Carbon::parse($this->created_at)->timestamp
        ];
    }

}
