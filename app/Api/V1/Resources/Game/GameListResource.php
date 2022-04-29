<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use App\SubGame;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class GameListResource extends Resource
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

        $sub_gameArray = SubGame::whereGameId($this->id)->whereType('Match')->first();

        return [
            'id' => (int)$this->id,
            'tournament_id'=>$this->tournament_id,
            'tournament_name' => $this->tournament->name,
            'sport_id' => $this->tournament->sport_id,
            'sport_name' => $this->tournament->sport->name,
            'name' => (string)$this->name,
            'winner_runner_id'=>$this->winner_runner_id,
            'score'=>(int)$this->score,
            'game_date'=>Carbon::parse($this->game_date)->format('Y-m-d H:i:s'),
            'start_time'=>Carbon::parse($this->start_time)->format('Y-m-d H:i:s'),
            'end_time'=> Carbon::parse($this->end_time)->format('Y-m-d H:i:s'),
            'market_id'=>(int)$this->market_id,
            'event_id'=>(int)$this->event_id,
            'in_play'=> $this->in_play,
            'status'=>$this->status,
            'full_name'=>$this->tournament->sport->name.' -> '.$this->tournament->name.' -> '.(string)$this->name,
            'created_at' => Carbon::parse($this->created_at)->timestamp,
            'updated_at' => Carbon::parse($this->updated_at)->timestamp,
            'sub_game' => $sub_gameArray,
        ];
    }

}
