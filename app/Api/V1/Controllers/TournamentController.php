<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Tournament\TournamentResource;
use App\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class TournamentController extends ApiController
{

    public function listTournament(Request $request)
    {
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $search = $request->get('search');
        $sport_id = $request->get('sport_id');
        $tournaments = Tournament::orderByDesc('id')
            ->whereHas('sport', function ($query) use ($search){
                if ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                }
            });
        $tournaments = $tournaments->orWhere(function ($query) use($search,$sport_id){
            if ($search) {
                $query->where('tournaments.name', 'like', '%' . $search . '%');
            }
            if ($sport_id) {
                $query->where('tournaments.sport_id',$sport_id);
            }
        });
        $tournaments = $tournaments->paginate($perpage);

        return TournamentResource::collection($tournaments)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.tournament_list_success')
        ]]);
    }
    public function addEditTournament(Request $request){
        $this->validateRequest('add-edit-tournament');
        $requestPara = $request->only('tournament_id','name','sport_id');
        $id = isset($requestPara['tournament_id']) && $requestPara['tournament_id']!='' && $requestPara['tournament_id'] != null ? $requestPara['tournament_id'] : null;
        DB::beginTransaction();
        try {
            $tournament = Tournament::findOrNew($id);
            $tournament->name = $request->get('name',null);
            $tournament->sport_id = $requestPara['sport_id'];
            $tournament->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return TournamentResource::make($tournament)->additional(['meta' => [
            'status_code' => 200,
            'message' => $id == '' ?  \Lang::get('api.tournament_save_success') : \Lang::get('api.tournament_edit_success')
        ]]);
    }

    public function getTournamentsBySportIds(Request $request){
        $sport_ids = $request->id;
        $tournament = Tournament::whereIn('sport_id',$sport_ids)->orderByDesc('created_at')->get();
        return TournamentResource::collection($tournament)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.tournament_list_success')
        ]]);
    }

}