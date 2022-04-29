<?php

namespace App\Api\V1\Resources\Bookie;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BookieListResource extends Resource
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
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'user_name'=>(string)$this->user_name,
            'email'=>(string)$this->email,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')
        ];
    }

}
