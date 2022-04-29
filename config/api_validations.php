<?php

return [

    'login' => [
        'v1' => [
            'rules' => [
                'user_name' => 'required',
                'password' => 'required'
            ],
            'messages' => [
                'user_name.required' => 'Please enter username.',
                'password.required' => 'Password can’t be blank.'
            ],
        ],
    ],

    'add-sport' => [
        'v1' => [
            'rules' => [
                'name' => 'required'
            ],
            'messages' => [
                'name.required' => 'Please enter name.',
            ],
        ],
    ],

    'add-edit-game' => [
        'v1' => [
            'rules' => [
                'sport_id' => 'required',
                'tournament_id' => 'required',
                'event_id' => 'required',
                'market_id' => 'required',
                'type' => 'required',
                'name' => 'required',
                'game_date' => 'required',
                'start_time' => 'required',
                'delay' => 'required',
                'min_bet' => 'required',
                'max_bet' => 'required',
                'extra_delay' => 'required',
                'extra_delay_rate' => 'required',

            ],
            'messages' => [
                'sport_id.required' => 'Please enter sport id.',
                'tournament_id.required' => 'Please enter tournament id.',
                'event_id.required' => 'Please enter event id.',
                'market_id.required' => 'Please enter market id.',
                'type.required' => 'Please enter type.',
                'name.required' => 'Please enter name.',
                'game_date.required' => 'Please enter game date.',
                'start_time.required' => 'Please enter start time.',
                'delay.required' => 'Please enter delay.',
                'min_bet.required' => 'Please enter min bet.',
                'max_bet.required' => 'Please enter max bet.',
                'extra_delay.required' => 'Please enter extra delay.',
                'extra_delay_rate.required' => 'Please enter extra delay rate.',
            ],
        ],
    ],

    'add-bookie' => [
        'v1' => [
            'rules' => [
                'name' => 'required|max:255',
                'user_name' => 'required|unique:bookies|max:255',
                'password' => 'required|min:6|regex:/^[a-zA-Z0-9]+$/',
//                'password' => 'required|min:6|regex:/^[a-zA-Z0-9]+$/|confirmed',
//                'password_confirmation' => 'required',
            ],
            'messages' => [
                'name.required' => 'Please enter name.',
                'user_name.required' => 'Please enter username.',
                'user_name.unique' => 'Username is already registered with us. Try Login.',
                'password.required' => 'Please enter password.',
                'password.regex' => 'Special characters are not allowed in password.',
                'password.min' => 'Please enter minimum 6 character alphanumeric password.',
//                'password.confirmed' => 'Confirm password and password must be same.',
//                'password_confirmation.required' => 'Please enter confirm password.'
            ],
        ],
    ],
    'edit-bookie' => [
        'v1' => [
            'rules' => [
                'name' => 'required|max:255',
                'user_name' => 'required|unique:bookies|max:255',

            ],
            'messages' => [
                'name.required' => 'Please enter name.',
                'user_name.required' => 'Please enter username.',
                'user_name.unique' => 'Username is already registered with us. Try Login.',
            ],
        ],
    ],

    'add-edit-tournament'=>[
        'v1' => [
            'rules' => [
                'sport_id' => 'required',
                'name' => 'required|max:255',
            ],
            'messages' => [
                'sport_id.required' => 'Please enter sport id.',
                'name.required' => 'Please enter tournament name.',
            ],
        ],

    ],

    'list-user' =>[
        'v1' => [
            'rules' => [
                'type' => 'required|in:Admin,User',
            ],
            'messages' => [
                'type.required' => 'Please enter user type.',
                'type.in' => 'Please enter valid parameter.',

            ],
        ],
    ],

    'lock'=>[
        'v1' => [
            'rules' => [
                'user_id' => 'required',
                'type' => 'required|in:Bet,User',
            ],
            'messages' => [
                'user_id.required' => 'Please enter user id.',
                'type.required' => 'Please enter type.',
                'type.in' => 'Please enter valid parameter.',
            ],
        ],
    ],

    'add-runner' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|max:10',
                'type' => "required|in:Match,OddEven,Fancy,Dabba,Toss",
                /*'name' => 'required',
                'lay2' => 'required',
                'lay2_value' => 'required',
                'lay1' => 'required',
                'lay1_value'=>'required',
                'lay'=>'required',
                'lay_value'=>'required',
                'back'=>'required',
                'back_value'=>'required',
                'back1'=>'required',
                'back1_value'=>'required',
                'back2'=>'required',
                'back2_value'=>'required',
                'delay'=>'required|numeric',
                'min_bet'=>'required|integer',
                'max_bet'=>'required|integer',
                'extra_delay_rate'=>'required|numeric',
                'extra_delay'=>'required|numeric',
                'status'=>'required|in:Ball Running,Suspended,Active'*/

            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'type.required' => 'Please enter type.',
                'type.in' => 'Please enter valid type.',
                /*'name.required' => 'Please enter name.',
                'lay2.required' => 'Please enter lay2.',
                'lay2_value.required' => 'Please enter lay2 value.',
                'lay1.required' => 'Please enter lay1.',
                'lay1_value.required' => 'Please enter lay1 value.',
                'lay.required' => 'Please enter lay.',
                'lay_value.required' => 'Please enter lay value.',
                'back.required' => 'Please enter back.',
                'back_value.required' => 'Please enter back value.',
                'back1.required' => 'Please enter back1.',
                'back1_value.required' => 'Please enter back1 value.',
                'back2.required' => 'Please enter back2.',
                'back2_value.required' => 'Please enter back2 value.',
                'delay.required'=>'Please enter delay.',
                'delay.numeric'=>'Please enter valid delay value.',
                'min_bet.required' => 'Please enter minimum bet value.',
                'min_bet.integer' => 'Please enter valid minimum bet value.',
                'max_bet.required' => 'Please enter maximum bet value.',
                'max_bet.integer' => 'Please enter valid maximum bet value.',
                'extra_delay_rate.required' => 'Please enter extra delay rate.',
                'extra_delay_rate.numeric' => 'Please enter valid extra delay rate value.',
                'extra_delay.required' => 'Please enter extra delay.',
                'extra_delay.numeric' => 'Please enter valid extra delay.',
                'status.required'=> 'Please enter status.',
                'status.in.required'=> 'Please enter valid status.',*/
            ],
        ],
    ],

    'change-password' =>[
        'v1' => [
            'rules' => [
                'password' => 'required',
            ],
            'messages' => [
                'password.required' => 'Password can’t be blank.'

            ],
        ],
    ],

    'add-admin' => [
        'v1' => [
            'rules' => [
               'limit' => 'required',
                'name' => 'required|max:255',
                'user_name' => 'required|unique:users|max:255',
//                'email' => ['required', 'email'],
                'password' => 'required|min:6|regex:/^[a-zA-Z0-9]+$/',
//                'partnership' => 'required',
                //'mobile' => 'required',
                //'city' => 'required|max:255',
                //'limit' => 'required|numeric|between:0,100'
            ],
            'messages' => [
                'limit.required' => 'Please enter limit.',
                'name.required' => 'Please enter name.',
                'user_name.required' => 'Please enter username.',
                'user_name.unique' => 'Username is already registered with us. Try Login.',
//                'email.required' => 'Please enter email address.',
//                'email.email' => 'Please enter valid email address.',
                //'email.unique' => 'Email address is already registered with us. Try Login.',
                'password.required' => 'Please enter password.',
                'password.regex' => 'Special characters are not allowed in password.',
                'password.min' => 'Please enter minimum 6 character alphanumeric password..',
//                'partnership.required' => 'Please enter partnership.',
                //'mobile.required' => 'Please enter mobile no.',
                //'city.required' => 'Please enter city.',
            ],
        ],
    ],

    'edit-admin' => [
        'v1' => [
            'rules' => [
//                'domain' => 'required',
                'name' => 'required|max:255',
                'user_name' => 'required|unique:users|max:255',
//                'email' => ['required', 'email'],
                //'mobile' => 'required',
                //'city' => 'required|max:255',
//                'partnership' => 'required|numeric|between:0,100'
            ],
            'messages' => [
//                'domain.required' => 'Please enter domain.',
                'name.required' => 'Please enter name.',
                'user_name.required' => 'Please enter username.',
                'user_name.unique' => 'Username is already registered with us. Try Login.',
//                'email.required' => 'Please enter email address.',
//                'email.email' => 'Please enter valid email address.',
                //'email.unique' => 'Email address is already registered with us. Try Login.',
                //'mobile.required' => 'Please enter mobile no.',
                //'city.required' => 'Please enter city.',
//                'partnership.required' => 'Please enter partnership.',
            ],
        ],
    ],

    'delete-user' => [
        'v1' => [
            'rules' => [
                'user_id' => 'required'
            ],
            'messages' => [
                'user_id.required' => 'Please enter user id.',
            ],
        ],
    ],
    'delete-rules' => [
        'v1' => [
            'rules' => [
                'rules_id' => 'required'
            ],
            'messages' => [
                'rules_id.required' => 'Please enter rules id.',
            ],
        ],
    ],
    'add-edit-rules'=>[
        'v1' => [
            'rules' => [
                'description' => 'required'
            ],
            'messages' => [
                'description.required' => 'Please enter rules description.',
            ],
        ],
    ],

    'delete-game' => [
        'v1' => [
            'rules' => [
                'id' => 'required'
            ],
            'messages' => [
                'id.required' => 'Please enter game id.',
            ],
        ],
    ],
    'get-tournament-by-sport' => [
        'v1' => [
            'rules' => [
                'sport_id' => 'required'
            ],
            'messages' => [
                'sport_id.required' => 'Please enter sport id.',
            ],
        ],
    ],

    'list-runner-by-game' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|max:10',
                'type' => "required|in:Match,OddEven,Fancy,Dabba,Toss",
                'status'=>'required|in:Ball Running,Suspended,Active'

            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'type.required' => 'Please enter type.',
                'type.in' => 'Please enter valid type.',
                'status.in.required'=> 'Please enter valid status.',
            ],
        ],
    ],
    'delete-bookie' => [
        'v1' => [
            'rules' => [
                'bookie_id' => 'required'
            ],
            'messages' => [
                'bookie_id.required' => 'Please enter bookie id.',
            ],
        ],
    ],
    'bookie-login' => [
        'v1' => [
            'rules' => [
                'user_name' => 'required',
                'password' => 'required'
            ],
            'messages' => [
                'user_name.required' => 'Please enter bookie username.',
                'password.required' => 'Password can’t be blank.'
            ],
        ],
    ],
    'bookie-change-password' =>[
        'v1' => [
            'rules' => [
                'password' => 'required',
            ],
            'messages' => [
                'password.required' => 'Password can’t be blank.'

            ],
        ],
    ],

    'all-runner-list-by-game' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|integer'
            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'game_id.integer' => 'Please enter valid game id.',
            ],
        ],
    ],

    'add-cr-dr' => [
        'v1' => [
            'rules' => [
                'type' => 'required|in:credit,debit,increase_limit,decrease_limit',
                'to_user' => 'required|integer',
                'amount' => 'required|numeric',
                'password' => 'required'
            ],
            'messages' => [
                'type.required' => 'Please select type',
                'type.in' => 'Please select valid type',
                'to_user.required' => 'Please enter to user id.',
                'to_user.integer' => 'Please enter valid to user id.',
                'amount.integer' => 'Please enter amount.',
                'amount.numeric' => 'Please enter valid amount.',
                'password' => 'Please enter password.'
            ],
        ],
    ],


    'get-game' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|integer',
            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'game_id.integer' => 'Please enter valid game id.',
            ],
        ],
    ],

    'game-runners' => [
        'm-panel' => [
            'rules' => [
                'game_id' => 'required|integer',
            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'game_id.integer' => 'Please enter valid game id.',
            ],
        ],
    ],
    'add-leti-deti' => [
        'v1' => [
            'rules' => [
                'from_user' => 'required|integer',
                'to_user' => 'required|integer',
                'amount' => 'required|numeric',
            ],
            'messages' => [
                'from_user.required' => 'Please enter from user id.',
                'from_user.integer' => 'Please enter valid from user id.',
                'to_user.required' => 'Please enter to user id.',
                'to_user.integer' => 'Please enter valid to user id.',
                'amount.required' => 'Please enter amount.',
                'amount.numeric' => 'Please enter valid amount.',
            ],
        ],
    ],

    'edit-profile' => [
        'v1' => [
            'rules' => [
//                'domain' => 'required',
                'name' => 'required|max:255',
                'email' => ['required', 'email'],
                'mobile' => 'required',
                'city' => 'required|max:255',
            ],
            'messages' => [
//                'domain.required' => 'Please enter domain.',
                'name.required' => 'Please enter name.',
                'email.required' => 'Please enter email address.',
                'email.email' => 'Please enter valid email address.',
                'mobile.required' => 'Please enter mobile no.',
                'city.required' => 'Please enter city.',
            ],
        ],
    ],
    'user-set-button' => [
        'v1' => [
            'rules' => [
                'button_value' => 'required',
                'button_name' => 'required'
            ],
            'messages' => [
                'button_value.required' => 'Please enter button value.',
                'button_name.required' => 'Please enter button name.',
            ],
        ],
    ],
    'edit-bookie-profile' => [
        'v1' => [
            'rules' => [
                'user_name' => 'required',
                'name' => 'required|max:255',
                'email' => ['required', 'email'],
                'mobile' => 'required',
                'city' => 'required|max:255',
            ],
            'messages' => [
                'user_name.required' => 'Please enter user name.',
                'name.required' => 'Please enter name.',
                'email.required' => 'Please enter email address.',
                'email.email' => 'Please enter valid email address.',
                'mobile.required' => 'Please enter mobile no.',
                'city.required' => 'Please enter city.',
            ],
        ],
    ],

    'get-account-statement' => [
        'v1' => [
            'rules' => [
                'user_id' => 'required|integer',
                'type' => 'required|in:game,crdr,limit',
                'start_date' => 'date',
                'end_date' => 'date'
            ],
            'messages' => [
                'user_id.required' => 'Please enter user id.',
                'user_id.integer' => 'Please enter valid user id.',
                'type.required' => 'Please enter type.',
                'type.in' => 'Please enter valid type.',
//                'start_date.required' => 'Please enter start date.',
                'start_date.date' => 'Please enter valid start date.',
                'end_date.required' => 'Please ennter end date.',
//                'end_date.date' => 'Please enter valid end date.',
            ],
        ],
    ],
    'get-account-statement-user' => [
        'v1' => [
            'rules' => [
                'type' => 'required|in:game,crdr,limit',
                'start_date' => 'date',
                'end_date' => 'date'
            ],
            'messages' => [
                'type.required' => 'Please enter type.',
                'type.in' => 'Please enter valid type.',
                'start_date.date' => 'Please enter valid start date.',
                'end_date.required' => 'Please ennter end date.',
            ],
        ],
    ],


    'save-bet' => [
        'v1' => [
            'rules' => [
                'runner_id' => 'required|integer',
                'type' => 'required',
                'side_rate' => 'required',
                'side_value' => 'required',
                'bet_amount' => 'required|numeric',
            ],
            'messages' => [
                'runner_id.required' => 'Please enter runner id.',
                'runner_id.integer' => 'Please enter valid runner id.',
                'type.required' => 'Please enter type.',
                'side_rate.required' => 'Please enter rate.',
                'side_value.required' => 'Please enter value.',
                'bet_amount.required' => 'Please enter bet amount.',
                'bet_amount.numeric' => 'Please enter valid bet amount.',
            ],
        ],
    ],
    'game-done' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|integer'
            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'game_id.integer' => 'Please enter valid game id.',
            ],
        ],
    ],
    'delete-bet-fair-account' => [
        'v1' => [
            'rules' => [
                'bet_fair_id' => 'required'
            ],
            'messages' => [
                'bet_fair_id.required' => 'Please enter bet fair account id.',
            ],
        ],
    ],
    'add-bet-fair-account' => [
        'v1' => [
            'rules' => [
                'user_name' => 'required',
                'password' => 'required'
            ],
            'messages' => [
                'user_name.required' => 'Please enter username.',
                'password.required' => 'Password can’t be blank.'
            ],
        ],
    ],

    'delete-lock' => [
        'v1' => [
            'rules' => [
                'id' => 'required|integer',
            ],
            'messages' => [
                'id.required' => 'Please enter lock id.',
                'id.integer' => 'Please enter valid lock id.'
            ],
        ],
    ],

    'bet-place' => [
        'v1' => [
            'rules' => [
                'runner_id' => 'required|exists:runners,id',
                'amount' => 'required|numeric',
                'type' => 'required|in:Lay,Back',
                'rate' => 'required',
                'rate_value' => 'required',
            ],
            'messages' => [
                'runner_id.required' => 'Please enter runner id.',
                'runner_id.exists' => 'Please enter valid runner id.',
                'type.required' => 'Please enter type.',
                'type.in' => 'Please enter valid type.',
                'rate.required' => 'Please enter rate.',
                'rate_value.required' => 'Please enter value.',
                'amount.required' => 'Please enter amount.',
                'amount.numeric' => 'Please enter valid amount.',
            ],
        ],
    ],
    'view-bet-by-runner' => [
        'v1' => [
            'rules' => [
                'id' => 'required|integer',
            ],
            'messages' => [
                'id.required' => 'Please enter runner id.',
                'id.integer' => 'Please enter valid runner id.'
            ],
        ],
    ],
    'in-play-status-change' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|integer',
                'in_play' => 'required|in:True,False'
            ],
            'messages' => [
                'game_id.required' => 'Please enter runner id.',
                'game_id.integer' => 'Please enter valid runner id.',
                'in_play.required' => 'Please enter in play status.',
                'in_play.in' => 'Please enter valid in play status.'
            ],
        ],
    ],
    'game-status-change' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|integer',
                'status' => 'required|in:Active,Inactive'
            ],
            'messages' => [
                'game_id.required' => 'Please enter runner id.',
                'game_id.integer' => 'Please enter valid runner id.',
                'status.required' => 'Please enter status.',
                'status.in' => 'Please enter valid status.'
            ],
        ],
    ],
    'ball-running' => [
        'v1' => [
            'rules' => [
                'sub_game_id' => 'required|integer',
            ],
            'messages' => [
                'sub_game_id.required' => 'Please enter runner id.',
                'sub_game_id.integer' => 'Please enter valid runner id.',
            ],
        ],
    ],
    'get-admin-expose' => [
        'v1' => [
            'rules' => [
                'lay' => 'required',
                'runner_id' => 'required|integer',
            ],
            'messages' => [
                'lay.required' => 'Please enter lay.',
                'runner_id.required' => 'Please enter runner id.',
                'runner_id.integer' => 'Please enter valid runner id.',
            ],
        ],
    ],
    'declare-result-game' => [
        'v1' => [
            'rules' => [
                'id' => 'required|integer',
                'result' => 'required',
            ],
            'messages' => [
                'id.required' => 'Please enter runner id.',
                'id.integer' => 'Please enter valid runner id.',
                'result.required' => 'Please enter result.',
            ],
        ],
    ],
    'admin-change-password' =>[
        'v1' => [
            'rules' => [
                'password' => 'required',
                'id' => 'required',
                'type'=> 'required|in:0,1',
            ],
            'messages' => [
                'password.required' => 'Password can’t be blank.',
                'id.required' => 'Id can’t be blank.',
                'type.required' => 'Type can’t be blank.',
                'type.in' => 'Password can’t be blank.',

            ],
        ],
    ],
    'casino-winner-list' => [
        'v1' => [
            'rules' => [
                'game_id' => 'required|integer'
            ],
            'messages' => [
                'game_id.required' => 'Please enter game id.',
                'game_id.integer' => 'Please enter valid game id.',
            ],
        ],
    ],
];
