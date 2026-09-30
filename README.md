# SISARPRAS

Sistem Informasi Sarana & Prasarana Sekolah berbasis web.

## Tech Stack

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: PostgreSQL 18
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **Design System**: Meta-inspired

## Fitur Utama

- ✅ Manajemen User (Admin, Petugas, Kepsek, Guru)
- ✅ Manajemen Barang (CRUD + Barcode/QR Code)
- ✅ Peminjaman Barang (Request, Approval, Return)
- ✅ Perawatan Barang (Rutin & Perbaikan)
- ✅ Master Data (Kategori, Lokasi, Supplier)
- ✅ Laporan (PDF & Excel) — *coming soon*
- ✅ Pengaturan Sistem — *coming soon*

## Instalasi

### 1. Clone repository

\`\`\`bash
git clone https://github.com/username/sisarpras.git
cd sisarpras
\`\`\`

### 2. Install dependencies

\`\`\`bash
composer install
npm install
\`\`\`

### 3. Setup environment

\`\`\`bash
cp .env.example .env
php artisan key:generate
\`\`\`

Edit `.env` sesuai konfigurasi database lokal Anda.

### 4. Setup database

Buat database PostgreSQL:

\`\`\`sql
CREATE DATABASE sisarpras_db;
\`\`\`

Jalankan migrasi & seeder:

\`\`\`bash
php artisan migrate:fresh --seed
php artisan storage:link
\`\`\`

### 5. Build asset

\`\`\`bash
npm run build
\`\`\`

### 6. Jalankan server

\`\`\`bash
php artisan serve
\`\`\`

Buka: http://localhost:8000

## Akun Default

| Role | Email | Password |
|:---|:---|:---|
| Admin | admin@sekolah.sch.id | password123 |
| Petugas Sarpras | sarpras@sekolah.sch.id | password123 |
| Kepala Sekolah | kepsek@sekolah.sch.id | password123 |
| Guru | budi@sekolah.sch.id | password123 |

> ⚠️ **Wajib ganti password** setelah login pertama!

## Development

\`\`\`bash
# Jalankan server
php artisan serve

# Build asset dengan watch (auto-rebuild)
npm run dev
\`\`\`

## Akses via Ngrok (untuk testing mobile)

\`\`\`bash
# Terminal 1
php artisan serve

# Terminal 2
ngrok http 8000

# Build asset (bukan npm run dev)
npm run build
\`\`\`

Update `.env`:
\`\`\`
APP_URL=https://xxx.ngrok-free.app
ASSET_URL=https://xxx.ngrok-free.app
\`\`\`

## Lisensi

MIT License
\`\`\`