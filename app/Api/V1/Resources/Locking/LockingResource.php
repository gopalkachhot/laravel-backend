<?php

namespace App\Api\V1\Resources\Locking;


use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class LockingResource extends Resource
{

    protected $withoutFields = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lock_by_user = \Auth::user();
        return [
            'id' => (int)$this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->user_name,
            'type' => $this->type,
            'sports_id' => ($this->sport_id) ? $this->sport_id : '',
            'sports_name' => ($this->sport_id) ? $this->sport->name : '',
            'tournament_id' => ($this->tournament_id) ? $this->tournament_id : '',
            'tournament_name' => ($this->tournament_id) ? $this->tournaments->name : '',
            'game_id' => ($this->game_id) ? $this->game_id : '',
            'game_name' => ($this->game_id) ? $this->game->name : '',
            'sub_game_id' => ($this->sub_game_id) ? $this->sub_game_id : '',
            'sub_game_name' => ($this->sub_game_id) ? $this->subGame->name : '',
            'locked_by_user_id' => $lock_by_user->name,
        ];
    }

}

