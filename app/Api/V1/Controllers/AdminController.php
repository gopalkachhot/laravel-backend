<?php

namespace App\Api\V1\Controllers;

use App\Api\ApiController;
use App\Api\V1\Resources\LetiDeti\LetiCustomPaginationResource;
use App\Api\V1\Resources\LetiDeti\LetiDetiResource;
use App\Api\V1\Resources\User\AdminResource;
use App\Api\V1\Resources\User\GeneralReportResource;
use App\LetiDeti;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends ApiController
{


    public function addEditAdmin(Request $request)
    {
        $user_details = \Auth::user();
        $userId = $user_details->id;
        $from_user = User::whereId($userId)->first();
        $id = $request->get('id');
        if ($id != null) {
            $this->validateRequest('edit-Admin');
            $user_id = $request->get('id');
            $exitUser = User::whereUserName($request->get('user_name'))->whereNotIn('id', [$user_id])->first();
            if ($exitUser) {
                return response([
                    'message' => \Lang::get('api.user_name_exist'),
                    'status_code' => 400
                ]);
            }
            $userProfile = User::findOrFail($request->get('id'));
            DB::beginTransaction();
            try {
                $userProfile->name = $request->get('name', $userProfile->name);
                $userProfile->domain = $request->get('domain', $userProfile->domain);
                $userProfile->user_name = $request->get('user_name', $userProfile->user_name);
                $userProfile->email = $request->get('email', $userProfile->email);
                //$userProfile->password = Hash::make($request->get('password'));
                $userProfile->mobile = ($request->get('mobile')) ? $request->get('mobile') : '';
                $userProfile->city = ($request->get('city')) ? $request->get('city') : '';
                $userProfile->partnership = ($request->get('partnership')) ? $request->get('partnership') : 100;
                $userProfile->save();
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage(), 400);
            }
            return AdminResource::make($userProfile)->additional(['meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.admin_update_success')
            ]]);
        } else {
            $this->validateRequest('add-admin');
            try {
                if (($user_details->limit - $user_details->used_limit) >= $request->get('limit')) {
                    $from_user->used_limit = $from_user->used_limit + $request->get('limit');
                    $from_user->save();
                } else {
                    return response(['message' => \Lang::get('api.insufficient_balance'), 'status_code' => 400], 200);
                }
                if ($user_details->level == 9) {
                    return response(['message' => \Lang::get('api.level_end'), 'status_code' => 400], 200);
                }

                $user = new User();
                $user->user_id = $userId;
                $user->name = $request->get('name');
                $user->domain = $request->get('domain');
                $user->user_name = $request->get('user_name');
                $user->email = $request->get('email');
                $user->password = Hash::make($request->get('password'));
                $user->mobile = ($request->get('mobile')) ? $request->get('mobile') : '';
                $user->city = ($request->get('city')) ? $request->get('city') : '';
                $user->partnership = ($request->get('partnership')) ? $request->get('partnership') : 100;
                $user->level = $user_details->level + 1;
                $user->limit = $request->get('limit');

                $user->used_limit = 0;
                $user->expense = 0;
                $user->is_admin = $request->get('is_admin', 'Yes');
                $user->is_betting_now = $request->get('is_betting_now', 'No');
                $user->save();
                ApiController::saveLetiDeti($from_user->id, $user->id, 'increase_limit', $request->get('limit'), 'First time credit limit');
                DB::commit();
                $token = $this->generateAccessToken($user)->accessToken;
                return AdminResource::make($user)->additional(['meta' => [
                    'token' => $token,
                    'status_code' => 200,
                    'message' => \Lang::get('api.admin_save_success')
                ]]);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage(), 400);
            }
        }
    }

    public function getAdmin(Request $request)
    {
        $requestPara = $request->only('user_id');
        $user_details = \Auth::user();
        $userId = $user_details->id;
        if (isset($requestPara['user_id'])) {
            $userId = $requestPara['user_id'];
        }
        $user = User::whereId($userId)->first();
        if (!$user) {
            return response(['message' => \Lang::get('api.no_admin_found'), 'status_code' => 400], 200);
        }
        return AdminResource::make($user)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.get_admin_details')
        ]]);
    }

    public function deleteAdmin(Request $request)
    {
        $this->validateRequest('delete-user');
        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->get('user_id'))->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage(), 400);
        }
        if ($user) {
            return response(['data' => null, 'meta' => ['message' => \Lang::get('api.admin_delete_success'), 'status_code' => 200]], 200);
        } else {
            return response(['message' => \Lang::get('api.invalid_user_id'), 'status_code' => 400], 400);
        }
    }

    public function getAccountStatement(Request $request)
    {
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $user_details = \Auth::user();
        $user_ids = $request->get('user_id') ? collect($request->get('user_id'))->pluck('id')->toArray() : [];
        $leti_deti = LetiDeti::orderBy('id')
            ->where(function ($query) use ($request, $user_details, $user_ids) {
                $query->whereIn('from_user_id', $user_ids);
                $query->orWhereIn('to_user_id', $user_ids);
            })
            ->where(function ($query) use ($request, $user_details) {
                if ($request->has('start_date') && $request->get('start_date') && $request->has('end_date') && $request->get('end_date')) {
                    $query->whereDate('created_at', '>=', Carbon::parse($request->get('start_date'))->format('Y-m-d H:i:s'));
                    $query->whereDate('created_at', '<=', Carbon::parse($request->get('end_date'))->format('Y-m-d H:i:s'));
                }
            })->where(function ($query) use ($request, $user_details) {
                if ($request->get('type') && $request->get('type') != 'all') {
                    if ($request->get('type') == 'crdr') {
                        $query->where('type', 'CRDR')->whereNull('sub_game_id');
                    }
                    if ($request->get('type') == 'limit') {
                        $query->where('type', 'Limit')->whereNull('sub_game_id');
                    }
                }
            })->paginate($perpage);
        return LetiDetiResource::collection($leti_deti)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.account_statement_found_success')
        ]]);
    }

    public function getGameWiseReport(Request $request)
    {
        $per_page = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $user_details = \Auth::user();
        $user_ids = $request->get('user_id') ? collect($request->get('user_id'))->pluck('id')->toArray() : [];
        $game_report = LetiDeti::orderBy('id')
            ->where(function ($query) use ($request, $user_details, $user_ids) {
                $query->whereIn('from_user_id', $user_ids);
                $query->orWhereIn('to_user_id', $user_ids);
            })->where(function ($q) use ($request, $user_details) {
                if ($request->has('start_date') && $request->get('start_date') && $request->has('end_date') && $request->get('end_date')) {
                    $q->whereDate('created_at', '>=', Carbon::parse($request->get('start_date'))->format('Y-m-d H:i:s'));
                    $q->whereDate('created_at', '<=', Carbon::parse($request->get('end_date'))->format('Y-m-d H:i:s'));
                }
            })->whereType('CRDR')->whereNotNull('sub_game_id')->paginate($per_page);
        return LetiDetiResource::collection($game_report)->additional(['meta' => [
            'status_code' => 200,
            'message' => 'Game wise report found successfully.'
        ]]);
    }

    public function getAllAccountReport(Request $request)
    {
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $user_details = \Auth::user();
        $user_ids = $request->get('user_id') ? collect($request->get('user_id'))->pluck('id')->toArray() : [];
        $all_report = LetiDeti::orderBy('id')
            ->where(function ($query) use ($request, $user_details, $user_ids) {
                $query->whereIn('from_user_id', $user_ids);
                $query->orWhereIn('to_user_id', $user_ids);
            })->where(function ($query) use ($request, $user_details) {
                if ($request->has('start_date') && $request->get('start_date') && $request->has('end_date') && $request->get('end_date')) {
                    $query->whereDate('created_at', '>=', Carbon::parse($request->get('start_date'))->format('Y-m-d H:i:s'));
                    $query->whereDate('created_at', '<=', Carbon::parse($request->get('end_date'))->format('Y-m-d H:i:s'));
                }
            })->paginate($perpage);
        return LetiDetiResource::collection($all_report)->additional(['meta' => [
            'status_code' => 200,
            'message' => 'Report found successfully.'
        ]]);
    }

    public function getGeneralReport(Request $request)
    {
        /** @var User $authUsers */
        $authUsers = \Auth::user();
        $users = array();
        User::whereUserId($authUsers->id)->get()->each(function (User $user) use (&$users){
            if($user->upper_level_expense != 0){
                array_push($users, [
                    'name' => $user->user_name,
                    'amount' => round(abs($user->upper_level_expense)),
                    'side' => $user->upper_level_expense > 0 ? 'right' : 'left'
                ]);
            }
        });

        $users[] = [
            'name' => 'Me',
            'amount' => round(abs($authUsers->expense)),
            'side' => $authUsers->expense < 0 ? 'left' : 'right'
        ];

        if($authUsers->user_id > 0) {
            $users[] = [
                'name' => 'Upper Level',
                'amount' => round(abs($authUsers->upper_level_expense)),
                'side' => $authUsers->upper_level_expense < 0 ? 'right' : 'left'
            ];
        }

        return [
            'data' => $users,
            'meta' => [
                'status_code' => 200,
                'message' => \Lang::get('api.account_statement_found_success')
            ]
        ];
    }
}
