<?php

namespace App\Jobs;

use App\Expose;
use App\PhpMqtt;
use App\Runner;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishExposeToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $runner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Runner $runner)
    {
        $this->user = $user;
        $this->runner = $runner;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $runner = $this->runner;
        User::getParentsUsers($this->user, $users);
        collect($users)->each(function (User $user) use ($runner){
            $exposesData = Expose::whereIn('runners_id', $runner->SubGame->runners->pluck('id')->toArray())
                ->get()->map(function (Expose $expose) use ($user){
                    return [
                        'expose' => (int)$expose->expose,
                        'id' => $expose->runners_id,
                        'user_id' => $expose->user_id,
                        'balance' => round($user->limit - $user->expense, 0)
                    ];
                });
            PhpMqtt::publish('user/' . $user->id, json_encode([
                'type' => 'expose',
                'data' => $exposesData,
                'user_id' => $user->id
            ]));
        });
    }
}
