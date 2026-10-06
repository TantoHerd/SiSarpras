<?php
// app/Repositories/StudentRepository.php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class StudentRepository
{
    public function __construct(
        protected Student $model
    ) {}

    public function paginate(?string $search = null, ?string $class = null, ?string $isActive = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->search($search)
            ->byClass($class)
            ->when($isActive !== null && $isActive !== '', function ($q) use ($isActive) {
                $q->where('is_active', (bool) $isActive);
            })
            ->orderBy('class')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Student
    {
        return $this->model->findOrFail($id);
    }

    public function findByNis(string $nis): ?Student
    {
        return $this->model->where('nis', $nis)->first();
    }

    public function create(array $data): Student
    {
        return $this->model->create($data);
    }

    public function update(Student $student, array $data): Student
    {
        $student->update($data);
        return $student->fresh();
    }

    public function delete(Student $student): bool
    {
        return $student->delete();
    }

    public function getStats(): array
    {
        return [
            'total'    => $this->model->count(),
            'active'   => $this->model->where('is_active', true)->count(),
            'inactive' => $this->model->where('is_active', false)->count(),
        ];
    }

    public function getClasses(): Collection
    {
        return $this->model
            ->select('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');
    }
}