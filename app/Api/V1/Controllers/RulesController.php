<?php



namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Rules\RulesResource;
use App\Rules;
use Illuminate\Http\Request;


class RulesController extends ApiController{

    public function listRules(Request $request){
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $rules = Rules::orderByDesc('id')
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('description', 'like', '%' . \Request::get('search') . '%');
                }
            });
        $rules = $rules->paginate($perpage);
        return RulesResource::collection($rules)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.rules_list_success')
        ]]);

    }
    public function addEditRules(Request $request){

        $this->validateRequest('add-edit-rules');
        $requestPara = $request->only('rules_id','description');
        $id = isset($requestPara['rules_id']) && $requestPara['rules_id']!='' && $requestPara['rules_id'] != null ? $requestPara['rules_id'] : null;

        \DB::beginTransaction();
        try {
            $rules = Rules::findOrNew($id);
            $rules->description = $request->get('description',null);
            $rules->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return RulesResource::make($rules)->additional(['meta' => [
            'status_code' => 200,
            'message' => $id == '' ?  \Lang::get('api.rules_save_success') : \Lang::get('api.rules_edit_success')
        ]]);
    }

    public function deleteRules(Request $request)
    {
        $this->validateRequest('delete-rules');
        \DB::beginTransaction();
        try {
            $rules = Rules::findOrFail($request->get('rules_id'))->delete();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if($rules){
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.rules_delete_success'), 'status_code' => 200]], 200);
        }else{
            return response(['message' => \Lang::get('api.invalid_rules_id'), 'status_code' => 400], 400);
        }
    }
    public function getRules(Request $request){

        $requestPara = $request->only('rules_id');

        $rules = Rules::whereId($requestPara['rules_id'])->first();
        if (!$rules) {
            return response(['message' => \Lang::get('api.no_rules_found'), 'status_code' => 400], 200);
        }
        return RulesResource::make($rules)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_rules_details')
        ]]);
    }

    public function getAllRules(Request $request){
        $rules = Rules::all();
        return RulesResource::collection($rules)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.rules_list_success')
        ]]);
    }
}
