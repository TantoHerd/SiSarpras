<?php
// app/Http/Controllers/SettingController.php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Halaman pengaturan — dengan tab
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'school');
        
        // Ambil semua setting, dikelompokkan per group
        $settings = Setting::orderBy('group_name')->orderBy('key')->get();
        $grouped = $settings->groupBy('group_name');

        return view('settings.index', compact('settings', 'grouped', 'tab'));
    }

    /**
     * Update setting berdasarkan group
     */
    public function update(UpdateSettingRequest $request)
    {
        try {
            // ← PERBAIKAN: ambil SEMUA input kecuali _token & _method
            $data = $request->except(['_token', '_method']);

            Log::info('SETTING UPDATE DEBUG', [
                'data_keys' => array_keys($data),
                'headmaster_name' => $request->input('headmaster_name'),
                'headmaster_nip' => $request->input('headmaster_nip'),
            ]);
            
            $group = $request->input('group_name', 'school');
            $redirectTab = $request->input('redirect_tab', $group);

            // Loop semua field, update satu per satu
            foreach ($data as $key => $value) {
                // Skip field yang bukan setting
                if (in_array($key, ['group_name', 'redirect_tab'])) {
                    continue;
                }

                $setting = Setting::where('key', $key)->first();
                if (!$setting) continue;

                // Handle boolean
                if ($setting->type === 'boolean') {
                    $value = $value ? 'true' : 'false';
                }

                // Handle file (logo)
                if ($setting->type === 'file' && $request->hasFile($key)) {
                    $file = $request->file($key);
                    
                    if ($setting->value && $setting->value !== 'logo-default.png' 
                        && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    $path = $file->store('logo', 'public');
                    $value = $path;
                } elseif ($setting->type === 'file') {
                    // Skip kalau tidak ada file baru
                    continue;
                }

                $this->settingService->update($key, $value);
            }

            // ← BARU: clear cache setelah update
            $this->settingService->clearCache();

            return redirect()
                ->route('settings.index', ['tab' => $redirectTab])
                ->with('success', 'Pengaturan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus logo (kembali ke default)
     */
    public function removeLogo()
    {
        try {
            $setting = Setting::where('key', 'school_logo')->firstOrFail();

            // Hapus file lama
            if ($setting->value && $setting->value !== 'logo-default.png' 
                && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            $this->settingService->update('school_logo', 'logo-default.png');

            return redirect()
                ->route('settings.index', ['tab' => 'school'])
                ->with('success', 'Logo berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus logo: ' . $e->getMessage());
        }
    }

    /**
     * Reset semua setting ke default
     */
    public function reset()
    {
        try {
            // Ambil dari seeder
            Artisan::call('db:seed', ['--class' => 'SettingSeeder', '--force' => true]);
            
            // Clear cache
            $this->settingService->clearCache();

            return redirect()
                ->route('settings.index')
                ->with('success', 'Semua pengaturan berhasil direset ke default.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset pengaturan: ' . $e->getMessage());
        }
    }
}