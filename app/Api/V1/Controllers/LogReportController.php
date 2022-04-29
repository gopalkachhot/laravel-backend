<?php



namespace App\Api\V1\Controllers;

use App\Api\ApiController;


use App\Api\V1\Resources\LogReport\LogReportResource;
use App\LogReport;
use Carbon\Carbon;
use Illuminate\Http\Request;


class LogReportController extends ApiController{

    public function listLogReports(Request $request)
    {
        $perpage = $request->has('per_page') ? $request->get('per_page') : ApiController::$DEFAULT_PER_PAGE;
        $logReport = LogReport::orderByDesc('id')->WhereDate('created_at', \Request::get('created_at'))
            ->where(function ($query) {
                if (\Request::get('search', null)) {
                    $query->orWhere('ip_address', \Request::get('search'));
                    $query->orWhere('detail',  'like', '%' . \Request::get('search') . '%');

                    $query->OrWhereHas('user', function ($q) {
                        $q->where('user_name', 'like', '%' . \Request::get('search') . '%');
                    });
                }
            });
        $logReport = $logReport->paginate($perpage);
        return LogReportResource::collection($logReport)->additional(['meta' => [
            'status_code' => 200,
            'message' => \Lang::get('api.logreport_list_success')
        ]]);
    }
}
