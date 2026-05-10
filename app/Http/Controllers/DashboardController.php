<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $totalReports = Report::where('user_id', $user->id)->count();
        $processingReports = Report::where('user_id', $user->id)->where('status', 'diproses')->count();
        $completedReports = Report::where('user_id', $user->id)->where('status', 'selesai')->count();
        $pendingReports = Report::where('user_id', $user->id)->where('status', 'menunggu')->count();
        
        $recentReports = Report::where('user_id', $user->id)
            ->with(['room', 'building'])
            ->latest()
            ->take(3)
            ->get();

        return view('user.dashboard', compact(
            'user', 'totalReports', 'processingReports', 'completedReports', 'pendingReports', 'recentReports'
        ));
    }
}
