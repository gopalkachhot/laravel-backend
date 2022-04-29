<?php

namespace App\Api\V1\Resources\Tournament;

use App\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class TournamentResource extends Resource
{

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
            'sport_id' => $this->sport_id,
            'tournament_id' => $this->tournament_id,
            'name' => $this->name,
            'sport' => [
               'id'=> $this->sport->id,
               'name'=> $this->sport->name,
            ],
        ];
    }

}
