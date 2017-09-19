<?php

namespace App\Http\Controllers;

// Illuminate
use Illuminate\Http\Request;
use Log;

class PreflightController extends Controller
{
    public function options(Request $request) {
        $methods = [
            'get',
            'post',
            'put',
            'delete',
            'options'
        ];

        $headers = [
            'Allows' => strtoupper(implode(' ', $methods)),
        ];

        // Log::info($request->path());

        return response([], 200, $headers);
    }
}
