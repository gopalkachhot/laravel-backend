<?php

namespace App\Api\V1\Resources\Betting;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class AllBetListResource extends Resource{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request){
        User::getParentsUsers($this->user, $users);
        return  [
            'id' => $this->id,
            'user_name' => $this->user->user_name,
            'user_id' => $this->user->id,
            'runner_id' => $this->runner_id,
            'runner_name' => $this->runner->name,
            'win_amount' => round($this->win_amount,0),
            'type' => $this->type,
            'rate' => number_format($this->rate, 2),
            'value' => $this->value,
            'runner_type' => $this->runner->SubGame->type,
            'amount' => round($this->amount,0),
            'ip' => $this->ip_address,
            'unmatch_to_match_time' => Carbon::parse($this->unmatch_to_match_time)->format('d-m-Y H:i:s'),
            'browser_details' => $this->browser_detail,
            'parent_list' => count($users) > 0 ? collect($users)->pluck('name') : [],
            'created_at'=>Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'=>Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
