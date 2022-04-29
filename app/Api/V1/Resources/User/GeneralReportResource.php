<?php

namespace App\Api\V1\Resources\User;

use Illuminate\Http\Resources\Json\Resource;

class GeneralReportResource extends Resource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'expense' => $this->expense,
        ];
    }

}
