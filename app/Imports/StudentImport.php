<?php
// app/Imports/StudentImport.php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class StudentImport implements 
    ToModel, 
    WithHeadingRow, 
    SkipsOnError, 
    SkipsOnFailure,
    WithChunkReading
{
    protected int $successCount = 0;
    protected int $updateCount = 0;
    protected int $skipCount = 0;
    protected array $errors = [];
    protected array $skipped = [];

    public function model(array $row)
    {
        // Pastikan NIS dan No HP dibaca sebagai string murni
        $nis   = trim((string) ($row['nis'] ?? ''));
        $name  = trim((string) ($row['nama'] ?? $row['name'] ?? ''));
        $class = trim((string) ($row['kelas'] ?? $row['class'] ?? ''));
        $phone = trim((string) ($row['no_hp'] ?? $row['phone'] ?? $row['hp'] ?? ''));

        if (empty($nis)) {
            $this->skipCount++;
            $this->skipped[] = [
                'row' => $row,
                'reason' => 'NIS kosong',
            ];
            return null;
        }

        if (empty($name)) {
            $this->skipCount++;
            $this->skipped[] = [
                'nis' => $nis,
                'reason' => 'Nama kosong',
            ];
            return null;
        }

        if (empty($class)) {
            $this->skipCount++;
            $this->skipped[] = [
                'nis' => $nis,
                'reason' => 'Kelas kosong',
            ];
            return null;
        }

        $existing = Student::where('nis', $nis)->first();

        if ($existing) {
            $existing->update([
                'name'  => $name,
                'class' => $class,
                'phone' => $phone ?: null,
            ]);
            $this->updateCount++;
            return null;
        } else {
            $this->successCount++;
            return new Student([
                'nis'       => $nis,
                'name'      => $name,
                'class'     => $class,
                'phone'     => $phone ?: null,
                'is_active' => true,
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = [
            'message' => $e->getMessage(),
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    public function getSuccessCount(): int { return $this->successCount; }
    public function getUpdateCount(): int { return $this->updateCount; }
    public function getSkipCount(): int { return $this->skipCount; }
    public function getErrors(): array { return $this->errors; }
    public function getSkipped(): array { return $this->skipped; }
}