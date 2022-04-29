<?php


namespace App\Api\V1\Resources\LetiDeti;


use App\Game;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class LetiCustomPaginationResource extends Resource
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
            'id' => (int)$this['id'],
            'from_user_name'=>$this['from_user_name'],
            'to_user_name' => $this['to_user_name'],
            'amount'=>$this['amount'],
            'sub_game_id'=> $this['sub_game_id'],
            'type'=>$this['type'],
            'remark'=>$this['remark'],
            'color' => $this['color'],
            'user' => $this['user'],
            'balance' => $this['balance'],
            'created_at' => $this['created_at']
        ];

    }

}

