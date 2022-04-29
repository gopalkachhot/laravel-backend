<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class GameResource extends Resource
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
            'id' => (int)$this->id,
            'name'=>$this->name,
            'start_time' => $this->start_time,
            'game_date' => $this->game_date,
            'accept_unmatched' => $this->accept_unmatched,
            'in_play' => $this->in_play,
        ];
    }

}
