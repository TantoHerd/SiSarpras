@extends('layouts.app')
@section('title', 'Tambah Satuan')

@section('content')
<div class="max-w-xl">
    <div class="card p-6">
        <h2 class="text-xl font-bold mb-4">Tambah Satuan</h2>
        <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Nama Satuan *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Contoh: Box">
                @error('name') <p class="text-critical text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Simbol (opsional)</label>
                <input type="text" name="symbol" value="{{ old('symbol') }}" class="form-input" placeholder="Contoh: box">
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('units.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection