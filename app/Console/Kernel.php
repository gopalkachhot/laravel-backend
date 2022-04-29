<?php

namespace App\Console;

use App\Api\V1\Controllers\BetfairController;
use App\Betting;
use App\Jobs\PublishBetToUser;
use App\Jobs\PublishExposeToUser;
use App\Runner;
use App\Sport;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Sport::all()->each(function (Sport $sport){
                $getTournament = BetfairController::getTournament($sport->id);
                $getTournament = json_decode($getTournament);
                $attachment = $getTournament->attachments;
                if(isset($attachment->competitions)){
                    foreach ($attachment->competitions as $tournament){
                        $tournamentData = Tournament::whereTournamentId($tournament->competitionId)->first();
                        if(!$tournamentData){
                            $tournamentData = new Tournament();
                        }
                        $tournamentData->sport_id = $sport->id;
                        $tournamentData->name = $tournament->name;
                        $tournamentData->tournament_id = $tournament->competitionId;
                        $tournamentData->save();
                    }
                }
            });
        })->everyFifteenMinutes();

        $schedule->call(function (){
            for ($a = 0; $a <= 60; $a = $a+5){
                sleep(5);
                Betting::whereBetStatus('Pending')->whereIsInUnmatch('Yes')->whereUnmatchToMatchTime(null)->get()->each(function (Betting $betting) {
                    if (($betting->type == 'Lay' && $betting->runner->lay == $betting->rate) || ($betting->type == 'Back' && $betting->runner->back == $betting->rate)) {
                        if (!$betting->user->isBetLockForRunner($betting->runner)) {
                            $betting->user->matchExposeReCount($betting->loss_amount, $betting->win_amount, $betting->runner, $betting->type);
                            $betting->unmatch_to_match_time = Carbon::now()->toDateTimeString();
                            $betting->save();
                            $user = $betting->user;
                            $user->un_match_expose -= $betting->loss_amount;
                            $user->save();
                            PublishExposeToUser::dispatch($betting->user, $betting->runner);
                            PublishBetToUser::dispatch($user, $betting, 'add');
                        }
                    }
                });
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
