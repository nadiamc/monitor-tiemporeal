<?php

namespace App\Http\Controllers;

use App\Services\ServerMetrics;

class ServerController extends Controller
{
    public function index(ServerMetrics $metrics)
    {
        return view('servidor', ['data' => $metrics->snapshot()]);
    }
}
