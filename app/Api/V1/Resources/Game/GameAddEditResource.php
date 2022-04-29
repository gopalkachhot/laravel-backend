<?php

namespace App\Api\V1\Resources\Game;

use App\BookieGame;
use App\SubGame;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use App\Runner;

class GameAddEditResource extends Resource
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

        $matchArray = [];
        SubGame::whereGameId($this->id)->whereType('Match')->get()->map(function ($sub_game) use (&$matchArray) {
            Runner::whereSubGameId($sub_game->id)->with('game')->get()->map(function ($runner) use (&$matchArray, &$sub_game) {
                $matchArray[] = [
                    'id' => $runner->id,
                    'title' => $sub_game->name,
                    'type' => $sub_game->type,
                    'max_profit' => $sub_game->max_profit,
                    'name' => $runner->name,
                    'event_id'=> $this->event_id,
                    'selection_id' => $runner->betfair_runner_id ? $runner->betfair_runner_id : '',
                    'lay2' => $runner->lay2,
                    'lay2_value' => $runner->lay2_value,
                    'lay1' => $runner->lay1,
                    'lay1_value' => $runner->lay1_value,
                    'lay' => $runner->lay,
                    'lay_value' => $runner->lay_value,
                    'back' => $runner->back,
                    'back_value' => $runner->back_value,
                    'back1' => $runner->back1,
                    'back1_value' => $runner->back1_value,
                    'back2' => $runner->back2,
                    'back2_value' => $runner->back2_value,
                    'min_bet' => $runner->min_bet,
                    'max_bet' => $runner->max_bet,
                    'delay' => $runner->delay,
                    'extra_delay' => $runner->extra_delay,
                    'extra_delay_rate' => $runner->extra_delay_rate,
                    'status' => $runner->status,
                ];
            });
        });

        $bookie_game_array = [];
        BookieGame::whereGameId($this->id)->with('game')->get()->map(function ($bookie_game) use (&$bookie_game_array) {
            $bookie_game_array[] = [
                //'id' => $bookie_game->id,
                'bookie_id' => $bookie_game->bookie_id,
                //'game_id' => $bookie_game->game_id,
                'name' => $bookie_game->bookie->name,
            ];
        });


        return [
            'id' => (int)$this->id,
            'tournament_id' => $this->tournament_id,
            'tournament_name' => $this->tournament->name,
            'sport_id' => $this->tournament->sport_id,
            'sport_name' => $this->tournament->sport->name,
            'name' => (string)$this->name,
            'game_date' => Carbon::parse($this->start_time)->format('Y-m-d'),
            'start_time' => Carbon::parse($this->start_time)->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time,
            'market_id' => (double)$this->market_id,
            'event_id' => (int)$this->event_id,
            'status' => $this->status,
            'accept_unmatched' => $this->accept_unmatched,
            'created_at' => Carbon::parse($this->created_at)->timestamp,
            'updated_at' => Carbon::parse($this->updated_at)->timestamp,
            'min_bet' => count($matchArray) > 0 ? $matchArray[0]['min_bet'] : '',
            'max_bet' => count($matchArray) > 0 ? $matchArray[0]['max_bet'] : '',
            'delay' => count($matchArray) > 0 ? $matchArray[0]['delay'] : '',
            'extra_delay' => count($matchArray) > 0 ? $matchArray[0]['extra_delay'] : '',
            'extra_delay_rate' => count($matchArray) > 0 ? $matchArray[0]['extra_delay_rate'] : '',
            'max_profit' => count($matchArray) > 0 ? $matchArray[0]['max_profit'] : '',
            'new_runners' => $matchArray,
            'bookie_games' => $bookie_game_array,
        ];
    }

}
