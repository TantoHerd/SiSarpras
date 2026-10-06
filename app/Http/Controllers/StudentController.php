<?php
// app/Http/Controllers/StudentController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Exports\StudentTemplateExport;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function __construct(
        protected StudentService $service
    ) {}

    /**
     * Daftar siswa
     */
    public function index(Request $request): View
    {
        $filters = [
            'search'    => $request->input('search'),
            'class'     => $request->input('class'),
            'is_active' => $request->input('is_active'),
        ];

        $students = $this->service->paginate(
            $filters['search'],
            $filters['class'],
            $filters['is_active']
        );

        $stats   = $this->service->getStats();
        $classes = $this->service->getClasses();

        return view('students.index', compact('students', 'stats', 'classes', 'filters'));
    }

    /**
     * Form tambah
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Simpan
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        try {
            $student = $this->service->create($request->validated());

            return redirect()
                ->route('students.index')
                ->with('success', "Siswa \"{$student->name}\" berhasil ditambahkan.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan siswa: ' . $e->getMessage());
        }
    }

    /**
     * Detail
     */
    public function show(Student $student): View
    {
        return view('students.show', compact('student'));
    }

    /**
     * Form edit
     */
    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        try {
            $this->service->update($student, $request->validated());

            return redirect()
                ->route('students.index')
                ->with('success', "Data siswa \"{$student->name}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui siswa: ' . $e->getMessage());
        }
    }

    /**
     * Hapus
     */
    public function destroy(Student $student): RedirectResponse
    {
        try {
            $name = $student->name;
            $this->service->delete($student);

            return redirect()
                ->route('students.index')
                ->with('success', "Siswa \"{$name}\" berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }

    /**
     * Form import Excel
     */
    public function importForm(): View
    {
        return view('students.import');
    }

    /**
     * Proses import Excel
     */
    public function importStore(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',  // max 5MB
            ],
        ], [
            'file.required' => 'File wajib diupload.',
            'file.file'     => 'Upload harus berupa file.',
            'file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $import = new StudentImport();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $updateCount  = $import->getUpdateCount();
            $skipCount    = $import->getSkipCount();
            $errors       = $import->getErrors();
            $skipped      = $import->getSkipped();

            // Kalau semuanya kosong (tidak ada yang di-import)
            if ($successCount === 0 && $updateCount === 0) {
                return redirect()
                    ->route('students.import.form')
                    ->with('error', 'Tidak ada data yang berhasil di-import. Periksa format file Anda.');
            }

            // Flash summary ke session
            session()->flash('import_result', [
                'success' => $successCount,
                'update'  => $updateCount,
                'skip'    => $skipCount,
                'errors'  => $errors,
                'skipped' => $skipped,
            ]);

            $message = "Import selesai: {$successCount} baru, {$updateCount} diperbarui";
            if ($skipCount > 0) {
                $message .= ", {$skipCount} dilewati";
            }

            return redirect()
                ->route('students.index')
                ->with('success', $message . '.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new StudentTemplateExport(),
            'Template-Import-Siswa-' . now()->format('Ymd') . '.xlsx'
        );
    }
}