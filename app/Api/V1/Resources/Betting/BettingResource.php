<?php

namespace App\Api\V1\Resources\Betting;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BettingResource extends Resource{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request){
        return  [
            'id' => $this->id,
            'user_name' => $this->user->user_name,
            'runner_id' => $this->runner_id,
            'runner_name' => $this->runner->name,
            'runner_type' => $this->runner->subGame->type,
            'game_name' => $this->runner->subGame->game->name,
            'tournament_name' => $this->runner->subGame->game->tournament->name,
            'sport_name' => $this->runner->subGame->game->tournament->sport->name,
            'loss_amount' => $this->loss_amount,
            'win_amount' => $this->win_amount,
            'type' => $this->type,
            'rate' => $this->rate,
            'value' => $this->value,
            'amount' => $this->amount,
            'unmatch_to_match_time' => Carbon::parse($this->unmatch_to_match_time)->format('Y-m-d H:i:s'),
            'bet_status' => $this->bet_status,
            'created_at'=>Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at'=>Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
