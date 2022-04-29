<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public final function error($message, $statusCode = 400, $previous = null)
    {
        $cors = [
            'Access-Control-Allow-Origin' => '*'
        ];
        throw new HttpException($statusCode, $message, $previous, $cors, 0);
    }
}
