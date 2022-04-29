<?php


namespace App\Api\V1\Resources\BetFairAccount;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class BetFairAccountResource extends Resource{

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
            'user_name' => $this->user_name,
/*            'created_at'=>Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at'=>Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),*/
        ];
    }
}
