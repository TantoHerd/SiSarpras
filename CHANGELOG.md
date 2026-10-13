# Changelog

Semua perubahan penting pada **SISARPRAS** akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
dan project ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- **Fitur 6: Procurement** (Permintaan Pengadaan Barang) — _pending confirmation_
- Laporan stok BHP
- Export opname ke Excel

---

## [1.3.0-sprint3] - 2026-10-13

### Added

**Fitur 5 — Barang Habis Pakai (BHP) + Stok Opname**

#### Database
- Kolom `items.item_type` (`asset` / `consumable`)
- Kolom `items.min_stock` untuk notifikasi stok menipis
- Kolom `categories.default_item_type` (default item type per kategori)
- Tabel `stock_movements` (audit log pergerakan stok)
- Tabel `stock_requests` (permintaan BHP)
- Tabel `stock_request_items` (detail permintaan)
- Tabel `stock_opnames` (sesi opname per lokasi)
- Tabel `stock_opname_items` (detail opname per item)
- Setting baru: `stock_low_threshold`, `stock_request_max_items`, `stock_request_max_qty`

#### Items
- Radio **Tipe Barang** (Aset / Habis Pakai) di form create & edit
- Input **Minimal Stok** (muncul hanya untuk consumable)
- Filter **Tipe Barang** di `/items`
- Badge **Aset** / **BHP** di index & show
- Highlight merah + badge "Menipis" untuk stok menipis
- Scope `lowStock()` untuk query

#### Stock Movements (Pergerakan Stok)
- Halaman `/stock-movements` (index + show)
- Filter by item, type, date range, dan search
- Statistik: hari ini, masuk, keluar, penyesuaian
- Log immutable (append-only, no `updated_at`)
- Link ke referensi (stock request / stock opname)

#### Stock Requests (Permintaan BHP)
- Halaman `/stock-requests` (index, my, create, show)
- Form create **multi-item** (max 5 jenis, 10 unit per jenis)
- Approve → **auto-decrement stok** + log movement
- Reject dengan alasan penolakan
- DB transaction + `lockForUpdate` (race-safe)
- Badge **pending count** di sidebar
- Guru/staf hanya lihat milik sendiri

#### Stock Opname
- Halaman `/stock-opnames` (index, create, show)
- Buka sesi per lokasi → auto-populate items consumable
- **Snapshot `system_stock`** saat sesi dibuat
- Input fisik per item dengan real-time diff
- Complete → **koreksi stok** + log movement `adjustment`
- Cancel sesi draft
- Cegah 2 sesi draft untuk lokasi yang sama
- Snapshot stock untuk konsistensi

#### Notifikasi Stok Menipis
- Notification class `LowStockNotification`
- Command `stock:check-low-stock`
- Schedule otomatis setiap 6 jam
- **Bell icon** di top nav dengan badge count + dropdown
- Halaman `/notifications` (list lengkap)
- Mark as read / mark all read / delete
- Recipients: admin + petugas

### Fixed

- PostgreSQL operator `->>` untuk kolom `data` di notifications
  (filter di PHP daripada query JSON)
- Notifications table structure (tambah kolom standar Laravel)
- `StockMovement::record()` pakai `Auth::id()` (IDE-friendly)
- Portal submit validation (NIS + allow_student_loan)
- Rate limit terlalu ketat (GET 60/menit, POST 5/menit)
- Cache key mismatch di `SettingService`

### Technical

- 8 migration baru
- 5 model baru: `StockMovement`, `StockRequest`, `StockRequestItem`, `StockOpname`, `StockOpnameItem`
- 3 enum baru: `StockMovementTypeEnum`, `StockRequestStatusEnum`, `StockOpnameStatusEnum`
- Log movement immutable (append-only)
- `DB::transaction` + `lockForUpdate` untuk mencegah race condition
- Snapshot stock untuk konsistensi opname
- Laravel notifications (database channel)

### Testing

- Item asset vs consumable
- Min stock threshold + notifikasi
- Permintaan BHP (create, approve, reject)
- Auto decrement stok + log movement
- Stock opname (buka, input fisik, complete)
- Koreksi stok + log adjustment
- Bell icon + dropdown + mark as read
- Command `stock:check-low-stock`

---

## [1.2.0-sprint2] - 2026-10-06

### Added

**Fitur 3 — Hybrid Tracking Mode**

#### Database
- Kolom `categories.default_tracking_mode` (`per_unit` / `per_batch`)
- Kolom `items.tracking_mode` (nullable override)
- Kolom `loans.quantity` (support quantity > 1)

