{{-- resources/views/students/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import Siswa')
@section('page_title', 'Import Data Siswa')
@section('page_subtitle', 'Upload file Excel untuk import siswa massal')

@section('content')

{{-- ==================== BREADCRUMB ==================== --}}
<nav class="mb-6 text-body-sm text-steel flex items-center gap-2">
    <a href="{{ route('students.index') }}" class="hover:text-primary flex items-center gap-1.5">
        <i class="fas fa-user-graduate text-xs"></i> Data Siswa
    </a>
    <i class="fas fa-chevron-right text-xs text-stone"></i>
    <span class="text-ink-deep">Import</span>
</nav>

<div class="max-w-3xl space-y-6">

    {{-- Flash --}}
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-critical/10 border border-critical/20">
            <i class="fas fa-exclamation-circle text-critical text-lg mt-0.5"></i>
            <p class="text-body-sm text-ink">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ============ STEP 1: DOWNLOAD TEMPLATE ============ --}}
    <div class="card">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center flex-shrink-0 text-body-md-bold">
                1
            </div>
            <div class="flex-1">
                <h3 class="text-body-md-bold text-ink-deep mb-1">Download Template</h3>
                <p class="text-body-sm text-steel mb-4">
                    Gunakan template ini untuk memastikan format file Anda benar.
                    Template sudah berisi contoh data.
                </p>
                <a href="{{ route('students.import.template') }}" class="btn-primary">
                    <i class="fas fa-file-excel"></i>
                    Download Template Excel
                </a>
            </div>
        </div>
    </div>

    {{-- ============ STEP 2: FORMAT INFO ============ --}}
    <div class="card">
        <h3 class="text-body-md-bold text-ink-deep mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-primary"></i>
            Format Kolom
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-body-sm">
                <thead class="bg-surface-soft border-b border-hairline-soft">
                    <tr>
                        <th class="text-left px-4 py-2 text-caption-bold text-steel uppercase">Kolom</th>
                        <th class="text-left px-4 py-2 text-caption-bold text-steel uppercase">Wajib?</th>
                        <th class="text-left px-4 py-2 text-caption-bold text-steel uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    <tr>
                        <td class="px-4 py-2 font-mono text-primary">nis</td>
                        <td class="px-4 py-2"><span class="badge-critical">Wajib</span></td>
                        <td class="px-4 py-2">Nomor Induk Siswa (unik)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-mono text-primary">nama</td>
                        <td class="px-4 py-2"><span class="badge-critical">Wajib</span></td>
                        <td class="px-4 py-2">Nama lengkap siswa</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-mono text-primary">kelas</td>
                        <td class="px-4 py-2"><span class="badge-critical">Wajib</span></td>
                        <td class="px-4 py-2">Kelas (contoh: X IPA 1)</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-mono text-primary">no_hp</td>
                        <td class="px-4 py-2"><span class="badge-neutral">Opsional</span></td>
                        <td class="px-4 py-2">No HP untuk verifikasi portal</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 p-3 rounded-xl bg-warning/5 border border-warning/20">
            <div class="flex items-start gap-2">
                <i class="fas fa-lightbulb text-warning mt-0.5 text-sm"></i>
                <div class="text-body-sm text-ink">
                    <strong>Tips:</strong>
                    <ul class="mt-1 space-y-1 list-disc list-inside text-steel">
                        <li>Baris pertama harus heading (nis, nama, kelas, no_hp).</li>
                        <li>Kalau NIS sudah ada, data akan <strong>diperbarui</strong> (bukan duplikat).</li>
                        <li>Kalau ada baris kosong, akan dilewati otomatis.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ STEP 3: UPLOAD ============ --}}
    <div class="card">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center flex-shrink-0 text-body-md-bold">
                2
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-body-md-bold text-ink-deep mb-1">Upload File Excel</h3>
                <p class="text-body-sm text-steel mb-4">
                    Format: <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong>.
                    Ukuran maks: <strong>5MB</strong>.
                </p>

                <form method="POST" 
                      action="{{ route('students.import.store') }}" 
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf

                    {{-- File input --}}
                    <div>
                        <input type="file" 
                               id="file" 
                               name="file" 
                               accept=".xlsx,.xls,.csv"
                               required
                               onchange="updateFileName(this)"
                               class="hidden">
                        
                        <label for="file" 
                               class="cursor-pointer block p-6 rounded-xl border-2 border-dashed border-hairline-soft hover:border-primary bg-canvas transition-colors text-center">
                            <i class="fas fa-cloud-upload-alt text-primary text-3xl mb-3"></i>
                            <p class="text-body-sm-bold text-ink-deep mb-1" id="file-label">
                                Klik untuk pilih file
                            </p>
                            <p class="text-caption text-steel">
                                atau drag & drop file di sini
                            </p>
                        </label>
                        
                        <p id="file-name" class="hidden mt-2 text-body-sm text-primary font-bold text-center"></p>
                    </div>

                    @error('file')
                        <p class="mt-2 text-body-sm text-critical-strong flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('students.index') }}" class="btn-ghost">
                            <i class="fas fa-arrow-left"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn-primary" id="submit-btn">
                            <i class="fas fa-upload"></i>
                            Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        const fileName = input.files[0]?.name;
        const label = document.getElementById('file-label');
        const nameEl = document.getElementById('file-name');
        
        if (fileName) {
            label.textContent = 'File dipilih:';
            nameEl.textContent = '📄 ' + fileName;
            nameEl.classList.remove('hidden');
        } else {
            label.textContent = 'Klik untuk pilih file';
            nameEl.textContent = '';
            nameEl.classList.add('hidden');
        }
    }
</script>
@endpush