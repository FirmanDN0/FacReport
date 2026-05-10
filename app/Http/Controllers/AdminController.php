<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\Technician;
use App\Models\Building;
use App\Models\Room;
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

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user,technician',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
        ]);

        // If role is technician, we should ideally create a Technician record too, 
        // but the user might want to fill those details separately in Technicians page.
        // However, for consistency with AdminController@storeTechnician, let's auto-create basic tech record.
        if ($user->role === 'technician') {
            \App\Models\Technician::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'status' => 'offline',
            ]);
        }

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user,technician',
            'password' => 'nullable|string|min:8',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->reports()->count() > 0) {
            return back()->with('error', 'Pengguna tidak bisa dihapus karena memiliki riwayat laporan.');
        }

        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
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

    // Building Management
    public function storeBuilding(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:buildings,code',
        ]);

        Building::create($request->all());
        return back()->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function updateBuilding(Request $request, Building $building)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:buildings,code,' . $building->id,
        ]);

        $building->update($request->all());
        return back()->with('success', 'Gedung berhasil diperbarui.');
    }

    public function deleteBuilding(Building $building)
    {
        // Optional: Check if building has reports or rooms
        if ($building->rooms()->count() > 0) {
            return back()->with('error', 'Gedung tidak bisa dihapus karena masih memiliki ruangan.');
        }

        $building->delete();
        return back()->with('success', 'Gedung berhasil dihapus.');
    }

    // Room Management
    public function storeRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'building_id' => 'required|exists:buildings,id',
            'floor' => 'nullable|string|max:10',
        ]);

        Room::create($request->all());
        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'building_id' => 'required|exists:buildings,id',
            'floor' => 'nullable|string|max:10',
        ]);

        $room->update($request->all());
        return back()->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function deleteRoom(Room $room)
    {
        // Optional: Check if room has reports
        if ($room->reports()->count() > 0) {
            return back()->with('error', 'Ruangan tidak bisa dihapus karena memiliki riwayat laporan.');
        }

        $room->delete();
        return back()->with('success', 'Ruangan berhasil dihapus.');
    }
}
