<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $ok = true;
        $checks = [];

        try {
            DB::select('select 1');
            $checks['database'] = 'ok';
        } catch (\Throwable) {
            $ok = false;
            $checks['database'] = 'error';
        }

        try {
            Storage::disk('local')->exists('.');
            $checks['storage'] = 'ok';
        } catch (\Throwable) {
            $ok = false;
            $checks['storage'] = 'error';
        }

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
        ], $ok ? 200 : 503);
    }
}
