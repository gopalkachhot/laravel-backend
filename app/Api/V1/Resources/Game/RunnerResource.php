<?php

namespace App\Api\V1\Resources\Game;

use App\Game;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class RunnerResource extends Resource
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
            'name ' => collect($this->runners)->map(function ($data){
                return (string)$data->name;
            }),
        ];
    }

}
