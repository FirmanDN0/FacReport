<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportLog;
use Illuminate\Support\Facades\Storage;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $technician = auth()->user()->technician;
        
        if (!$technician) {
            abort(403, 'Anda tidak terdaftar sebagai teknisi.');
        }

        $reports = Report::where('technician_id', $technician->id)
            ->with(['room', 'building', 'user'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Report::where('technician_id', $technician->id)->count(),
            'pending' => Report::where('technician_id', $technician->id)->where('status', 'menunggu')->count(),
            'processing' => Report::where('technician_id', $technician->id)->where('status', 'diproses')->count(),
            'completed' => Report::where('technician_id', $technician->id)->where('status', 'selesai')->count(),
        ];

        return view('technician.dashboard', compact('reports', 'stats'));
    }

    public function showReport(Report $report)
    {
        $technician = auth()->user()->technician;
        if ($report->technician_id !== $technician->id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $report->load(['room', 'building', 'user', 'logs']);
        return view('technician.report-show', compact('report'));
    }

    public function updateReport(Request $request, Report $report)
    {
        $technician = auth()->user()->technician;
        if ($report->technician_id !== $technician->id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:diproses,selesai',
            'technician_notes' => 'nullable|string',
            'completion_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $oldStatus = $report->status;
        $data = $request->only(['status', 'technician_notes']);

        if ($request->hasFile('completion_photo')) {
            // Delete old photo if exists
            if ($report->completion_photo) {
                Storage::disk('public')->delete($report->completion_photo);
            }
            $data['completion_photo'] = $request->file('completion_photo')->store('completion-photos', 'public');
        }

        $report->update($data);

        if ($oldStatus !== $request->status) {
            $message = $request->status === 'selesai' ? 'Perbaikan selesai oleh teknisi' : 'Teknisi mulai memproses laporan';
            
            ReportLog::create([
                'report_id' => $report->id,
                'action' => $message,
                'description' => $request->technician_notes,
                'status' => $request->status === 'selesai' ? 'completed' : 'pending',
            ]);

            // Notify User
            $report->user->notify(new \App\Notifications\ReportNotification($report, $message));
        }

        return back()->with('success', 'Laporan berhasil diperbarui.');
    }
}
