<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    const FOREIGN_KEY_VIOLATION_CODE = 1451;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if($e instanceof ModelNotFoundException) {
            $modelName = class_basename($e->getModel());
            return $this->errorResponse("$modelName does not exist with the specified key!", 404);
        }

        if($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), 403);
        }

        if($e instanceof NotFoundHttpException) {
            return $this->errorResponse("The specified URL cannot be found", 404);
        }

        if($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('The specified method for the request is invalid', 405);
        }

        if($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        if($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1]; // gives the SQL error code. can find with dd() if u can generate QueryException
            if($errorCode == self::FOREIGN_KEY_VIOLATION_CODE) {
                return $this->errorResponse('Cannot remove this resource permanently, as it is related with any other resource', 409);
            }
        }

        if(config('app.debug')) {
            return parent::render($request, $e);
        }
        return $this->errorResponse('Unexpected Server Error', 500);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->errors();
        if($this->isFrontEnd($request)) {
            if($request->ajax()) {
                return response()->json($errors, 422);
            } else {
                return redirect()->back()->withInput($request->input())->withErrors($errors);
            }
        }

        return $this->errorResponse($e->errors(), 422);

    }

     /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return  JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        if($this->isFrontEnd($request)) {
            return redirect()->guest('login');
        }
        return $this->errorResponse("Unauthenticated!", 401);
    }

    private function isFrontEnd(Request $request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
