<?php

namespace App\Api\V1\Resources\Runner;

use App\Runner;
use App\Sport;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class RunnerResource extends Resource
{

    protected $withoutFields = [];
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){

        /*$runnerArray = [];
        Runner::whereGameId($this->game_id)->whereType($this->type)->with('game')->get()->map(function ($runner) use (&$runnerArray){
            $runnerArray[] = [
                'id' => $runner->id,
                'game_id' => $this->game_id,
                'name' => $runner->name,
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

        return $runnerArray;*/

        return  [
            'id'=>$this->id,
            'game_id' => $this->game_id,
            'name' => $this->name,
            'type' => $this->type,
            'lay2' => $this->lay2,
            'lay2_value' => $this->lay2_value,
            'lay1' => $this->lay1,
            'lay1_value' => $this->lay1_value,
            'lay' => $this->lay,
            'lay_value' => $this->lay_value,
            'back' => $this->back,
            'back_value' => $this->back_value,
            'back1' => $this->back1,
            'back1_value' => $this->back1_value,
            'back2' => $this->back2,
            'back2_value' => $this->back2_value,
            'delay' => $this->delay,
            'min_bet' => $this->min_bet,
            'max_bet' => $this->max_bet,
            'extra_delay' => $this->extra_delay,
            'extra_delay_rate' => $this->extra_delay_rate,
            //'status' => $this->status,
        ];
    }

}
