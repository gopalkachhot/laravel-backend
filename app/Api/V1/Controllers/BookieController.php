<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\Bookie\BookieGameResource;
use App\Api\V1\Resources\Bookie\BookieListResource;
use App\Api\V1\Resources\Bookie\BookieResource;
use App\Api\V1\Resources\Game\GameResource;
use App\Bookie;
use App\BookieGame;
use App\Game;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BookieController extends ApiController
{
    public function listBookie(Request $request)
    {
        /*$user_details = \Auth::user();
        $userId = $user_details->id;*/
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $bookie_list = Bookie::orderBy('updated_at', 'desc')
            /*->where('created_user_id','=',$userId)*/
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('id', 'like', '%' . \Request::get('search') . '%');
                    $query->orWhere('name', 'like', '%' . \Request::get('search') . '%');
                    $query->orWhere('user_name', 'like', '%' . \Request::get('search') . '%');
                    $query->orWhere('created_at', 'like', '%' . \Request::get('search') . '%');
                    $query->orWhere('updated_at', 'like', '%' . \Request::get('search') . '%');
                }
            })->paginate($perpage);
        return BookieListResource::collection($bookie_list)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.bookie_list_success')
        ]]);
    }

    public function addEditBookie(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $requestPara = $request->only('id', 'name', 'user_name', 'password', 'created_user_id', 'token');
        $id = $request->get('id');
        if ($id != null) {
            $this->validateRequest('edit-Bookie');
            $user_id = $request->get('id');
            $exitUser = Bookie::whereUserName($request->get('user_name'))->whereNotIn('id', [$user_id])->first();
            if ($exitUser) {
                return response([
                    'message' => \Lang::get('api.bookie_user_name_exist'),
                    'status_code' => 400
                ]);
            }
            $bookie = Bookie::findOrFail($request->get('id'));
            try {
                DB::beginTransaction();
                $bookie->name = $request->get('name', null);
                $bookie->user_name = $requestPara['user_name'];
                $bookie->password = $request->has('password') ? Hash::make($requestPara['password']) : $bookie->password;
                $bookie->email = isset($request->email) ? $request->email : '';
                $bookie->created_user_id = $userId;
                $bookie->mobile = isset($request->mobile) ? $request->mobile : '';
                $bookie->city = isset($request->city) ? $request->city : '';
                $bookie->token = '';
                $bookie->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                dd($e);
            }
            return BookieResource::make($bookie)->additional(['meta' => [
                'status_code' => 200,
                'message' => $id ? \Lang::get('api.bookie_update_success') : \Lang::get('api.bookie_save_success')
            ]]);
        } else {
            $this->validateRequest('add-bookie');
            try {
                $bookie = new Bookie();
                $bookie->name = $request->get('name', null);
                $bookie->user_name = $requestPara['user_name'];
                $bookie->password = Hash::make($requestPara['password']);
                $bookie->email = isset($request->email) ? $request->email : '';
                $bookie->created_user_id = $userId;
                $bookie->mobile = isset($request->mobile) ? $request->mobile : '';
                $bookie->city = isset($request->city) ? $request->city : '';
                $bookie->token = '';
                $bookie->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
            return BookieResource::make($bookie)->additional(['meta' => [
                'status_code' => 200,
                'message' => $id ? \Lang::get('api.bookie_update_success') : \Lang::get('api.bookie_save_success')
            ]]);
        }
    }

    public function getBookie(Request $request)
    {
        $requestPara = $request->only('bookie_id');
        $bookieId = $requestPara['bookie_id'];
        $bookie = Bookie::whereId($bookieId)->first();
        if (!$bookie) {
            return response(['message' => \Lang::get('api.no_bookie_found'), 'status_code' => 400], 200);
        }
        return BookieResource::make($bookie)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_bookie_details')
        ]]);
    }

    public function deleteBookie(Request $request)
    {
        $this->validateRequest('delete-bookie');
        DB::beginTransaction();
        try {
            $user = Bookie::findOrFail($request->get('bookie_id'))->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if ($user) {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.bookie_delete_success'), 'status_code' => 200]], 200);
        } else {
            return response(['message' => \Lang::get('api.invalid_bookie_id'), 'status_code' => 400], 400);
        }
    }

    public function getBookieGame(Request $request)
    {
        $bookieGame = BookieGame::whereBookieId($request->bookie_id)->with('game')->get();
        if (!$bookieGame) {
            return response(['message' => \Lang::get('api.no_bookie_found'), 'status_code' => 400], 200);
        }
        return BookieGameResource::collection($bookieGame)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_bookie_details')
        ]]);

    }

    public function bookieGameList()
    {
        $bookieGame = BookieGame::all();
        $gameID = array();
        foreach ($bookieGame as $game) {
            $gameID[] = $game->game_id;
        }
        $game = Game::whereWinnerRunnerId(null)->whereNotIn('id', $gameID)->get();
        return GameResource::collection($game)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.bookie_game_list')
        ]]);
    }

    public function addBookieGame(Request $request)
    {
        try {
            collect($request->get('data', []))->each(function ($value) {
                $bookieGame = new BookieGame();
                $bookieGame->bookie_id = $value['bookie_id'];
                $bookieGame->game_id = $value['game_id'];
                $bookieGame->save();
            });
            \DB::commit();
            return [
                'data' => [],
                'meta' => [
                    'status_code' => 200,
                    'message' => \Lang::get('api.add_bookie_game')
                ]
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            return $this->error($e->getMessage(), 400);
        }
    }

    /*Change password*/
    public function subAdminChangePassword(Request $request)
    {
        $this->validateRequest('admin-change-password');
        DB::beginTransaction();
        try {
            if ($request->get('type') == 0) {
                $bookie = Bookie::whereId($request->get('id'))->first();
                if ($bookie) {
                    $bookie->password = \Hash::make($request->get('password'));
                    $bookie->save();
                    $data = ['type' => 'logout', 'data' => 'Yes', 'message' => 'Your password has been changed by admin.'];
                    \App\PhpMqtt::publish('bookie/' . $bookie->id, json_encode($data));
                } else {
                    return response(['message' => \Lang::get('api.invalid_bookie_id'), 'status_code' => 400], 400);
                }
            } else if ($request->get('type') == 1) {
                $admin = User::whereId($request->get('id'))->first();
                if ($admin) {
                    $admin->password = \Hash::make($request->get('password'));
                    $admin->save();
                    $data = [
                        'type' => 'change_password',
                        'data' => 'Yes',
                        'message' => 'Your password has been chnaged by admin.'
                    ];
                    \App\PhpMqtt::publish('user/' . $admin->id, json_encode($data));
                } else {
                    return response(['message' => \Lang::get('api.invalid_sub_admin_id'), 'status_code' => 400], 400);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        return (['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.change_password')
        ]]);

    }

}
