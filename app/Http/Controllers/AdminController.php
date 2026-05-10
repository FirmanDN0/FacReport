<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\Technician;
use App\Models\Building;
use App\Models\User;
use App\Notifications\ReportNotification;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Stats
        $totalReports = Report::count();
        $processingReports = Report::where('status', 'diproses')->count();
        $completedReports = Report::where('status', 'selesai')->count();
        $pendingReports = Report::where('status', 'menunggu')->count();

        // Reports with filter
        $query = Report::with(['room', 'building', 'user', 'technician']);

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('report_code', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(10);

        // Reports per building chart
        $buildings = Building::withCount('reports')->get();

        // Severity distribution
        $severityData = [
            'ringan' => Report::where('severity', 'ringan')->count(),
            'sedang' => Report::where('severity', 'sedang')->count(),
            'berat' => Report::where('severity', 'berat')->count(),
        ];

        // Recent activity
        $recentLogs = ReportLog::with('report')
            ->latest()
            ->take(5)
            ->get();

        // Active technicians
        $technicians = Technician::all();

        // Weekly new reports count
        $weeklyNewReports = Report::where('created_at', '>=', now()->subWeek())->count();
        $recentCompletedDays = Report::where('status', 'selesai')
            ->where('updated_at', '>=', now()->subDays(2))
            ->count();

        return view('admin.dashboard', compact(
            'totalReports', 'processingReports', 'completedReports', 'pendingReports',
            'reports', 'buildings', 'severityData', 'recentLogs', 'technicians',
            'weeklyNewReports', 'recentCompletedDays'
        ));
    }

    public function reports(Request $request)
    {
        $query = Report::with(['room', 'building', 'user', 'technician']);

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('report_code', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(15);
        
        $totalReports = Report::count();
        $processingReports = Report::where('status', 'diproses')->count();
        $completedReports = Report::where('status', 'selesai')->count();
        $pendingReports = Report::where('status', 'menunggu')->count();

        return view('admin.reports', compact(
            'reports', 'totalReports', 'processingReports', 'completedReports', 'pendingReports'
        ));
    }

    public function showReport(Report $report)
    {
        $report->load(['room', 'building', 'technician', 'logs', 'user']);
        $technicians = Technician::where('status', '!=', 'offline')->get();
        return view('admin.report-detail', compact('report', 'technicians'));
    }

    public function updateReport(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,dibatalkan',
            'technician_id' => 'nullable|exists:technicians,id',
            'technician_notes' => 'nullable|string',
        ]);

        $oldStatus = $report->status;
        $report->update($request->only(['status', 'technician_id', 'technician_notes']));

        // Log status change
        if ($oldStatus !== $request->status) {
            $statusMessages = [
                'diproses' => 'Laporan sedang diproses',
                'selesai' => 'Perbaikan selesai & laporan ditutup',
                'dibatalkan' => 'Laporan dibatalkan oleh admin',
                'menunggu' => 'Status dikembalikan ke menunggu',
            ];

            $actionMessage = $statusMessages[$request->status] ?? 'Status diperbarui';
            ReportLog::create([
                'report_id' => $report->id,
                'action' => $actionMessage,
                'description' => $request->technician_notes,
                'status' => $request->status === 'selesai' ? 'completed' : 
                           ($request->status === 'dibatalkan' ? 'cancelled' : 'completed'),
            ]);

            // Notify User
            $report->user->notify(new ReportNotification($report, $actionMessage));
        }

        // Log technician assignment
        if ($request->filled('technician_id') && $report->wasChanged('technician_id')) {
            $tech = Technician::find($request->technician_id);
            $actionMessage = "Ditugaskan ke {$tech->name}";
            ReportLog::create([
                'report_id' => $report->id,
                'action' => $actionMessage,
                'status' => 'completed',
            ]);

            // Notify User
            $report->user->notify(new ReportNotification($report, "Laporan Anda telah ditugaskan ke teknisi: {$tech->name}", 'assignment'));
        }

        // Log technician notes
        if ($request->filled('technician_notes') && $report->wasChanged('technician_notes')) {
            ReportLog::create([
                'report_id' => $report->id,
                'action' => 'Catatan teknisi ditambahkan',
                'description' => $request->technician_notes,
                'status' => 'completed',
            ]);

            // Notify User (optional, maybe only if status is not completed)
            if ($report->status !== 'selesai') {
                $report->user->notify(new ReportNotification($report, "Ada catatan baru dari teknisi untuk laporan Anda.", 'note'));
            }
        }

        return back()->with('success', 'Laporan berhasil diperbarui.');
    }

    public function users()
    {
        $users = User::withCount('reports')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function technicians()
    {
        $technicians = Technician::withCount('reports')->get();
        return view('admin.technicians', compact('technicians'));
    }

    public function storeTechnician(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'specialization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'technician',
            ]);

            Technician::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'specialization' => $request->specialization,
                'phone' => $request->phone,
                'status' => 'aktif',
            ]);
        });

        return back()->with('success', 'Teknisi dan akun berhasil ditambahkan.');
    }

    public function updateTechnician(Request $request, Technician $technician)
    {
        $request->validate([
            'status' => 'required|in:aktif,sibuk,offline',
        ]);

        $technician->update(['status' => $request->status]);

        return back()->with('success', 'Status teknisi berhasil diperbarui.');
    }

    public function settings()
    {
        $buildings = Building::with('rooms')->get();
        return view('admin.settings', compact('buildings'));
    }
}
