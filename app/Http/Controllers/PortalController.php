<?php
// app/Http/Controllers/PortalController.php

namespace App\Http\Controllers;

use App\Http\Requests\PortalLoanRequest;
use App\Services\PortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class PortalController extends Controller
{
    public function __construct(
        protected PortalService $portalService
    ) {}

    /**
     * Halaman utama portal
     */
    public function index(): View|RedirectResponse
    {
        // Cek portal aktif
        if (!$this->portalService->isPortalEnabled()) {
            return view('portal.disabled');
        }

        $welcomeText = setting('portal_siswa_welcome_text', 'Selamat datang di Portal Peminjaman Siswa.');
        $items = $this->portalService->getAvailableItems();

        return view('portal.index', compact('welcomeText', 'items'));
    }

    /**
     * Submit permintaan
     */
    public function store(PortalLoanRequest $request): RedirectResponse
    {
        // ═══════════════════════════════════════════════
        // DEBUG LOG — WAJIB untuk debugging
        // ═══════════════════════════════════════════════
        Log::info('=== PORTAL STORE START ===', [
            'nis' => $request->nis,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'purpose' => $request->purpose,
            'student_phone' => $request->student_phone,
        ]);

        // 1. Cek portal aktif
        if (!$this->portalService->isPortalEnabled()) {
            Log::warning('PORTAL: portal disabled');
            return redirect()->route('portal.index')
                ->with('error', 'Portal sedang tidak aktif.');
        }
        Log::info('PORTAL: portal is enabled');

        // 2. Verifikasi siswa
        $student = $this->portalService->verifyStudent(
            $request->nis,
            $request->student_phone
        );

        Log::info('PORTAL: verify student', [
            'found' => $student ? true : false,
            'student_id' => $student?->id,
            'student_name' => $student?->name,
        ]);

        if (!$student) {
            Log::warning('PORTAL: student not found or inactive');
            return back()
                ->withInput()
                ->with('error', 'Data siswa tidak ditemukan atau tidak aktif. Periksa NIS & No HP.');
        }

        // 3. Buat request
        try {
            Log::info('PORTAL: attempting to create request...');

            $portalRequest = $this->portalService->createRequest($student, $request->validated());

            Log::info('PORTAL: request CREATED successfully', [
                'portal_request_id' => $portalRequest->id,
            ]);

            return redirect()
                ->route('portal.success', ['nis' => $student->nis])
                ->with('success', 'Permintaan berhasil dikirim!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('PORTAL: VALIDATION ERROR', [
                'errors' => $e->errors(),
            ]);
            return back()->withInput()->withErrors($e->errors());

        } catch (\Exception $e) {
            Log::error('PORTAL: EXCEPTION', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Halaman sukses
     */
    public function success(Request $request): View
    {
        $nis = $request->get('nis');
        return view('portal.success', compact('nis'));
    }

    /**
     * Cek status permintaan
     */
    public function status(Request $request): View
    {
        $nis = $request->get('nis');
        $requests = collect();

        if ($nis) {
            // Verifikasi siswa ada
            $student = $this->portalService->verifyStudent($nis);
            
            if ($student) {
                $requests = $this->portalService->getStudentRequests($nis);
            }
        }

        return view('portal.status', compact('nis', 'requests'));
    }
}