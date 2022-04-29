<?php
namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\BetFairAccount\BetFairAccountResource;
use App\BetfairAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetfairAccountController extends ApiController{

    public function AddBetfairAccount(Request $request)
    {
        $requestPara = $request->only('user_name', 'password');
        $this->validateRequest('add-bet-fair-account');
        $exitData = BetfairAccount::whereUserName($request->get('user_name'))->first();

        if($exitData != null){
            return response(['data' => $exitData, 'meta' => ['message' => \Lang::get('api.users_name_exist'), 'status_code' => 400]], 400);
        }
        $betfairKey = getenv('BET_FAIR_ACCOUNT_KEY');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://identitysso.betfair.com/api/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "username=$request->user_name&password=$request->password",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "X-Application:$betfairKey"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response);

        if($response->status == 'SUCCESS') {
            $betfairaccount = new BetfairAccount();
            $betfairaccount->user_name = $request->user_name;
            $betfairaccount->password = $request->password;
            $betfairaccount->token =$response->token ;
            $betfairaccount->save();
            return response(['data' => $betfairaccount, 'meta' => ['message' => \Lang::get('api.bet_fair_add_success'), 'status_code' => 200]], 200);
        } else {
            return response(['data' => $response, 'meta' => ['message' => \Lang::get('api.something_wrong'), 'status_code' => 400]], 200);
        }
    }

    public function ListBetfairAccount(Request $request){

        $requestPara = $request->only('search');
        $search = isset($requestPara['search']) ? $requestPara['search'] : "";
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;

        $betfairaccount = BetfairAccount::orWhere('user_name', 'like', '%' . $search . '%')->paginate($perpage);

        return BetFairAccountResource::collection($betfairaccount)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.game_list_success')
        ]]);
    }


    public function DeleteBetfairAccount(Request $request){

        $this->validateRequest('delete-bet-fair-account');
        DB::beginTransaction();
        try {
            $betfairaccount = BetfairAccount::whereId($request->get('bet_fair_id'))->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if($betfairaccount){
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.bet_fair_account_delete_success'), 'status_code' => 200]], 200);
        }else{
            return response(['message' => \Lang::get('api.invalid_bet_fair_account_id'), 'status_code' => 400], 400);
        }



    }
}