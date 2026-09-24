<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // ==========================================================
        // API Authentication Exception
        // ==========================================================
        if (
            $request->is('api/*') &&
            $exception instanceof \Illuminate\Auth\AuthenticationException
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bearer token is expired, invalid or missing.'
            ], 401);
        }

        // ==========================================================
        // API Error Response
        // ==========================================================
        if ($request->is('api/*')) {

            $status = method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500;

            return response()->json([
                'success' => false,
                'error' => [
                    'code'    => $status,
                    'message' => $exception->getMessage(),
                ]
            ], $status);
        }

        // ==========================================================
        // Web Response
        // ==========================================================

        // Show custom popup only in production
        if (app()->environment('production')) {
            return response()->view('admin.exceptionfile');
        }

        // Local / Development
        return parent::render($request, $exception);
    }

    private function jsonResponse(Throwable $exception)
    {
        $status = method_exists($exception, 'getStatusCode')
            ? $exception->getStatusCode()
            : 500;

        return response()->json([
            'error' => [
                'code'    => $status,
                'message' => $exception->getMessage(),
            ],
        ], $status);
    }

    private function htmlResponse(Throwable $exception)
    {
        return response()->view('admin.exceptionfile');
    }
}
