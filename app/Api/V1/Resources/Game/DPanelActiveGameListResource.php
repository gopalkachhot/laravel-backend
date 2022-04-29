<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use App\Runner;
use App\SubGame;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class DPanelActiveGameListResource extends Resource
{

    protected $withoutFields = [];

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $runners = array();
        $sub_game = SubGame::whereGameId($this->id)->whereType('Match')->first();
        if (!$sub_game) {
            $sub_game = SubGame::whereGameId($this->id)->whereType('Dabba')->first();
        }
        if ($sub_game) {
            collect($sub_game->runners)->map(function (Runner $runner) use (&$runners) {
                array_push($runners, [
                    'id' => $runner->id,
                    'name' => $runner->name,
                    'lay' => $runner->lay,
                    'back' => $runner->back,
                ]);
            });
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'in_play' => $this->in_play,
            'date' => $this->start_time,
            'game_date' => $this->game_date,
            'runners' => $runners,
            'sport_id' => $this->tournament->sport_id
        ];
    }

}
