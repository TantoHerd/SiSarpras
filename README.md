<p align="center">
   <a href="https://github.com/USERNAME/sisarpras" target="_blank">
      <img src="https://ui-avatars.com/api/?name=SISARPRAS&background=0064e0&color=fff&size=128&bold=true" alt="sisarpras-logo" width="80px" height="auto">
   </a>
</p>

<h1 align="center">
   <a href="https://github.com/USERNAME/sisarpras" target="_blank" align="center">
      SISARPRAS — Sistem Informasi Sarana & Prasarana Sekolah
   </a>
</h1>

<p align="center">
   🚀 Aplikasi manajemen inventaris sekolah yang modern, lengkap, dan mudah digunakan. Dibangun dengan Laravel 12 + PostgreSQL.
</p>

<p align="center">
  <a href="https://github.com/USERNAME/sisarpras/blob/main/LICENSE">
    <img src="https://img.shields.io/github/license/USERNAME/sisarpras?style=flat-square" alt="license">
  </a>
  <a href="https://github.com/USERNAME/sisarpras/releases/">
    <img src="https://img.shields.io/github/release/USERNAME/sisarpras.svg?style=flat-square" alt="GitHub release">
  </a>
  <a href="https://github.com/USERNAME/sisarpras/issues">
    <img src="https://img.shields.io/github/issues/USERNAME/sisarpras.svg?style=flat-square" alt="GitHub issues">
  </a>
  <a href="https://github.com/USERNAME/sisarpras/issues">
    <img src="https://img.shields.io/github/issues-closed/USERNAME/sisarpras.svg?style=flat-square" alt="GitHub closed issues">
  </a>
  <a href="https://github.com/USERNAME/sisarpras/stargazers">
    <img src="https://img.shields.io/github/stars/USERNAME/sisarpras?style=flat-square" alt="GitHub stars">
  </a>
  <a href="https://github.com/USERNAME/sisarpras/network/members">
    <img src="https://img.shields.io/github/forks/USERNAME/sisarpras?style=flat-square" alt="GitHub forks">
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/PostgreSQL-18-4169E1?style=flat-square&logo=postgresql&logoColor=white" alt="PostgreSQL 18">
  <img src="https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Version-1.1.0--sprint1-blue?style=flat-square" alt="Version">
  <img src="https://img.shields.io/badge/Status-Active-success?style=flat-square" alt="Status">
  <img src="https://img.shields.io/badge/PRs-Welcome-brightgreen?style=flat-square" alt="PRs Welcome">
</p>

<p align="center">
  <kbd>
    <img src="docs/screenshots/preview.png" alt="SISARPRAS Preview" width="100%">
  </kbd>
</p>

---

## 🚀 Introduction

**SISARPRAS** adalah sistem informasi berbasis web untuk mengelola **Sarana & Prasarana Sekolah**. Dirancang untuk membantu operator sekolah, petugas sarpras, dan kepala sekolah dalam mendata aset, mengelola peminjaman, mencatat perawatan, dan menghasilkan laporan inventaris secara profesional.

Dibangun dengan **Laravel 12**, **PostgreSQL 18**, dan **Tailwind CSS 3**, SISARPRAS mengedepankan **kemudahan penggunaan**, **keamanan data**, dan **estetika modern** dengan design system Meta-inspired (pill buttons, cobalt accent, rounded cards).

