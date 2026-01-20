<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('sendResponse')) {
    /**
     * Return a standardized successful JSON response.
     *
     * @param array $data   The response data to include (e.g ['message' => $message, 'user' => $user]).
     * @param int $code     The HTTP status code (default is 200).
     * @return JsonResponse A JSON response with a "success" flag.
     */
    function sendResponse($data, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            ...$data
        ];

        return response()->json($response, $code);
    }
}


if (!function_exists('sendError')) {
    /**
     * Return a standardized error JSON response.
     *
     * @param array $errors The error data to include (e.g ['error' => 'Invalid credentials']).
     * @param int $code     The HTTP status code (default is 400).
     * @return JsonResponse A JSON response with a "success" flag set to false.
     */
    function sendError($errors, $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            ...$errors
        ];

        return response()->json($response, $code);
    }
}