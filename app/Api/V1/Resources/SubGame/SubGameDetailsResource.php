<?php

namespace App\Api\V1\Resources\SubGame;

use App\Game;
use App\SubGame;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class SubGameDetailsResource extends Resource
{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        /**@var Game $this*/
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'type' => $this->type,
            'name' => $this->name,
            'max_stack' => $this->max_stack,
            'message' => $this->message,
            'order_in_list' => $this->order_in_list ? $this->order_in_list : 0,
            'status' => $this->status,
            'runners' => $this->runners ? $this->runners : []
        ];
    }

}
