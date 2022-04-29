<?php


namespace App\Api\V1\Resources\LetiDeti;


use App\Game;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class LetiDetiResource extends Resource
{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        $user = \Auth::user();
        $selected_user = collect($request->get('user_id'))->pluck('id')->toArray();
        $color = $this->sub_game_id ? ($this->amount < 0 ? 'Green-Account' : 'Red-Account') : (in_array($this->from_user_id,$selected_user) ? 'Red-Account' : 'Green-Account');
        return [
            'id' => (int)$this->id,
            'from_user_name'=> $this->fromUser->user_name,
            'to_user_name' => $this->to_user_id ? $this->toUser->user_name : '-',
            'amount'=>round($this->amount,0),
            'sub_game_id'=> $this->sub_game_id,
            'type'=>$this->type,
            'remark'=>$this->remark,
            'color' => $color,
            'balance' => $this->sub_game_id ? ($this->from_user_balance) : ($color == 'Red-Account' ? $this->from_user_balance : $this->to_user_balance),
            'user' => $this->sub_game_id ? '-' : ($user->id == $this->from_user_id ? $this->toUser->user_name : $this->fromUser->user_name),
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y')
        ];

    }

}

