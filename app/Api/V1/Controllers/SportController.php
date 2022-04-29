<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Sport\SportResource;
use App\Sport;
use App\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Hardik
 * Date: 10/07/18
 * Time: 10:00 AM
 */
class SportController extends ApiController
{

    public function listSport(Request $request)
    {
        $sports = Sport::orderByDesc('id')->get();
        return SportResource::collection($sports)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.sport_list_success')
        ]]);
    }

    public function addEditSport(Request $request)
    {
        $id = $request->get('id');
        $this->validateRequest('add-sport');
        $requestPara = $request->only('name');

        DB::beginTransaction();
        try {
            $sport = Sport::findOrNew($id);
            $sport->name = $requestPara['name'];
            $sport->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return SportResource::make($sport)->additional(['meta' => [
            'status_code' => 200,
            'message' => $id ? \Lang::get('api.sport_edit_success') : \Lang::get('api.sport_save_success')
        ]]);
    }

}