# FacilityOS - Sistem Pelaporan Fasilitas 🏢

FacReport (FacilityOS) adalah platform berbasis web yang dirancang untuk mempermudah pelaporan, pelacakan, dan perbaikan kerusakan fasilitas di lingkungan organisasi/gedung. Proyek ini dibangun menggunakan **Laravel 12**, **Vite**, dan **Tailwind CSS v4**.

## ✨ Fitur Utama
- **Multi-Role Access**: Admin, Teknisi, dan User (Pelapor).
- **Real-time Notifications**: Notifikasi instan tanpa refresh halaman menggunakan **Laravel Reverb**.
- **Bukti Foto Perbaikan**: Teknisi wajib mengunggah foto bukti setelah perbaikan selesai.
- **Dashboard Statistik**: Visualisasi data laporan untuk Admin.
- **Profil Mandiri**: Pengguna dapat mengelola profil dan keamanan akun secara mandiri.

---

## 🚀 Panduan Instalasi (Cara Clone)

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

### 1. Prasyarat
Pastikan Anda sudah menginstal:
- **PHP >= 8.2**
- **Composer**
- **Node.js & NPM**
- **SQLite** (atau database pilihan Anda)

### 2. Clone Repository
```bash
git clone https://github.com/username/repository-name.git
cd repository-name
```

### 3. Instal Dependensi PHP
```bash
composer install
```

### 4. Instal Dependensi Frontend
```bash
npm install
```

### 5. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Kemudian buat Application Key:
```bash
php artisan key:generate
```

### 6. Setup Database
Pastikan database SQLite sudah tersedia (default di Laravel 11+):
*Jika file `database/database.sqlite` belum ada, silakan buat file kosong tersebut.*

Jalankan migrasi dan isi data contoh (seeder):
```bash
php artisan migrate:fresh --seed
```

---

## 🛠️ Cara Menjalankan Aplikasi

Anda perlu menjalankan **tiga perintah** ini di terminal yang berbeda (atau gunakan alat seperti Laravel Herd/Valet):

1. **Jalankan Server Laravel:**
   ```bash
   php artisan serve
   ```

2. **Jalankan Vite (Assets & CSS):**
   ```bash
   npm run dev
   ```

3. **Jalankan Server Notifikasi Real-time (WebSocket):**
   ```bash
   php artisan reverb:start
   ```

---

## 🔐 Akun Uji Coba (Credentials)

Setelah menjalankan `--seed`, Anda bisa login menggunakan akun berikut:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@gmail.com` | `password` |
| **User (Pelapor)** | `fardan@gmail.com` | `password` |
| **Teknisi** | `andi@gmail.com` | `password` |
| **Teknisi** | `budi@gmail.com` | `password` |

---

## 📝 Catatan Tambahan
- Jika muncul peringatan "Unknown at-rule" (`@theme`, `@source`) di VS Code, silakan instal ekstensi **Tailwind CSS IntelliSense** atau abaikan saja karena itu fitur resmi Tailwind v4.
- Pastikan koneksi internet stabil saat pertama kali menjalankan `npm run dev` untuk mengunduh font dari Google Fonts.

---
Dikembangkan dengan ❤️ oleh Tim FacilityOS.
