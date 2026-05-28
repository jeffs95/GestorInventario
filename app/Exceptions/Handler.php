<?php

namespace App\Exceptions;

use App\Models\Bitacora;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {});
    }

    public function render($request, Throwable $e)
    {
        // Dejar que Laravel maneje estas excepciones sin interceptar
        if (
            $e instanceof ValidationException      ||
            $e instanceof AuthenticationException  ||
            $e instanceof HttpException            ||
            $e instanceof HttpResponseException
        ) {
            return parent::render($request, $e);
        }

        // Registrar en bitácora
        Bitacora::registrar(
            tipo: 'error',
            mensaje: get_class($e) . ': ' . $e->getMessage(),
            stackTrace: $e->getTraceAsString()
        );

        // API → JSON con mensaje genérico
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Ocurrió un error. Por favor comuníquese con el administrador.',
            ], 500);
        }

        // Web → redirigir de vuelta con toast de error
        return redirect()->back()
            ->with('error', 'Ocurrió un error. Por favor comuníquese con el administrador.');
    }
}
