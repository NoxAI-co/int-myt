<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        view()->share(['seccion' => 'Estadisticas', 'subseccion' => 'estadisticas', 'title' => 'Estadisticas', 'icon' =>'fas fa-chart-bar']);
    }
    public function index()
    {
        $this->getAllPermissions(Auth::user()->id);

        // Fechas calculadas
        $currentMonthEnd = Carbon::now()->endOfMonth(); // Fin del mes actual
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth(); // Fin del mes anterior

        // Clientes activos
        $activeClients = $this->calculateStatistics('enabled', null, null, $lastMonthEnd, $currentMonthEnd);

        // Clientes activos con solo internet
        $internetOnlyClients = $this->calculateStatistics('enabled', true, false, $lastMonthEnd, $currentMonthEnd);

        // Clientes activos con solo TV
        $tvOnlyClients = $this->calculateStatistics('enabled', false, true, $lastMonthEnd, $currentMonthEnd);

        // Clientes activos con ambos servicios
        $bothServicesClients = $this->calculateStatistics('enabled', true, true, $lastMonthEnd, $currentMonthEnd);

        $data = $this->getMonthlyStatistics();

        $year = Carbon::now()->year;


        return view('statistics.index', compact(
            'activeClients',
            'internetOnlyClients',
            'tvOnlyClients',
            'bothServicesClients',
            'data',
            'year'
        ));
    }

    private function calculateStatistics($state, $hasInternet, $hasTv, $lastMonthEnd, $currentMonthEnd)
    {
        $query = DB::table('contracts')->where('state', $state);

        if (!is_null($hasInternet)) {
            $query->when($hasInternet, function ($q) {
                return $q->whereNotNull('plan_id');
            }, function ($q) {
                return $q->whereNull('plan_id');
            });
        }

        if (!is_null($hasTv)) {
            $query->when($hasTv, function ($q) {
                return $q->whereNotNull('servicio_tv');
            }, function ($q) {
                return $q->whereNull('servicio_tv');
            });
        }

        // Desde el inicio hasta el mes pasado
        $previousCount = $query->where('created_at', '<=', $lastMonthEnd)->count();

        $query = DB::table('contracts')->where('state', $state);

        if (!is_null($hasInternet)) {
            $query->when($hasInternet, function ($q) {
                return $q->whereNotNull('plan_id');
            }, function ($q) {
                return $q->whereNull('plan_id');
            });
        }

        if (!is_null($hasTv)) {
            $query->when($hasTv, function ($q) {
                return $q->whereNotNull('servicio_tv');
            }, function ($q) {
                return $q->whereNull('servicio_tv');
            });
        }

        // Desde el inicio hasta el mes presente
        $currentCount = $query->where('created_at', '<=', $currentMonthEnd)->count();

        // Calcular porcentaje de cambio
        $percentageChange = $this->calculatePercentageChange($previousCount, $currentCount);

        return [
            'current' => $currentCount,
            'previous' => $previousCount,
            'percentageChange' => $percentageChange,
        ];
    }

    private function calculatePercentageChange($lastMonth, $currentMonth)
    {
        if ($lastMonth === 0) {
            return $currentMonth > 0 ? 100 : 0;
        }
        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    public function getMonthlyStatistics()
    {
        $year = Carbon::now()->year; // Año actual
        $data = [
            'months' => [],
            'activos' => [],
            'con_internet' => [],
            'con_tv' => [],
            'con_combo' => [],
        ];

        for ($month = 1; $month <= Carbon::now()->month; $month++) {
            $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $data['months'][] = $startOfMonth->format('F'); // Nombre del mes

            // Activos
            $data['activos'][] = DB::table('contracts')
                ->where('state', 'enabled')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Con Internet
            $data['con_internet'][] = DB::table('contracts')
                ->where('state', 'enabled')
                ->whereNotNull('plan_id')
                ->whereNull('servicio_tv')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Con TV
            $data['con_tv'][] = DB::table('contracts')
                ->where('state', 'enabled')
                ->whereNull('plan_id')
                ->whereNotNull('servicio_tv')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Con Combo (Internet y TV)
            $data['con_combo'][] = DB::table('contracts')
                ->where('state', 'enabled')
                ->whereNotNull('plan_id')
                ->whereNotNull('servicio_tv')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
        }

        return $data;
    }
}