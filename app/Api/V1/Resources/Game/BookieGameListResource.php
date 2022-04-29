<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BookieGameListResource extends Resource
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
            'id' => (int)$this->game->id,
            'name' => (string)$this->game->name,
            'status' => (string)$this->game->status,
        ];
    }

}
