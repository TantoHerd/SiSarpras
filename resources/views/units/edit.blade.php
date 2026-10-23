@extends('layouts.app')
@section('title', 'Edit Satuan')

@section('content')
<div class="max-w-xl">
    <div class="card p-6">
        <h2 class="text-xl font-bold mb-4">Edit Satuan</h2>
        <form action="{{ route('units.update', $unit->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Nama Satuan *</label>
                <input type="text" name="name" value="{{ old('name', $unit->name) }}" required class="form-input">
                @error('name') <p class="text-critical text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Simbol (opsional)</label>
                <input type="text" name="symbol" value="{{ old('symbol', $unit->symbol) }}" class="form-input">
            </div>
            <div class="flex gap-2 pt-4">
                <button type="submit" class="btn-primary">Perbarui</button>
                <a href="{{ route('units.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection