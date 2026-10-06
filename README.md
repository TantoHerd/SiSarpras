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
