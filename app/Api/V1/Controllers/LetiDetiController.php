<?php

namespace App\Api\V1\Controllers;


use App\Api\ApiController;
use App\Api\V1\Resources\LetiDeti\LetiDetiResource;
use App\Api\V1\Resources\User\UserResource;
use App\LetiDeti;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LetiDetiController extends ApiController
{
//    public function __construct()
//    {
//        $this->middleware('auth:api');
//    }

    public function letiDeti(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $requestPara = $request->only('search');
        $search = isset($requestPara['search']) ? $requestPara['search'] : "";
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $user = User::whereUserId($userId)->get();
        $user = collect($user);
        $u_arr = $user->pluck('id')->toArray();
        array_push($u_arr, $userId);
        $leti_deti = LetiDeti::orderBy('updated_at', 'desc')
            ->where(function ($query) use ($userId, $u_arr) {
                $query->whereIn('from_user_id', $u_arr);
                $query->orWhereIn('to_user_id', $u_arr);
            })
            ->with('fromUser')
            ->with('toUser')
            ->with('subGame')
            ->where(function ($query) use ($search) {
                $query->orWhereHas('fromUser', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
                $query->orWhereHas('toUser', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
                $query->orWhereHas('subGame', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
                $query->orWhere('amount', 'like', '%' . $search . '%');
                $query->orWhere('type', 'like', '%' . $search . '%');
            })->paginate($perpage);

        return LetiDetiResource::collection($leti_deti)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.leti_deti_list_success')
        ]]);


    }

    public function deleteLetiDeti(Request $request)
    {
//        $this->validateRequest('delete-user');
        \DB::beginTransaction();
        try {
            $letiDeti = LetiDeti::findOrFail($request->get('letideti_id'))->delete();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if ($letiDeti) {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.leti_deti_delete_success'), 'status_code' => 200]], 200);
        } else {
            return response(['message' => \Lang::get('api.invalid_leti_deti_id'), 'status_code' => 400], 400);
        }
    }

    public function addCrDr(Request $request)
    {
        $this->validateRequest('add-cr-dr');
        $requestPara = $request->only('type', 'to_user', 'amount', 'remark');
        $user = \Auth::user();
        $userData = User::whereId($user->id)->first();
        if (!Hash::check($request->get('password'), $userData->password)){
            return response(['message' => 'Incorrect Password.', 'status_code' => 400], 200);
        }
        $from_user = User::whereId($user->id)->first();
        $to_user = User::whereId($request->get('to_user'))->first();
            if ($request->get('type') == 'credit') {
                if (($from_user->limit - $from_user->expense - $from_user->expose) >= $request->get('amount')) {
                    $to_user->expense -= $request->get('amount');
                    $to_user->upper_level_expense -= $request->get('amount');
                    $to_user->save();
                    $from_user->expense += $request->get('amount');
                    $from_user->save();
                    ApiController::saveLetiDeti($from_user->id, $to_user->id, $request->get('type'), $request->get('amount'), $request->get('remark'));
                    return response(['data' => null, 'meta' => ['message' => \Lang::get('api.cr_dr_add_success'), 'status_code' => 200]], 200);
                } else {
                    return response(['message' => \Lang::get('api.insufficient_balance'), 'status_code' => 400], 200);
                }
            } elseif ($request->get('type') == 'debit') {
                if (($to_user->limit - $to_user->expense) >= $request->get('amount')) {
                    $to_user->expense += $request->get('amount');
                    $to_user->upper_level_expense += $request->get('amount');
                    $to_user->save();
                    $from_user->expense -= $request->get('amount');
                    $from_user->save();
                    ApiController::saveLetiDeti($from_user->id, $to_user->id, $request->get('type'), $request->get('amount'), $request->get('remark'));
                    return response(['data' => null, 'meta' => ['message' => \Lang::get('api.cr_dr_add_success'), 'status_code' => 200]], 200);
                } else {
                    return response(['message' => \Lang::get('api.insufficient_balance'), 'status_code' => 400], 200);
                }
            } elseif ($request->get('type') == 'increase_limit') {
                if (($from_user->limit - $from_user->used_limit) >= $request->get('amount')) {
                    $from_user->used_limit = $from_user->used_limit + $request->get('amount');
                    $from_user->save();
                    //$to_user->upper_level_expense -= $request->get('amount');
                    $to_user->limit = $to_user->limit + $request->get('amount');
                    //$to_user->balance = $to_user->balance + $request->get('amount');
                    $to_user->save();
                    ApiController::saveLetiDeti($from_user->id, $to_user->id, $request->get('type'), $request->get('amount'), $request->get('remark'));
                    return response(['data' => null, 'meta' => ['message' => \Lang::get('api.limit_add_success'), 'status_code' => 200]], 200);
                } else {
                    return response(['message' => \Lang::get('api.insufficient_balance'), 'status_code' => 400], 200);
                }
            } elseif ($request->get('type') == 'decrease_limit') {
                if (($to_user->limit - $to_user->used_limit) >= $request->get('amount')) {
                    $to_user->limit = $to_user->limit - $request->get('amount');
                    //$to_user->upper_level_expense += $request->get('amount');
                    //$to_user->balance = $to_user->balance - $request->get('amount');
                    $to_user->save();
                    $from_user->used_limit = $from_user->used_limit - $request->get('amount');
                    $from_user->save();
                    ApiController::saveLetiDeti($from_user->id, $to_user->id, $request->get('type'), $request->get('amount'), $request->get('remark'));
                    return response(['data' => null, 'meta' => ['message' => \Lang::get('api.limit_add_success'), 'status_code' => 200]], 200);
                } else {
                    return response(['message' => \Lang::get('api.insufficient_balance'), 'status_code' => 400], 200);
                }
            }
    }

    public function getAllUserByAdmin(Request $request)
    {
        $user_details = \Auth::user();
        $users = User::whereUserId($user_details->id)->get();
        if ($users) {
            return UserResource::collection($users)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.users_list_success')
            ]]);
        } else {
            return response(['message' => \Lang::get('api.no_user_found'), 'status_code' => 400], 400);
        }

    }
}
