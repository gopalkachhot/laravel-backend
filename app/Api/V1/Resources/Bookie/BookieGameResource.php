<?php

namespace App\Api\V1\Resources\Bookie;

use App\AssignedFancy;
use App\Bookie;
use App\Sport;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BookieGameResource extends Resource
{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){

        return [
//            'id' => (int)$this->id,
//            'name'=>$this->bookie->name,
//            'bookie_username' => $this->bookie->user_name,
//            'bookie_email' => $this->bookie->email,
            'game_name' => $this->game->name,
        ];
    }

}
