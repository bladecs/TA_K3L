<?php

namespace App\Http\Controllers\Satgas;

use App\Http\Controllers\Controller;
use App\Support\Dashboard\SatgasDashboardData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SatgasDashboardData $dashboardData): View
    {
        $dashboard = $dashboardData->build((string) $request->string('period', '180'));

        return view('satgas.dashboard', $dashboard);
    }
}
