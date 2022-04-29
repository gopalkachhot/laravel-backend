<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use App\SubGame;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Arr;

class DPanelGameListResource extends Resource
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
        $tournaments = array();
        Tournament::whereSportId($this->id)->get()->map(function ($data) use (&$tournaments){
            $games = [];
            Game::whereTournamentId($data->id)->whereStatus('Active')
               ->select('id', 'name', 'game_date')->get()->each(function ($game) use (&$games){
                    $flag = true;
                    foreach ($games as &$g){
                        if($g['title'] === Carbon::parse($game->game_date)->toDateString()) {
                            array_push($g['sub'], [
                                'title' => $game->name,
                                'isOpen' => false,
                                'id' => $game->id
                            ]);
                            $g['ids'][] = $game->id;
                            $flag = false;
                            break;
                        }
                    }
                    if($flag){
                        array_push($games, [
                            'title' => Carbon::parse($game->game_date)->toDateString(),
                            'isOpen' => false,
                            'ids' => [$game->id],
                            'sub' => [[
                                'title' => $game->name,
                                'isOpen' => false,
                                'id' => $game->id
                            ]]
                        ]);
                    }
               });
           if (count($games) > 0) {
               array_push($tournaments, [
                   'title' => $data->name,
                   'isOpen' => false,
                   'ids' => Arr::collapse(collect($games)->pluck('ids')->toArray()),
                   'sub' => $games
               ]);
           }
        });

        return [
            'title' => $this->name,
            'sport_id' => $this->id,
            'isOpen' => false,
            'route' => str_replace(' ', '-', strtolower($this->name)),
            'icon' => "img/".str_replace(' ', '-', strtolower($this->name)).".svg",
            'sub' => $tournaments,
            'ids' => Arr::collapse(collect($tournaments)->pluck('ids')->toArray())
        ];
    }

}
