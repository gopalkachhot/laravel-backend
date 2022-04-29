<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BookieGameGetResource extends Resource
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
            'tournament_id'=>$this->tournament_id,
            'tournament_name' => $this->tournament->name,
            'sport_id' => $this->tournament->sport_id,
            'sport_name' => $this->tournament->sport->name,
            'name' => (string)$this->name,
            'winner_runner_id'=>$this->winner_runner_id,
            'score'=>(int)$this->score,
            'status'=>$this->status,
            'full_name'=>$this->tournament->sport->name.' -> '.$this->tournament->name.' -> '.(string)$this->name,
            'created_at' => Carbon::parse($this->created_at)->timestamp,
            'updated_at' => Carbon::parse($this->updated_at)->timestamp
        ];
    }

}