🔗 **[Live Demo](#)** _(coming soon)_

---

## ⚙️ Installation Guide

Getting started is super simple! Follow the steps below:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/USERNAME/sisarpras.git
   cd sisarpras

2. **Install Composer Dependencies**
   ```bash
   composer install

3. **Copy .env & Generate App Key**
   ```bash
   cp .env.example .env
   php artisan key:generate

4. **Configure Database**
   Open .env file and update your PostgreSQL credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=sisarpras
   DB_USERNAME=postgres
   DB_PASSWORD=your_password

5. **Run Migrations & Seeder**
   ```bash
   php artisan migrate --seed

6. **Create Storage Link**
   ```bash
   php artisan storage:link

7. **Install Node Modules**
   ```bash
   npm install
   # OR
   yarn

8. **Build Frontend Assets**
   ```bash
   npm run dev
   # OR
   yarn dev
9. **Serve the Application**
   ```bash
   php artisan serve


🚀 Open http://127.0.0.1:8000 in your browser, and you're good to go!

Default Login:
Role	        Email	                Password
Admin	        admin@sekolah.sch.id	password123
Petugas	        petugas@sekolah.sch.id	password123
Kepala Sekolah	kepsek@sekolah.sch.id	password123
Guru	        budi@sekolah.sch.id	    password123

⚠️ Ganti password default setelah login pertama!

🏗️ Available Features

📦 Manajemen Inventaris
1. Data barang dengan kode otomatis format KATEGORI-TAHUN-BULAN-URUTAN
2. Kategorisasi, lokasi, supplier, dan Sumber Dana perolehan aset
3. Upload foto & generate barcode/QR code
4. Tracking kondisi (baik, rusak ringan, rusak berat)
5. Filter multi-kriteria & pencarian global

📋 Peminjaman
1. Pengajuan peminjaman oleh guru & staf
2. Approval workflow oleh petugas sarpras
3. Pengembalian dengan tracking keterlambatan
4. Denda opsional (aktif/nonaktif via settings)
5. Riwayat peminjaman per user

🔧 Perawatan
1. 2 jenis perawatan: Rutin & Perbaikan
2. Two-stage workflow (in_progress → completed)
3. Update kondisi barang otomatis
4. Pencatatan biaya perawatan

📊 Laporan
1. Laporan Inventaris — filter kategori, lokasi, kondisi, sumber dana
2. Laporan Peminjaman — filter tanggal, status, peminjam
3. Laporan Perawatan — filter tanggal, jenis, status
4. Export PDF (dengan kop surat & tanda tangan kepala sekolah)
5. Export Excel (dengan formatting & auto-filter)
6. Widget statistik di dashboard

⚙️ Pengaturan
1. Profil sekolah (nama, alamat, kontak, logo)
2. Data kepala sekolah untuk tanda tangan laporan
3. Preferensi aplikasi (nama, versi, timezone, format tanggal)
4. Pengaturan peminjaman (durasi, batas, denda)

🔐 Manajemen User & Role
1. 4 role dengan hak akses berbeda: Admin, Petugas Sarpras, Kepala Sekolah, Guru/Staf
2. Role middleware untuk akses kontrol
3. Reset password & toggle active oleh admin

🛠️ Tech Stack
<table>
<thead>
<tr>
    <th>Kategori</th>
    <th>Teknologi</th>
</tr>
</thead>
<tbody>
<tr>
<td>
    <p>Beckend</p>
    <p>Database</p>
    <p>Frontend</p>
    <p>Authentication</p>
    <p>PDF Export</p>
    <p>Excel Export</p>
    <p>Icons</p>
    <p>Font</p>
</td>
<td>
    <p>Laravel 12, PHP 8.2</p>
    <p>PostgreSQL 18</p>
    <p>Blade Templates, Tailwind CSS 3, Alpine.js</p>
    <p>Laravel Breeze</p>
    <p>barryvdh/laravel-dompdf</p>
    <p>maatwebsite/excel</p>
    <p>Font Awesome 6</p>
    <p>Montserrat</p>
</td>
</tr>
</tbody>
</table>

Design System (Meta-inspired)
<table>
<thead>
<tr>
    <th>Warna</th>
    <th>Hex</th>
    <th>Penggunaan</th>
</tr>
</thead>
<tbody>
<tr>
<td>
    <p>Primary Cobalt</p>
    <p>Ink Deep</p>
    <p>Surface Soft</p>
    <p>Hairline</p>
    <p>Success</p>
    <p>Warning</p>
    <p>Critical</p>
</td>
<td>
    <p>#0064e0</p>
    <p>#0a1317</p>
    <p>#f1f4f7</p>
    <p>#dee3e9</p>
    <p>#31a24c</p>
    <p>#f2a918</p>
    <p>#e41e3f</p>
</td>
<td>
    <p>Buttons, links, accents</p>
    <p>Text utama, dark backgrounds</p>
    <p>Backgrounds, cards</p>
    <p>Borders, dividers</p>
    <p>Status aktif, kondisi baik</p>
    <p>Peringatan, rusak ringan</p>
    <p>Error, rusak berat, delete</p>
</td>
</tr>
</tbody>
</table>


🧑‍💻 Available Commands
1. Development Mode (Hot Reload):
    ```bash
    npm run dev
    # OR
    yarn dev

2. Production Build:
   ```bash
   npm run build
   # OR
   yarn build

3. Clear Cache:
   ```bash
   php artisan optimize:clear

4. Reset Database (⚠️ Fresh Migrate):
   ```bash
   php artisan migrate:fresh --seed

📦 What's Included?

✅ Modul Selesai (Sprint 0-1)
1. Authentication
   - Login, Register, Forgot Password, Reset Password
   - Profile management

2. Dashboard
   - Statistik inventaris, peminjaman, perawatan
   - Widget Top 5 Sumber Dana (Sprint 1)

3. Master Data
   - Kategori, Lokasi, Supplier
   - Sumber Dana (Sprint 1)
   - Manajemen User

4. Data Barang (Items)
   - CRUD lengkap dengan upload foto
   - Barcode/QR generation
   - Kode otomatis

5. Peminjaman (Loans)
   - Request, approval, return
   - Denda opsional

6. Perawatan (Maintenances)
   - Rutin & Perbaikan
   - Two-stage workflow

7. Laporan (Reports)
   - Inventaris, Peminjaman, Perawatan
   - Export PDF & Excel

8. Pengaturan (Settings)
   - 4 tab: Sekolah, Kepsek, Preferensi, Peminjaman

🚧 Sedang Dikerjakan (Sprint 2-3)
- Cetak Label Massal (in progress)
- Hybrid Tracking Mode (per_unit / per_batch)
- Portal Siswa untuk Peminjaman
- Barang Habis Pakai + Stok Opname

📚 Documentation
📖 CHANGELOG.md — Riwayat versi
🤝 CONTRIBUTING.md — Panduan kontribusi
🔒 SECURITY.md — Kebijakan keamanan
📋 CODE_OF_CONDUCT.md — Kode etik

🖥️ Browser Support
<table>
<thead>
<tr>
    <th>Chrome</th>
    <th>Firefox</th>
    <th>Safari</th>
    <th>Edge</th>
    <th>Opera</th>
</tr>
</thead>
<tbody>
<tr>
<td>✅</td>
<td>✅</td>
<td>✅</td>
<td>✅</td>
<td>✅</td>
</tr>
</tbody>
</table>

Tested on:
- Chrome 120+
- Firefox 121+
- Safari 17+
- Edge 120+

👥 Roles & Permissions
<table>
<thead>
<tr>
    <th>Fitur</th>
    <th>Admin</th>
    <th>Petugas</th>
    <th>Kepsek</th>
    <th>Guru</th>
</tr>
</thead>
<tbody>
<tr>
<td>
    <p>Dashboard</p>
    <p>Data Barang (CRUD)</p>
    <p>Data Barang (view)</p>
    <p>Peminjaman (approval)</p>
    <p>Peminjaman (request)</p>
    <p>Perawatan</p>
    <p>Laporan</p>
    <p>Master Data</p>
    <p>Sumber Dana</p>
    <p>Manajemen User</p>
    <p>Pengaturan</p>
</td>
<td>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
</td>
<td>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>✅</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
</td>
<td>
    <p>✅</p>
    <p>❌</p>
    <p>✅</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
    <p>✅</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
</td>
<td>
    <p>✅</p>
    <p>❌</p>
    <p>✅</p>
    <p>❌</p>
    <p>✅</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
    <p>❌</p>
</td>
</tr>
</tbody>
</table>


🗺️ Roadmap

1. ✅ Sprint 1 — Quick Wins (Sedang Berjalan)
    - ☑ Fitur 1: Sumber Dana Aset
    - □ Fitur 2: Cetak Label Massal (in progress)

2. 🚧 Sprint 2 — Fitur Menengah (Planned)
    - □ Fitur 3: Hybrid Tracking Mode (per_unit / per_batch)
    - □ Fitur 4: Portal Siswa untuk Peminjaman

3. 🔮 Sprint 3 — Fitur Besar (Planned)
    - □ Fitur 5: Barang Habis Pakai + Stok Opname
    - □ Notifikasi stok menipis (dashboard + bell icon)
    - □ Approval workflow untuk permintaan BHP

4. 💡 Future Ideas
    - □ Barcode scanner integration (mobile-friendly)
    - □ Multi-warehouse / multi-lokasi support
    - □ Import barang via Excel
    - □ REST API untuk mobile app
    - □ Notifikasi WhatsApp/Email untuk peminjaman & perawatan
    - □ Audit log lengkap (siapa, kapan, apa)

🦸 Contributing
Contributions are welcome! Berikut caranya:

1. Fork the repository
2. Create your feature branch
   ```bash
   git checkout -b feature/amazing-feature
3. Commit your changes (gunakan Conventional Commits)
   ```bash
   git commit -m 'feat(items): add bulk import functionality'
4. Push to the branch
   ```bash
   git push origin feature/amazing-feature
5. Create a Pull Request
   📖 Baca CONTRIBUTING.md untuk panduan lengkap.

📅 Changelog
- Check out the CHANGELOG.md for detailed release notes.
- Latest Release: v1.1.0-sprint1 — Sumber Dana Module

🛠️ Support
🐛 Bug Report
- Buka issue di GitHub Issues dengan template Bug Report.

💡 Feature Request
- Buka discussion di GitHub Discussions dengan template Feature Request.

📧 Email
Untuk pertanyaan yang tidak cocok di issue tracker:
- 📧 support@sisarpras.test

📖 Dokumentasi
- Wiki (coming soon)
- CHANGELOG

📄 License
This project is open-sourced software licensed under the MIT license.
```text
MIT License

Copyright (c) 2026 SISARPRAS

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction...
```
Lihat file LICENSE untuk teks lengkap.

🙏 Credits
Dibangun Dengan
- Laravel — The PHP Framework for Web Artisans
- Tailwind CSS — Utility-first CSS framework
- Alpine.js — Lightweight JavaScript framework
- Font Awesome — Icon library
- DomPDF — PDF generation
- Maatwebsite Excel — Excel export

Inspirasi Design
- Meta Design System — Pill buttons, cobalt accent, rounded cards
- ThemeSelection Sneat — README structure inspiration

Kontributor
- [Tanto Herdiansyah] — Project Owner & Lead Developer
- Anda bisa menjadi kontributor berikutnya!

🔥 Tertarik Berkontribusi?
Kalau project ini bermanfaat, jangan lupa:
- ⭐ Star repo ini
- 🍴 Fork untuk eksplorasi
- 🐛 Report bug yang Anda temukan
- 💡 Usulkan fitur baru
- 📢 Share ke rekan guru/operator sekolah

<div align="center">
Dibuat dengan ❤️ untuk pendidikan Indonesia

⬆ Kembali ke atas

</div>
