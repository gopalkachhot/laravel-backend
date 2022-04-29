<?php

use Illuminate\Http\Request;


Route::group(['prefix' => 'v1', 'namespace' => 'App\Api\V1\Controllers'], function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/get-game-by-tournament','BetfairController@getGamesByTournament');
    Route::post('/get-market-id-gamedata','BetfairController@getMarketIdGameData');
    Route::post('/m-login', 'AuthController@mPanelLogin');
//    Route::post('/testfuncron','BetfairController@testfuncron');
    Route::get('/get-market-id','BetfairController@getMarketIdGameData');
    /*Route::post('/unmatch-to-match','BettingController@unmatchToMatch');
    Route::post('/getTournament','BetfairController@getTournament');
    Route::post('/getMarketBhav','BetfairController@getMarketBhav');*/
//    Route::get('/testfuncron','BetfairController@testfuncron');
    /*Route::post('/test-mqtt','UserController@testMqtt');*/

    Route::group(['middleware' => ['auth:api', 'admin']], function () {
        Route::post('/change-password', 'UserController@changePassword');
        Route::post('/get-user', 'UserController@getUser');
        Route::post('/get-users-sub-user','UserController@getUsersSubUser');
        Route::post('/users', 'UserController@listUser');
        Route::post('/get-admin', 'AdminController@getAdmin');
        Route::post('/admin', 'AdminController@addEditAdmin');
        Route::post('/delete-admin', 'AdminController@deleteAdmin');
        Route::post('/leti-deti','LetiDetiController@letiDeti');
        Route::post('/delete-leti-deti','LetiDetiController@deleteLetiDeti');
        Route::post('/sports', 'SportController@listSport');
        Route::post('/get-tournament-by-sport','GameController@getTournamentBySportId');
        Route::post('/get-tournaments-by-sport-ids', 'TournamentController@getTournamentsBySportIds');
        Route::post('/games', 'GameController@listGame');
        Route::post('/get-game', 'GameController@getGame');
        Route::post('/game', 'GameController@addEditGame');
        Route::post('/delete-game', 'GameController@deleteGame');
        Route::post('/get-games-by-tournaments','GameController@getGamesByTournaments');
        Route::post('/view-all-bet-by-runner','SubAdminBookieController@viewBetByRunnerId');
        Route::post('/get-sub-games-by-games','SubGameController@getSubGamesByGames');
        Route::post('/change-in-play-status','GameController@inPlayStatusChange');
        Route::post('/change-game-status','GameController@gameStatusChange');
        Route::post('/list-rules','RulesController@listRules');
        Route::post('/delete-rules','RulesController@deleteRules');
        Route::post('/get-rules', 'RulesController@getRules');
        Route::post('/rules', 'RulesController@addEditRules');
        Route::post('/runner','RunnerController@addRunner');
        Route::post('/get-all-runner-by-game-id','GameController@getAllRunerByGameId');
        Route::post('/edit-runner','RunnerController@editRunner');
        Route::post('/bookies', 'BookieController@listBookie');
        Route::post('/bookie','BookieController@addEditBookie');
        Route::post('/delete-bookie', 'BookieController@deleteBookie');
        Route::post('/get-bookie', 'BookieController@getBookie');
        Route::post('/bookie-game','BookieController@getBookieGame');
        Route::post('/bookie-game-list','BookieController@bookieGameList');
        Route::post('/add-bookie-game','BookieController@addBookieGame');
        Route::post('/log-reports','LogReportController@listLogReports');
        Route::post('/add-cr-dr','LetiDetiController@addCrDr');
        Route::post('/bettings','BettingController@listBettings');
        Route::post('/get-all-user-by-admin','LetiDetiController@getAllUserByAdmin');

        Route::post('/get-account-statement', 'AdminController@getAccountStatement');
        Route::post('/get-game-report', 'AdminController@getGameWiseReport');
        Route::post('/get-all-account-report', 'AdminController@getAllAccountReport');

        Route::post('/lock','LockingController@lock');
        Route::post('/lock-list-by-admin','LockingController@lockingListByAdmin');
        Route::post('/delete-lock','LockingController@deleteLock');
        Route::post('/list-betfair-account','BetfairAccountController@ListBetfairAccount');
        Route::post('/delete-betfair-account','BetfairAccountController@DeleteBetfairAccount');
        Route::post('/add-betfair-account','BetfairAccountController@AddBetfairAccount');
        Route::post('/change-subgame-status','SubGameController@changeSubgameStatus');
        Route::post('/update-get-data-from-betfair','SubGameController@updateGetDataFromBetfair');
        Route::post('/get-admin-expose','RunnerController@getAdminExpose');
        Route::post('/admin-get-match-runner','GameDoneController@getRunner');
        Route::post('/declare-result-for-game','GameDoneController@declareResultForGame');
        Route::post('/general-report', 'AdminController@getGeneralReport');
        Route::post('/sub-admin-change-password', 'BookieController@subAdminChangePassword');

        Route::post('/get-all-matched-bet','BettingController@getAllMatchBet');
        Route::post('/get-all-unmatched-bet','BettingController@getAllUnmatchBet');

        Route::post('/game-runners', 'MPanelGameController@gameRunners');

        Route::post('/get-books-data', 'MPanelGameController@booksData');
        Route::post('/get-columns', 'MPanelGameController@getColumns');
        Route::post('/get-expose','RunnerController@getExpose');
        Route::post('/get-parent','MPanelGameController@getParent');

        Route::post('/get-account-statement-bets','BettingController@getAccountStatementBets');

        Route::post('/get-recall-list','GameController@recallGameList');
      /*Route::post('/user', 'UserController@addEditUser');
        Route::post('/list-user-by-admin', 'UserController@listUserByAdmin');
        Route::get('/get-all-users', 'UserController@getAllUser');
        Route::post('/edit-profile', 'UserController@editProfile');
        Route::post('/sport', 'SportController@addEditSport');
        Route::post('/tournaments', 'TournamentController@listTournament');
        Route::post('/tournament', 'TournamentController@addEditTournament');
        Route::post('/get-tournament', 'TournamentController@getTournament');
        Route::post('/get-game-data', 'GameController@getGameData');
        Route::post('/check-user-lock','LockingController@checkUserLock');
        Route::post('/add-leti-deti','LetiDetiController@addLetiDeti');
        Route::post('/get-runner-by-game-id','RunnerController@getRunnerByGameId');
        Route::post('/all-bet','BettingController@getAllBet');
        Route::post('/all-match-bet','BettingController@getAllMatchBet');
        Route::post('/all-unmatch-bet','BettingController@getAllUnmatchBet');
        Route::post('/get-runner-by-game-id','GameDoneController@getRunners');
        Route::post('/get-runner','GameDoneController@getRunner');
        Route::post('/game-done','GameDoneController@gameDone');*/
    });

    /*
     * For Sub Admin
     * */
    Route::post('/bookie-login', 'SubAdminBookieController@bookieLogin');
    Route::group(['middleware' => ['auth:sub-admin']], function () {
        Route::post('/sub-admin-bookie-change-password', 'SubAdminBookieController@bookieChangePassword');
        Route::post('/sub-admin-bookies-game-list','SubAdminBookieController@bookieGameList');
        Route::post('/sub-admin-get-all-runner-by-game-id-bookie','SubAdminBookieController@getAllRunerByGameId');
        Route::post('/sub-admin-edit-runner','RunnerController@editRunner');
        Route::post('/sub-admin-get-bookie', 'SubAdminBookieController@getBookie');
        Route::post('/sub-admin-edit-profile','SubAdminBookieController@editProfile');
        Route::post('/declare-result-for-game','GameDoneController@declareResultForGame');
        Route::post('/sub-admin-get-runner','GameDoneController@getRunner');
        Route::post('/sub-admin-bookie-games', 'GameController@getGames');
        Route::post('/sub-admin-add-runner','RunnerController@addRunner');
        Route::post('/sub-admin-view-bet-by-runner','SubAdminBookieController@viewBetByRunnerId');
        Route::post('/sub-admin-change-runner-status','SubGameController@changeSubgameStatus');
        Route::post('/change-subgame-status','SubGameController@changeSubgameStatus');
        Route::post('/get-subgame-data','SubGameController@getSubgameData');
        Route::post('/start-casino-game', 'RunnerController@startCasinoGame');
        Route::post('/change-rate-casino-game', 'RunnerController@changeRateCasinoGame');

        /*Route::post('/sub-admin-edit-runner-bookie','SubAdminBookieController@editRunnerBookie');
        Route::post('/sub-admin-game-done','GameController@gameDone');
        Route::post('/sub-admin-games', 'GameController@listGame');*/
    });


    /*
     * Mpanel
     * */
    Route::group(['middleware' => ['auth:api','checkLock'], 'prefix' => 'm-panel'], function () {
        Route::post('/m-panel-get-user', 'UserController@getUser');
        Route::post('/m-panel-get-user-data', 'UserController@getUserData')->middleware('isAdmin');
        Route::post('/m-panel-edit-profile', 'UserController@editProfile');
        Route::post('/m-panel-game-list', 'MPanelGameController@listGameData');
        Route::post('/m-panel-game-runners', 'MPanelGameController@gameRunners');
        Route::post('/m-panel-user-set-button', 'MPanelGameController@userSetButton');
        Route::post('/m-panel-get-user-set-button', 'MPanelGameController@getSetButton');
        Route::post('/m-panel-list-user-by-admin', 'UserController@listUserByAdmin')->middleware('isAdmin');
        Route::post('/m-panel-user', 'UserController@addEditUser')->middleware('isAdmin');
        Route::post('/m-panel-delete-admin', 'AdminController@deleteAdmin')->middleware('isAdmin');
        Route::post('/m-panel-add-cr-dr','LetiDetiController@addCrDr')->middleware('isAdmin');
        Route::post('/m-panel-get-all-user-by-admin','LetiDetiController@getAllUserByAdmin');
        Route::post('/m-panel-sports', 'SportController@listSport');
        Route::post('/m-panel-get-tournaments-by-sport-ids', 'TournamentController@getTournamentsBySportIds');
        Route::post('/m-panel-get-games-by-tournaments','GameController@getGamesByTournaments');
        Route::post('/m-panel-get-sub-games-by-games','SubGameController@getSubGamesByGames');
        Route::post('/m-panel-lock','LockingController@lock')->middleware('isAdmin');
        Route::post('/m-panel-lock-list-by-admin','LockingController@lockingListByAdmin')->middleware('isAdmin');
        Route::post('/m-panel-delete-lock','LockingController@deleteLock')->middleware('isAdmin');

        Route::post('/m-panel-get-account-statement', 'AdminController@getAccountStatement');
        Route::post('/m-panel-get-game-report', 'AdminController@getGameWiseReport');
        Route::post('/m-panel-get-all-account-report', 'AdminController@getAllAccountReport');

        Route::post('/m-panel-change-password', 'UserController@changePassword');
        Route::post('/place-bet', 'BettingController@betPlace');
        Route::post('/m-panel-all-bet','BettingController@getAllBet');
        Route::post('/m-panel-all-match-bet','BettingController@getAllMatchBet');
        Route::post('/m-panel-get-casino-winner-list','MPanelGameController@getCasinoWinnerList');
        Route::post('/m-panel-all-unmatch-bet','BettingController@getAllUnmatchBet');
        Route::post('/m-panel-get-game-data', 'GameController@getGameData');
        Route::post('/search','UserController@search');
        Route::post('/m-panel-general-report', 'AdminController@getGeneralReport');
        Route::post('/m-panel-books-data', 'MPanelGameController@booksData');
        Route::post('/m-panel-columns', 'MPanelGameController@getColumns');
        Route::post('/m-panel-get-expose','RunnerController@getExpose');
        Route::post('/get-account-statement-bets','BettingController@getAccountStatementBets');
        Route::post('/user-admin-change-password','BookieController@subAdminChangePassword')->middleware('isAdmin');
        Route::post('/get-parent','MPanelGameController@getParent');

        Route::get('/d-panel-game-list', 'DPanelController@getInPlayGameList');
        Route::get('/d-panel-active-game-list', 'DPanelController@getActiveGameList');
        Route::post('/d-panel-game-runners', 'MPanelGameController@gameRunners');
        Route::post('/d-panel-all-bet','BettingController@getAllBet');
        Route::post('/d-panel-get-account-statement', 'AdminController@getAccountStatement');

        Route::post('/m-panel-delete-unmatched-bet','BettingController@deleteUnMatchedBet');

        Route::get('/get-all-rules', 'RulesController@getAllRules');
    });
});



