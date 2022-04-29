<?php

namespace App\Exceptions;

use App\Api\ApiController;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpFoundation\Response;

trait ApiExceptions {

    public function apiException($request, $exception){
        if ($exception instanceof ModelNotFoundException){
            return response()->json([
                'message' => \Lang::get('api.model_not_found'),
                'status_code' => 400
            ],ApiController::$VALIDATION_FAILED_HTTP_CODE);
        }
        if ($exception instanceof NotFoundHttpException){
            return response()->json([
                'message' => \Lang::get('api.url_not_found'),
                'status_code' => 400
            ],ApiController::$VALIDATION_FAILED_HTTP_CODE);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException){
            return response()->json([
                'message' => $exception->validator->errors()->first(),
                'status_code' => 400
            ],ApiController::$VALIDATION_FAILED_HTTP_CODE);
        }

        if ($exception instanceof AuthenticationException){
            return response()->json([
                'message' => \Lang::get('api.unauthorized_user'),
                'status_code' => 401
            ],ApiController::$UNAUTHORIZED_USER);
        }

        if ($exception instanceof MethodNotAllowedHttpException){
            return response()->json([
                'message' => \Lang::get('api.method_not_found'),
                'status_code' => 400
            ],ApiController::$VALIDATION_FAILED_HTTP_CODE);
        }


        return parent::render($request, $exception);

    }

}