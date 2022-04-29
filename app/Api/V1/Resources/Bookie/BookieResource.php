<?php

namespace App\Api\V1\Resources\Bookie;

use App\AssignedFancy;
use App\Bookie;
use App\Sport;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BookieResource extends Resource
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
            'email'=>$this->email,
            'mobile'=>$this->mobile,
            'city'=>$this->city,
//            'create_user_name'=>$userNmae,
//            'created_at' => Carbon::parse($this->created_at)->timestamp,
//            'updated_at' => Carbon::parse($this->updated_at)->timestamp
        ];
    }

}