#### Kategori
- Radio **Mode Tracking Default** di form create & edit
- Badge mode tracking di index & show
- Fallback logic: `item.tracking_mode ?? category.default_tracking_mode ?? 'per_unit'`

#### Items
- Radio **Mode Tracking** di form create & edit (dengan info kategori live)
- Filter mode tracking di `/items`
- Badge **Unit** / **Batch** di index & show
- Info "Override" vs "Ikuti Kategori" di detail

#### Loans
- Support quantity > 1 untuk `per_batch`
- Validasi stok per mode (per_unit selalu 1, per_batch max stok)
- Auto-decrement stok saat pinjam (per_batch)
- Auto-increment stok saat kembali
- Status item auto-update (`dipinjam` / `tidak_aktif`)
- Lock `forUpdate` untuk mencegah race condition

**Fitur 4 — Portal Siswa**

#### Database
- Tabel `students` (NIS, nama, kelas, HP, `is_active`)
- Tabel `portal_requests` (queue approval dari portal)
- Kolom `categories.allow_student_loan` (flag kategori)
- Setting: `portal_siswa_enabled`, `portal_siswa_max_loans`, `portal_siswa_max_days`, `portal_siswa_welcome_text`

#### Admin Panel
- **CRUD Siswa** lengkap:
  - Index dengan search, filter kelas, filter status
  - Create/edit dengan live preview
  - Show detail
  - Delete dengan konfirmasi
- **Import Excel siswa**:
  - Download template
  - Upload `.xlsx` / `.xls` / `.csv`
  - `updateOrCreate` by NIS (idempotent)
  - Preview hasil (baru / diperbarui / dilewati)
  - Handle error per baris

#### Portal Publik (Tanpa Login)
- `GET /portal` — form peminjaman mandiri
- `POST /portal` — submit permintaan
- `GET /portal/status` — cek status by NIS
- `GET /portal/success` — halaman konfirmasi
- `GET /portal` (disabled) — halaman kalau portal OFF
- **Rate limiting:** GET 60/menit, POST 5/menit
- Verifikasi siswa via NIS + HP
- Form quantity dinamis (muncul kalau per_batch)

#### Petugas Review
- `GET /admin/portal-requests` — queue permintaan
  - Statistik: pending / approved / rejected / today
  - Filter status, search by NIS / nama / kelas
- `GET /admin/portal-requests/{id}` — detail lengkap
- `POST /admin/portal-requests/{id}/approve` — auto-create loan
- `POST /admin/portal-requests/{id}/reject` — dengan alasan
- **Badge pending count** di sidebar

#### Settings
- Tab **Portal Siswa** di `/settings`
- Toggle enable/disable portal
- Setting max loans per siswa (default: 3)
- Setting durasi pinjam siswa (default: 7 hari)
- Setting teks sambutan
- Copy link portal (1 klik)

### Fixed

- `funding_source_id` tidak tersimpan di `UpdateItemRequest`
- `headmaster_name` & `headmaster_nip` tidak tersimpan (cache key)
- Validasi grup `headmaster` di `UpdateSettingRequest`
- Total row alignment di PDF laporan inventaris
- Kategori terpotong di label single
- Settings syntax error (nested quotes)
- Portal/index `@push` yang salah
- Route conflict `/portal-requests` → `/admin/portal-requests`
- Rate limit terlalu ketat (3/menit → 60/menit GET, 5/menit POST)

### Technical

- Repository + Service pattern untuk consistency
- Form Request terpisah Store/Update
- Route split: index (admin+petugas+kepsek), CRUD (admin only)
- Dynamic delete modal dengan warning
- Print CSS optimized untuk A4
- Snapshot identitas siswa di `portal_requests`

---

## [1.1.0-sprint1] - 2026-10-05

### Added

**Fitur 1 — Sumber Dana Aset**
- Tabel `funding_sources` (code, name, description, is_active)
- Kolom `items.funding_source_id` (FK, nullable)
- Seeder: BOS, BOPD, BANPROV, Lainnya
- CRUD Sumber Dana (admin only)
- Dynamic delete modal (warning jika dipakai item)
- Statistik & filter di index Sumber Dana
- Dropdown Sumber Dana di form Item
- Filter Sumber Dana di `/items`
- Kolom & filter Sumber Dana di Laporan Inventaris (web, PDF, Excel)
- Tampilkan Sumber Dana di Detail Item

**Fitur 2 — Cetak Label Massal**
- Multi-select checkbox di `/items` dengan select all
- Route `/items/barcode-batch?ids=1,2,3`
- Preview grid A4: 3 kolom × 6 baris = 18 label per halaman
- Label single (9×5.8 cm) & batch (6.5×4.3 cm)
- Logo sekolah di label (fallback ke inisial)
- Badge Sumber Dana + Kategori di label
- QR code di label
- Print-friendly CSS

