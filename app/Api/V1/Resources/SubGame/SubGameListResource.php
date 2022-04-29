<?php

namespace App\Api\V1\Resources\SubGame;

use App\Game;
use App\SubGame;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class SubGameListResource extends Resource
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
            'sub_game_name' => $this->name,
            'runners' => isset($this->runners) && $this->runners ? collect($this->runners)->pluck('name')->toArray() : []
        ];
    }

}
