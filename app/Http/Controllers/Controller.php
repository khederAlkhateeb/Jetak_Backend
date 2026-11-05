<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Unified success response for all APIs.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     */
    protected function successResponse($data = null, string $message = 'Operation successful', int $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
