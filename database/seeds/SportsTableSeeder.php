<?php

use Illuminate\Database\Seeder;

class SportsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $sportList = [
            [ 'id'=>'1','sport_name' => 'Soccer',],
            [ 'id'=>'2','sport_name' => 'Tennis',],
            [ 'id'=>'4','sport_name' => 'Cricket',],
            [ 'id'=>'5','sport_name' => 'Casino',],
            [ 'id'=>'7','sport_name' => 'Horse Riding',],
        ];

        foreach ($sportList as $sport){
            $sportObj = \App\Sport::find($sport['id']) ?: new \App\Sport();
            $sportObj->id = $sport['id'];
            $sportObj->name = $sport['sport_name'];
            $sportObj->save();
        }

        $tournaments = [
            [
                'id' => '1',
                'sport_id' => '5',
                'name' => 'Teenpatti',
                'tournament_id' => 0
            ],
        ];

        foreach ($tournaments as $tournament){
            $tournamentObj = \App\Tournament::find($tournament['id']) ?: new \App\Tournament();
            $tournamentObj->id = $tournament['id'];
            $tournamentObj->sport_id = $tournament['sport_id'];
            $tournamentObj->name = $tournament['name'];
            $tournamentObj->tournament_id = $tournament['tournament_id'];
            $tournamentObj->save();
        }

        $games = [
            [
                'id' => '1',
                'tournament_id' => '1',
                'name' => 'One day teenpatti',
                'winner_runner_id' => null,
                'score' => null,
                'game_date' => '2021-12-31 00:00:00',
                'start_time' => '2021-12-31 00:00:00',
                'end_time' => null,
                'market_id' => '',
                'event_id' => '',
                'status' => 'Active',
                'accept_unmatched' => 'No',
                'in_play' => true,
            ],
        ];

        foreach ($games as $game){
            $gameObj = \App\Game::find($game['id']) ?: new \App\Game();
            $gameObj->id = $game['id'];
            $gameObj->tournament_id = $game['tournament_id'];
            $gameObj->name = $game['name'];
            $gameObj->winner_runner_id = $game['winner_runner_id'];
            $gameObj->score = $game['score'];
            $gameObj->game_date = $game['game_date'];
            $gameObj->start_time = $game['start_time'];
            $gameObj->end_time = $game['end_time'];
            $gameObj->market_id = $game['market_id'];
            $gameObj->event_id = $game['event_id'];
            $gameObj->status = $game['status'];
            $gameObj->accept_unmatched = $game['accept_unmatched'];
            $gameObj->in_play = $game['in_play'];
            $gameObj->save();
        }

        $bookies = [
            [
                'id' => 1,
                'name' => 'subadmin',
                'user_name' => 'subadmin',
                'password' => Hash::make('123456'),
                'created_user_id' => 1,
                'email' => '',
                'mobile' => '',
                'city' => '',
                'token' => '',
            ]
        ];

        foreach ($bookies as $bookie) {
            $bookieObj = \App\Bookie::find($bookie['id']) ?: new \App\Bookie();
            $bookieObj->id = $bookie['id'];
            $bookieObj->name = $bookie['name'];
            $bookieObj->user_name = $bookie['user_name'];
            $bookieObj->email = $bookie['email'];
            $bookieObj->mobile = $bookie['mobile'];
            $bookieObj->city = $bookie['city'];
            $bookieObj->token = $bookie['token'];
            $bookieObj->password = $bookie['password'];
            $bookieObj->created_user_id = $bookie['created_user_id'];
            $bookieObj->save();
        }

        $bookie_games = [
            [
                'id' => 1,
                'bookie_id' => 1,
                'game_id' => 1,
            ]
        ];

        foreach ($bookie_games as $bookie_game) {
            $bookieGameObj = \App\BookieGame::find($bookie_game['id']) ?: new \App\BookieGame();
            $bookieGameObj->id = $bookie_game['id'];
            $bookieGameObj->bookie_id = $bookie_game['bookie_id'];
            $bookieGameObj->game_id = $bookie_game['game_id'];
            $bookieGameObj->save();
        }

        // Add sub game
        $subGames = [
            [
                'id' => '1',
                'game_id' => '1',
                'bookie_id' => '1',
                'name' => \Carbon\Carbon::now()->format('YmdHis'),
                'type' => 'Dabba',
                'status' => 'Active',
                'max_profit'=> '3',
                'max_stack'=> '10000',
                'max_stack_amount'=> '10000',
                'message'=> '',
                'order_in_list' => '1'
            ],
        ];
        foreach ($subGames as $subGame){
            $subGameObj = \App\SubGame::find($subGame['id']) ?: new \App\SubGame();
            $subGameObj->id = $subGame['id'];
            $subGameObj->game_id = $subGame['game_id'];
            $subGameObj->name = $subGame['name'];
            $subGameObj->bookie_id = $subGame['bookie_id'];
            $subGameObj->type = $subGame['type'];
            $subGameObj->status = $subGame['status'];
            $subGameObj->max_profit = $subGame['max_profit'];
            $subGameObj->max_stack = $subGame['max_stack'];
            $subGameObj->max_stack_amount = $subGame['max_stack_amount'];
            $subGameObj->order_in_list = $subGame['order_in_list'];
            $subGameObj->message = $subGame['message'];
            $subGameObj->save();
        }

        // Add runner
        $runners = [
            [
                'id' => '1',
                'sub_game_id' => '1',
                'name' => 'Player A',
                'lay' => 2.02,
                'lay_value' => 10000,
                'back' => 1.98,
                'back_value' => 10000,
                'delay' => 0,
                'max_bet'=> 10000,
                'status' => 'Suspended',
                'min_bet'=> 0
            ],
            [
                'id' => '2',
                'sub_game_id' => '1',
                'name' => 'Player B',
                'lay' => 2.02,
                'lay_value' => 10000,
                'back' => 1.98,
                'back_value' => 10000,
                'delay' => 0,
                'max_bet'=> 10000,
                'status' => 'Suspended',
                'min_bet'=> 0
            ],
        ];
        foreach ($runners as $runner){
            $runnerObj = \App\Runner::find($runner['id']) ?: new \App\Runner();
            $runnerObj->sub_game_id = $runner['sub_game_id'];
            $runnerObj->lay2 = '';
            $runnerObj->name = $runner['name'];
            $runnerObj->betfair_runner_id = null;
            $runnerObj->lay2_value = '';
            $runnerObj->lay1 = '';
            $runnerObj->lay1_value = '';
            $runnerObj->lay = $runner['lay'];
            $runnerObj->lay_value = $runner['lay_value'];
            $runnerObj->back = $runner['back'];
            $runnerObj->back_value = $runner['back_value'];
            $runnerObj->back1 = '';
            $runnerObj->back1_value = '';
            $runnerObj->back2 = '';
            $runnerObj->back2_value = '';
            $runnerObj->delay = $runner['delay'];
            $runnerObj->min_bet = $runner['min_bet'];
            $runnerObj->max_bet = $runner['max_bet'];
            $runnerObj->status = $runner['status'];
            $runnerObj->extra_delay = 0;
            $runnerObj->extra_delay_rate = 0;
            $runnerObj->save();
        }
    }
}
