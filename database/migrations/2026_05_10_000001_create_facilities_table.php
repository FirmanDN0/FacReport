<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gedung (Buildings)
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Gedung A, Gedung B, etc.
            $table->string('code')->unique(); // A, B, C, D
            $table->timestamps();
        });

        // Ruangan (Rooms)
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ruang Teori 204, Lab SIJA
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->string('floor')->nullable();
            $table->timestamps();
        });

        // Teknisi (Technicians)
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization')->nullable();
            $table->string('phone')->nullable();
            $table->enum('status', ['aktif', 'sibuk', 'offline'])->default('aktif');
            $table->integer('active_tasks')->default(0);
            $table->timestamps();
        });

        // Laporan Kerusakan (Damage Reports)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique(); // FLS-001, FLS-002
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('facility_name'); // Proyektor Epson, AC Ruangan
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->enum('severity', ['ringan', 'sedang', 'berat'])->default('sedang');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->foreignId('technician_id')->nullable()->constrained()->onDelete('set null');
            $table->text('technician_notes')->nullable();
            $table->timestamps();
        });

        // Log Penanganan (Handling Logs / Timeline)
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., "Laporan diterima oleh sistem"
            $table->text('description')->nullable();
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_logs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('buildings');
    }
};
