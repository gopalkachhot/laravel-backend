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

class PublishExposeAndBalanceToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        User::getParentsUsers($this->user, $users);
        collect($users)->each(function (User $user) {
            PhpMqtt::publish('user/' . $user->id, json_encode([
                'type' => 'expose-balance',
                'user_id' => $user->id,
                'data' => [
                    'expose' => (int)$user->expose,
                    'user_id' => round($user->user_id, 0),
                    'balance' => round($user->limit - $user->expense, 0),
                    'limit' => $user->limit
                ],
            ]));
        });
    }
}
