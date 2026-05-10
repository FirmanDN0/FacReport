<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\Room;
use App\Models\Building;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\ReportNotification;
use Illuminate\Support\Facades\Notification;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Report::where('user_id', $user->id)->with(['room', 'building']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('report_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(10);

        // Stats
        $totalReports = Report::where('user_id', $user->id)->count();
        $processingReports = Report::where('user_id', $user->id)->where('status', 'diproses')->count();
        $completedReports = Report::where('user_id', $user->id)->where('status', 'selesai')->count();
        $pendingReports = Report::where('user_id', $user->id)->where('status', 'menunggu')->count();

        return view('user.reports.index', compact(
            'reports', 'totalReports', 'processingReports', 'completedReports', 'pendingReports'
        ));
    }

    public function create()
    {
        $rooms = Room::with('building')->get();
        $buildings = Building::all();
        return view('user.reports.create', compact('rooms', 'buildings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'building_id' => 'required|exists:buildings,id',
            'severity' => 'required|in:ringan,sedang,berat',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('report-photos', 'public');
        }

        $report = Report::create([
            'report_code' => Report::generateCode(),
            'user_id' => auth()->id(),
            'facility_name' => $request->facility_name,
            'room_id' => $request->room_id,
            'building_id' => $request->building_id,
            'severity' => $request->severity,
            'description' => $request->description,
            'photo' => $photoPath,
            'status' => 'menunggu',
        ]);

        // Create initial log
        ReportLog::create([
            'report_id' => $report->id,
            'action' => 'Laporan diterima oleh sistem',
            'description' => 'Laporan kerusakan berhasil dikirim dan menunggu konfirmasi admin.',
            'status' => 'completed',
        ]);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new ReportNotification($report, "Laporan baru: {$report->facility_name} di {$report->room->name}", 'new_report'));

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dikirim!');
    }

    public function show(Report $report)
    {
        // Ensure user can only see their own reports
        if ($report->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $report->load(['room', 'building', 'technician', 'logs', 'user']);
        return view('user.reports.show', compact('report'));
    }

    public function cancel(Report $report)
    {
        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        if ($report->status === 'selesai') {
            return back()->with('error', 'Laporan yang sudah selesai tidak bisa dibatalkan.');
        }

        $report->update(['status' => 'dibatalkan']);
        
        ReportLog::create([
            'report_id' => $report->id,
            'action' => 'Laporan dibatalkan oleh pelapor',
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Laporan berhasil dibatalkan.');
    }
}
