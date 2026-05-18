<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products_count' => DB::table('products')->count(),
            'low_stock_count' => DB::table('products')->where('stock', '<', 10)->count(),
            'movements_in_month' => DB::table('stock_movements')
                ->where('type', 'entrada')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'movements_out_month' => DB::table('stock_movements')
                ->where('type', 'salida')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return response()->json($stats);
    }

    public function health()
    {
        try {
            DB::select('SELECT 1');
            return response()->json(['status' => 'ok', 'database' => 'connected']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'fail', 'error' => $e->getMessage()], 500);
        }
    }
}
