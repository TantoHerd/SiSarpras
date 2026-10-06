# Changelog

Semua perubahan penting pada SISARPRAS akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/),
dan project ini mengikuti [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.1.0-sprint1] - 2026-10-06

### Added

**Sumber Dana Module**
- CRUD Sumber Dana (admin only)
- Seeder: BOS, BOPD, BANPROV, Lainnya
- Relasi `funding_source_id` di tabel `items`
- Dropdown Sumber Dana di form Item
- Filter Sumber Dana di halaman Items
- Kolom & filter Sumber Dana di laporan Inventaris (web, PDF, Excel)
- Export Excel master Sumber Dana
- Widget Top 5 Sumber Dana di Dashboard
- Statistik & filter di index Sumber Dana

### Fixed

- Setting kepala sekolah tidak tersimpan (cache key mismatch)
- Validasi grup `headmaster` di `UpdateSettingRequest`
- `funding_source_id` tidak tersimpan di `UpdateItemRequest`
- Total row alignment di PDF laporan inventaris

### Technical

- New migration: `funding_sources` table
- New migration: `add_funding_source_id_to_items_table`
- New export class: `FundingSourceExport`
- New views: `funding-sources/{index,create,edit,_form}`
- Repository + Service pattern untuk consistency

## [1.0.0] - 2026-09-XX

### Added

- Initial release: Items, Loans, Maintenances, Reports, Settings
- Auth dengan Breeze
- Layout: Top Nav + Sidebar
- Role middleware: admin, petugas_sarpras, kepala_sekolah, guru
