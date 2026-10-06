<?php
// app/Services/StudentService.php

namespace App\Services;

use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StudentService
{
    public function __construct(
        protected StudentRepository $repository
    ) {}

    public function paginate(?string $search = null, ?string $class = null, ?string $isActive = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $class, $isActive, $perPage);
    }

    public function find(int $id): Student
    {
        return $this->repository->find($id);
    }

    public function findByNis(string $nis): ?Student
    {
        return $this->repository->findByNis($nis);
    }

    public function create(array $data): Student
    {
        return $this->repository->create($this->normalize($data));
    }

    public function update(Student $student, array $data): Student
    {
        return $this->repository->update($student, $this->normalize($data));
    }

    public function delete(Student $student): void
    {
        $this->repository->delete($student);
    }

    public function getStats(): array
    {
        return $this->repository->getStats();
    }

    public function getClasses(): Collection
    {
        return $this->repository->getClasses();
    }

    protected function normalize(array $data): array
    {
        if (isset($data['nis'])) {
            $data['nis'] = trim($data['nis']);
        }
        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        if (isset($data['class'])) {
            $data['class'] = trim($data['class']);
        }
        if (isset($data['phone'])) {
            $data['phone'] = trim($data['phone']) ?: null;
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return $data;
    }
}