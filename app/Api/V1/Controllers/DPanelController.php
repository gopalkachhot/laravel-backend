<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Game\DPanelActiveGameListResource;
use App\Api\V1\Resources\Game\DPanelGameListResource;
use App\Game;
use App\Sport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DPanelController extends ApiController
{
    public function getInPlayGameList(Request $request){
        $sport = Sport::with('tournaments')->get();
        return DPanelGameListResource::collection($sport)->additional(['meta' => [
            'status_code' => 200,
            'message' => 'success'
        ]]);
    }

    public function getActiveGameList(Request $request){
        $games = Game::whereStatus('Active')->get();
        return DPanelActiveGameListResource::collection($games)->additional(['meta' => [
            'status_code' => 200,
            'message' => 'success'
        ]]);
    }
}
