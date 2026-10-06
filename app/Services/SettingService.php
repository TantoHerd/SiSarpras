<?php
// app/Services/SettingService.php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected string $cacheKey = 'settings.all';
    protected int $cacheTtl = 3600; // 1 jam

    /**
     * Ambil semua setting (dengan cache)
     */
    public function getAll(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Ambil satu setting
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    /**
     * Ambil setting dengan konversi tipe data
     */
    public function getTyped(string $key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        
        if (!$setting) return $default;
        
        return match($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Update setting
     */
    public function update(string $key, $value): bool
    {
        $updated = Setting::where('key', $key)->update(['value' => $value]);
        
        $this->clearCache();
        
        return $updated > 0;
    }

    /**
     * Update banyak setting sekaligus
     */
    public function updateMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
        $this->clearCache();
    }

    /**
     * Clear cache
     */
    // app/Services/SettingService.php
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);  // 'settings.all'
        Cache::forget('settings');       // cadangan
    }

    /**
     * Ambil setting berdasarkan group
     */
    public function getByGroup(string $group): array
    {
        return Setting::where('group_name', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}