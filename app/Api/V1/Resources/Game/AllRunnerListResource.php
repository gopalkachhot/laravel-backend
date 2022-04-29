<?php

namespace App\Api\V1\Resources\Game;

use App\Expose;
use App\SubGame;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use App\Runner;

class AllRunnerListResource extends Resource
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
        $runners_data = collect($this->runners)->map(function ($data) {
            $expose = Expose::whereUserId(\Auth::user()->id)->whereRunnersId($data->id)->first();
            return collect($data)->put('runner_expose',$expose && $expose->expose ? (int)$expose->expose : 0)
                ->put('current_time', Carbon::now()->format('Y-m-d H:i:s'));
        });
        return [
            'id' => (int)$this->id,
            'title' => $this->name,
            'message' => $this->message ? $this->message : '',
            'sub_game_status' => $this->status,
            'type' => $this->type,
            'sub_game_result' => $this->result,
            'cards' => $this->cards,
            'max_stack' => $this->max_stack,
            'max_stack_amount' => $this->max_stack_amount,
            'get_data_from_betfair' => $this->get_data_from_betfair,
            'tournament_id' => $this->game->tournament_id,
            'tournament_name' => $this->game->tournament->name,
            'sport_id' => $this->game->tournament->sport_id,
            'sport_name' => $this->game->tournament->sport->name,
            'name' => (string)$this->name,
            'game_date' => Carbon::parse($this->game->start_time)->format('Y-m-d'),
            'start_time' => Carbon::parse($this->game->start_time)->format('Y-m-d H:i:s'),
            'end_time' => $this->game->end_time,
            'market_id' => (double)$this->game->market_id,
            'event_id' => (int)$this->game->event_id,
            'winner_runner_id' => $this->game->winner_runner_id,
            'status' => $this->game->status,
            'accept_unmatched' => $this->game->accept_unmatched,
            'created_at' => Carbon::parse($this->created_at)->timestamp,
            'updated_at' => Carbon::parse($this->updated_at)->timestamp,
            'min_bet' => count($this->runners) > 0 ? $this->runners[0]->min_bet : '',
            'max_bet' => count($this->runners) > 0 ? $this->runners[0]->max_bet : '',
            'delay' => count($this->runners) > 0 ? $this->runners[0]->delay : '',
            'extra_delay' => count($this->runners) > 0 ? $this->runners[0]->extra_delay : '',
            'extra_delay_rate' => count($this->runners) > 0 ? $this->runners[0]->extra_delay_rate : '',
            'runners_data' => $runners_data,
            'order_in_list' => $this->order_in_list ? $this->order_in_list : null,
        ];
    }
}
