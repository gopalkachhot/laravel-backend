<?php

namespace App\Jobs;

use App\Betting;
use App\PhpMqtt;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishBetToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $betting;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Betting $betting, $type)
    {
        $this->user = $user;
        $this->betting = $betting;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $betting = $this->betting;
        $res = [
            'id' => $betting->id,
            'user_name' => $betting->user->user_name,
            'game_id' => $betting->runner->SubGame->game_id,
            'runner_id' => $betting->runner_id,
            'runner_name' => $betting->runner->name,
            'runner_type' => $betting->runner->subGame->type,
            'game_name' => $betting->runner->subGame->game->name,
            'tournament_name' => $betting->runner->subGame->game->tournament->name,
            'sport_name' => $betting->runner->subGame->game->tournament->sport->name,
            'loss_amount' => number_format($betting->loss_amount, 2),
            'win_amount' => number_format($betting->win_amount, 2),
            'type' => $betting->type,
            'rate' => number_format($betting->rate, 2),
            'value' => number_format($betting->value, 2),
            'amount' => number_format($betting->amount, 2),
            'is_in_unmatch' => $betting->is_in_unmatch,
            'unmatch_to_match_time' => Carbon::parse($betting->unmatch_to_match_time)->format('Y-m-d H:i:s'),
            'bet_status' => $betting->bet_status,
            'created_at' => Carbon::parse($betting->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($betting->updated_at)->format('Y-m-d H:i:s'),
            'deleted' => $this->type == 'delete' ? true : false
        ];
        User::getParentsUsers($this->user, $users);
        collect($users)->each(function (User $user) use (&$res){
            PhpMqtt::publish('user/' . $user->id, json_encode([
                'type' => 'match_unmatch_bet',
                'data' => $res,
                'user_id' => $user->id
            ]));
        });
    }
}
