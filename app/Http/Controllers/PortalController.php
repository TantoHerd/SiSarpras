<?php
// app/Http/Controllers/PortalController.php

namespace App\Http\Controllers;

use App\Http\Requests\PortalLoanRequest;
use App\Services\PortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Models\Item;
use App\Services\PortalBhpService;
use App\Services\PortalLoanService;

class PortalController extends Controller
{
    public function __construct(
        protected PortalService $portalService,
        protected PortalBhpService $bhpService,
        protected PortalLoanService $loanService,
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

    /**
     * API: Cari identitas (siswa atau guru/staf) by nama
     * Digunakan untuk autofill di portal.
     */
    public function searchIdentity(Request $request): JsonResponse
    {
        $term = $request->get('q');
        $type = $request->get('type', 'all'); // 'student', 'user', 'all'

        if (strlen($term) < 3) {
            return response()->json([]);
        }

        $results = collect();

        // Cari Siswa
        if ($type === 'all' || $type === 'student') {
            $students = Student::active()
                ->where('name', 'ILIKE', "%{$term}%")
                ->orWhere('nis', 'ILIKE', "%{$term}%")
                ->take(5)
                ->get()
                ->map(fn($s) => [
                    'id'    => $s->id,
                    'type'  => 'student',
                    'name'  => $s->name,
                    'id_number' => $s->nis,
                    'phone' => $s->phone ?? '-',
                    'class' => $s->class,
                ]);
            $results = $results->merge($students);
        }

        // Cari Guru/Staf/Petugas (kecuali admin)
        if ($type === 'all' || $type === 'user') {
            $users = User::where('name', 'ILIKE', "%{$term}%")
                ->orWhere('nip', 'ILIKE', "%{$term}%")
                ->whereHas('role', fn($q) => $q->whereIn('name', ['guru', 'petugas_sarpras']))
                ->take(5)
                ->get()
                ->map(fn($u) => [
                    'id'    => $u->id,
                    'type'  => 'user',
                    'name'  => $u->name,
                    'id_number' => $u->nip ?? '-',
                    'phone' => $u->phone ?? '-',
                    'class' => 'Guru/Staf',
                ]);
            $results = $results->merge($users);
        }

        return response()->json($results->take(10)->values());
    }

    /**
     * Form Permintaan BHP
     */
    public function bhpForm(): View
    {
        $items = Item::consumable()
            ->where('quantity', '>', 0) // Hanya tampilkan yang ada stoknya
            ->whereColumn('quantity', '>', 'min_stock') // Hanya yang stoknya di atas min_stock
            ->with(['category'])
            ->orderBy('name')
            ->get();

        return view('portal.bhp', compact('items'));
    }

    /**
     * Proses Permintaan BHP (Sementara redirect, logika di 4C)
     */
    public function bhpStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|min:10|max:500',
            'identity_nis_nip' => 'required|string',
            'identity_name' => 'required|string',
            'identity_phone' => 'nullable|string',
            'identity_type' => 'required|in:student,user',
        ]);

        try {
            $this->bhpService->createRequest($validated);
            
            // ← KUNCI: Gunakan session 'success_popup'
            return redirect()->route('portal.index')->with('success_popup', 'Permintaan BHP berhasil! Silakan ambil barang Anda di ruang Sarpras.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Form Peminjaman (Multi-item)
     */
    public function loanForm(): View
    {
        $items = Item::asset() // Hanya barang aset (bukan consumable)
            ->available() // Status = tersedia
            ->with(['category', 'location'])
            ->orderBy('name')
            ->get();

        return view('portal.loan', compact('items'));
    }

    public function loanStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|min:10|max:500',
            'identity_nis_nip' => 'required|string',
            'identity_name' => 'required|string',
            'identity_phone' => 'nullable|string',
            'identity_type' => 'required|in:student,user',
        ]);

        try {
            $this->loanService->createRequest($validated);
            
            // ← KUNCI: Gunakan session 'success_popup' agar memicu modal di /portal
            return redirect()->route('portal.index')->with('success_popup', 'Permintaan peminjaman Anda berhasil diajukan! Silakan tunggu approval dari petugas.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

}