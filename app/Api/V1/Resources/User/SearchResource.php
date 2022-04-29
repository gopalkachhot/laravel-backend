<?php

namespace App\Api\V1\Resources\User;

use App\Sport;
use App\Tournament;
use Illuminate\Http\Resources\Json\Resource;

class  SearchResource extends Resource
{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        if ($this->type == 'game'){
            $tournament = Tournament::whereId($this->tournament_id)->first();
        }

        return  [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'sport_id' => isset($tournament) && $tournament ? $tournament->sport_id : null,
            'route' => isset($tournament) && $tournament ? str_replace(' ', '-', strtolower($tournament->sport->name)) : null,
        ];
    }

}
