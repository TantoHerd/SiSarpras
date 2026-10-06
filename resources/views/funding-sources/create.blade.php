@extends('layouts.app')

@section('title', 'Tambah Sumber Dana')
@section('page-title', 'Tambah Sumber Dana')
@section('page-subtitle', 'Buat data sumber dana baru')

@section('content')

<div class="max-w-2xl">

    <div class="card p-6 sm:p-8">

        {{-- Back --}}
        <a href="{{ route('funding-sources.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#0064e0] mb-6 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke daftar
        </a>

        <h2 class="text-xl font-bold text-[#0a1317] mb-1">Form Sumber Dana</h2>
        <p class="text-sm text-gray-500 mb-6">Isi informasi sumber dana dengan lengkap.</p>

        <form action="{{ route('funding-sources.store') }}" method="POST" class="space-y-5">
            @csrf

            @include('funding-sources._form', ['fundingSource' => null])

            <div class="flex items-center gap-3 pt-4 border-t border-[#dee3e9]">
                <button type="submit" class="btn-primary">Simpan</button>
                <a href="{{ route('funding-sources.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>

    </div>

</div>

@endsection