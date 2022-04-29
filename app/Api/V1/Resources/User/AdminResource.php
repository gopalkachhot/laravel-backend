<?php

namespace App\Api\V1\Resources\User;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class AdminResource extends Resource
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
            'domain' => $this->domain,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'limit' => $this->limit,
            'mobile' => $this->mobile,
            'city' => $this->city,
            'partnership' => $this->partnership,
            'is_admin' => $this->is_admin,
            'balance' => round($this->limit - $this->expense,0),
            'expose' =>round((int)$this->expose,0),
            'expense' => round($this->expense,0),
        ];
    }

}
