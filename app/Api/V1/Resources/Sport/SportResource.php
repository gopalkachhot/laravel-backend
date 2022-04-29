<?php

namespace App\Api\V1\Resources\Sport;

use App\Sport;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class SportResource extends Resource
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
            'name' => $this->name,
        ];
    }

}