**Bonus**
- Export Excel master Sumber Dana
- Widget **Top 5 Sumber Dana** di Dashboard

### Fixed

- `funding_source_id` tidak tersimpan di Item
- Cache key mismatch di `SettingService`

### Technical

- New migration: `funding_sources` table
- New migration: `add_funding_source_id_to_items`
- New export: `FundingSourceExport`
- New views: `funding-sources/*`, `items/barcode-batch`

---

## [1.0.0] - 2026-09-29

### Added

**Initial Release**
- Autentikasi dengan Laravel Breeze
- Layout: Top Nav + Sidebar + responsive
- Middleware role: admin, petugas_sarpras, kepala_sekolah, guru

**Modul Items**
- CRUD barang dengan kode otomatis (`KATEGORI-TAHUN-BULAN-URUTAN`)
- Kategorisasi, lokasi, supplier
- Upload foto & generate barcode/QR
- Tracking kondisi (baik, rusak ringan, rusak berat)
- Filter multi-kriteria & pencarian
- Item history (audit perubahan)

**Modul Loans**
- Peminjaman oleh guru & staf
- Approval workflow oleh petugas
- Pengembalian dengan tracking keterlambatan
- Denda opsional (aktif/nonaktif via setting)
- Riwayat peminjaman per user

**Modul Maintenances**
- 2 jenis perawatan: **Rutin** & **Perbaikan**
- Two-stage workflow (`in_progress` → `completed`)
- Update kondisi barang otomatis
- Pencatatan biaya perawatan

**Modul Reports**
- Laporan Inventaris (filter kategori, lokasi, kondisi)
- Laporan Peminjaman (filter tanggal, status, peminjam)
- Laporan Perawatan (filter tanggal, jenis, status)
- Export PDF & Excel dengan formatting profesional
- Widget statistik di dashboard

**Modul Users & Master Data**
- Manajemen user (4 role)
- Master Data: Kategori, Lokasi, Supplier
- Pengaturan sistem (4 tab: Sekolah, Kepsek, Preferensi, Peminjaman)

**Design System (Meta-inspired)**
- Primary Cobalt: `#0064e0`
- Pill buttons, rounded-3xl cards
- Font: Montserrat
- Icon: Font Awesome 6

---

## Sprint Summary

| Sprint | Fitur | Tag | Status |
|---|---|---|---|
| **Sprint 1** | Sumber Dana + Cetak Label Massal | `v1.1.0-sprint1` | ✅ Complete |
| **Sprint 2** | Hybrid Tracking + Portal Siswa | `v1.2.0-sprint2` | ✅ Complete |
| **Sprint 3** | BHP + Stok Opname | `v1.3.0-sprint3` | ✅ Complete |
| **Sprint 4** | Procurement (Permintaan Pengadaan) | - | ⏸️ Pending |

**All 5 planned features DONE!** 🎉

---

## Versioning

Format versi: `MAJOR.MINOR.PATCH[-sprint]`

- **MAJOR** — Breaking changes
- **MINOR** — Fitur baru (backward compatible)
- **PATCH** — Bug fixes
- **-sprint** — Suffix untuk sprint milestone

---

## Types of Changes

- `Added` — Fitur baru
- `Changed` — Perubahan fitur existing
- `Deprecated` — Fitur yang akan dihapus
- `Removed` — Fitur yang dihapus
- `Fixed` — Bug fixes
- `Security` — Vulnerability fixes

---

## Links

- **Repository:** https://github.com/TantoHerd/SiSarpras
- **Releases:** https://github.com/TantoHerd/SiSarpras/releases
- **Issues:** https://github.com/TantoHerd/SiSarpras/issues
- **Wiki:** https://github.com/TantoHerd/SiSarpras/wiki _(coming soon)_

---

[Unreleased]: https://github.com/TantoHerd/SiSarpras/compare/v1.3.0-sprint3...HEAD
[1.3.0-sprint3]: https://github.com/TantoHerd/SiSarpras/releases/tag/v1.3.0-sprint3
[1.2.0-sprint2]: https://github.com/TantoHerd/SiSarpras/releases/tag/v1.2.0-sprint2
[1.1.0-sprint1]: https://github.com/TantoHerd/SiSarpras/releases/tag/v1.1.0-sprint1
[1.0.0]: https://github.com/TantoHerd/SiSarpras/releases/tag/v1.0.0
