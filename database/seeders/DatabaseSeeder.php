<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Building;
use App\Models\Room;
use App\Models\Technician;
use App\Models\Report;
use App\Models\ReportLog;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Regular user
        $fardan = User::create([
            'name' => 'Fardan',
            'email' => 'fardan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);


        // Buildings
        $gedungA = Building::create(['name' => 'Gedung A', 'code' => 'A']);
        $gedungB = Building::create(['name' => 'Gedung B', 'code' => 'B']);
        $gedungC = Building::create(['name' => 'Gedung C', 'code' => 'C']);
        $gedungD = Building::create(['name' => 'Gedung D', 'code' => 'D']);

        // Rooms
        $rooms = [
            ['name' => 'Ruang Teori 204', 'building_id' => $gedungB->id, 'floor' => '2'],
            ['name' => 'Lab SIJA', 'building_id' => $gedungC->id, 'floor' => '1'],
            ['name' => 'Lab Komputer Lantai 3', 'building_id' => $gedungA->id, 'floor' => '3'],
            ['name' => 'Koridor Lantai 2', 'building_id' => $gedungA->id, 'floor' => '2'],
            ['name' => 'Toilet Lantai 1', 'building_id' => $gedungB->id, 'floor' => '1'],
            ['name' => 'Ruang 301', 'building_id' => $gedungC->id, 'floor' => '3'],
            ['name' => 'Aula Utama', 'building_id' => $gedungD->id, 'floor' => '1'],
            ['name' => 'Ruang Guru', 'building_id' => $gedungA->id, 'floor' => '1'],
            ['name' => 'Perpustakaan', 'building_id' => $gedungB->id, 'floor' => '2'],
            ['name' => 'Lab Fisika', 'building_id' => $gedungD->id, 'floor' => '2'],
        ];

        $createdRooms = [];
        foreach ($rooms as $room) {
            $createdRooms[] = Room::create($room);
        }

        // Technicians
        $technicianData = [
            ['name' => 'Teknisi Andi', 'email' => 'andi@gmail.com', 'specialization' => 'Elektronik', 'phone' => '081234567890', 'status' => 'aktif'],
            ['name' => 'Teknisi Budi', 'email' => 'budi@gmail.com', 'specialization' => 'Mekanikal', 'phone' => '081234567891', 'status' => 'aktif'],
            ['name' => 'Teknisi Citra', 'email' => 'citra@gmail.com', 'specialization' => 'AC & Pendingin', 'phone' => '081234567892', 'status' => 'sibuk'],
            ['name' => 'Teknisi Dewi', 'email' => 'dewi@gmail.com', 'specialization' => 'Plumbing', 'phone' => '081234567893', 'status' => 'offline'],
            ['name' => 'Teknisi Eko', 'email' => 'eko@gmail.com', 'specialization' => 'Listrik', 'phone' => '081234567894', 'status' => 'aktif'],
        ];

        foreach ($technicianData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'technician',
            ]);

            Technician::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'specialization' => $data['specialization'],
                'phone' => $data['phone'],
                'status' => $data['status'],
            ]);
        }

        // Reports
        $reportsData = [
            [
                'report_code' => 'FLS-001',
                'user_id' => $fardan->id,
                'facility_name' => 'Proyektor Epson',
                'room_id' => $createdRooms[0]->id,
                'building_id' => $gedungB->id,
                'severity' => 'sedang',
                'description' => 'Lampu berkedip merah, tidak mau nyala sama sekali.',
                'status' => 'diproses',
                'technician_id' => 1,
                'technician_notes' => 'Sparepart lampu sedang dipesan dari pusat. Estimasi pemasangan hari Rabu.',
                'created_at' => '2026-04-18 08:15:00',
            ],
            [
                'report_code' => 'FLS-002',
                'user_id' => $fardan->id,
                'facility_name' => 'Kursi Kayu Patah',
                'room_id' => $createdRooms[1]->id,
                'building_id' => $gedungC->id,
                'severity' => 'ringan',
                'description' => 'Kursi kayu di baris ke-3 patah kakinya, membahayakan siswa.',
                'status' => 'selesai',
                'technician_id' => 2,
                'created_at' => '2026-04-10 09:30:00',
            ],
            [
                'report_code' => 'FLS-003',
                'user_id' => $fardan->id,
                'facility_name' => 'AC Ruangan',
                'room_id' => $createdRooms[2]->id,
                'building_id' => $gedungA->id,
                'severity' => 'berat',
                'description' => 'AC tidak dingin, mengeluarkan suara berdengung keras.',
                'status' => 'diproses',
                'technician_id' => 3,
                'created_at' => '2026-04-24 10:00:00',
            ],
            [
                'report_code' => 'FLS-004',
                'user_id' => $fardan->id,
                'facility_name' => 'Lampu Koridor Lantai 2 Mati',
                'room_id' => $createdRooms[3]->id,
                'building_id' => $gedungA->id,
                'severity' => 'ringan',
                'description' => 'Lampu koridor lantai 2 mati total, gelap pada malam hari.',
                'status' => 'selesai',
                'technician_id' => 5,
                'created_at' => '2026-04-05 14:00:00',
            ],
            [
                'report_code' => 'FLS-005',
                'user_id' => $fardan->id,
                'facility_name' => 'Kran Wastafel Bocor',
                'room_id' => $createdRooms[4]->id,
                'building_id' => $gedungB->id,
                'severity' => 'sedang',
                'description' => 'Kran wastafel bocor, air menggenang di lantai toilet.',
                'status' => 'selesai',
                'technician_id' => 4,
                'created_at' => '2026-04-01 11:00:00',
            ],
            [
                'report_code' => 'FLS-006',
                'user_id' => $fardan->id,
                'facility_name' => 'Pintu Kelas Macet',
                'room_id' => $createdRooms[5]->id,
                'building_id' => $gedungC->id,
                'severity' => 'ringan',
                'description' => 'Pintu kelas sulit dibuka dan ditutup, engsel berkarat.',
                'status' => 'menunggu',
                'created_at' => '2026-05-02 08:00:00',
            ],
            [
                'report_code' => 'FLS-007',
                'user_id' => $fardan->id,
                'facility_name' => 'Pintu Kelas Macet',
                'room_id' => $createdRooms[5]->id,
                'building_id' => $gedungC->id,
                'severity' => 'ringan',
                'description' => 'Pintu kelas 301 tidak bisa dikunci dari luar.',
                'status' => 'menunggu',
                'created_at' => '2026-05-04 09:12:00',
            ],
        ];

        foreach ($reportsData as $data) {
            $report = Report::create($data);

            // Create timeline logs
            ReportLog::create([
                'report_id' => $report->id,
                'action' => 'Laporan diterima oleh sistem',
                'status' => 'completed',
                'created_at' => $report->created_at,
            ]);

            if (in_array($report->status, ['diproses', 'selesai'])) {
                ReportLog::create([
                    'report_id' => $report->id,
                    'action' => 'Dikonfirmasi oleh admin',
                    'status' => 'completed',
                    'created_at' => $report->created_at->addHours(1),
                ]);

                if ($report->technician_id) {
                    $tech = Technician::find($report->technician_id);
                    ReportLog::create([
                        'report_id' => $report->id,
                        'action' => "Ditugaskan ke {$tech->name} (Tim {$tech->specialization})",
                        'status' => 'completed',
                        'created_at' => $report->created_at->addDay(),
                    ]);
                }
            }

            if ($report->status === 'diproses') {
                ReportLog::create([
                    'report_id' => $report->id,
                    'action' => 'Inspeksi selesai — sparepart dipesan',
                    'status' => 'completed',
                    'created_at' => $report->created_at->addDays(2),
                ]);
                ReportLog::create([
                    'report_id' => $report->id,
                    'action' => 'Menunggu pemasangan sparepart',
                    'description' => 'Estimasi: ' . $report->created_at->addDays(5)->format('d F Y'),
                    'status' => 'pending',
                    'created_at' => $report->created_at->addDays(2),
                ]);
                ReportLog::create([
                    'report_id' => $report->id,
                    'action' => 'Perbaikan selesai & laporan ditutup',
                    'description' => '—',
                    'status' => 'pending',
                    'created_at' => $report->created_at->addDays(2),
                ]);
            }

            if ($report->status === 'selesai') {
                ReportLog::create([
                    'report_id' => $report->id,
                    'action' => 'Perbaikan selesai & laporan ditutup',
                    'status' => 'completed',
                    'created_at' => $report->created_at->addDays(3),
                ]);
            }
        }
    }
}
