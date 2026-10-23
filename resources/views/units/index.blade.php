@extends('layouts.app')
@section('title', 'Master Satuan')
@section('page_title', 'Master Satuan')
@section('page_subtitle', 'Kelola satuan barang (unit, pcs, box, dll)')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="card-sm bg-success/10 border-success/20 text-success px-4 py-3 text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="card-sm bg-critical/10 border-critical/20 text-critical px-4 py-3 text-sm font-bold">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between">
        <p class="text-body-sm text-steel">Total <strong class="text-ink-deep">{{ $units->total() }}</strong> satuan</p>
        <a href="{{ route('units.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Tambah Satuan</a>
    </div>

    <div class="card !p-0 overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface-soft border-b border-hairline-soft">
                <tr>
                    <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase">Nama Satuan</th>
                    <th class="text-left px-5 py-3 text-caption-bold text-steel uppercase">Simbol</th>
                    <th class="text-center px-5 py-3 text-caption-bold text-steel uppercase">Jumlah Barang</th>
                    <th class="text-right px-5 py-3 text-caption-bold text-steel uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline-soft">
                @forelse($units as $unit)
                    <tr class="hover:bg-surface-soft/50">
                        <td class="px-5 py-4 text-body-sm-bold text-ink-deep">{{ $unit->name }}</td>
                        <td class="px-5 py-4 text-body-sm text-steel font-mono">{{ $unit->symbol ?? '-' }}</td>
                        <td class="px-5 py-4 text-center text-body-sm-bold">{{ $unit->items_count }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('units.edit', $unit->id) }}" class="btn-icon !w-9 !h-9 hover:!bg-warning/10 hover:!text-warning" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus satuan ini?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon !w-9 !h-9 hover:!bg-critical/10 hover:!text-critical" title="Hapus">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-8 text-steel">Belum ada satuan.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($units->hasPages()) <div class="p-4 border-t">{{ $units->links() }}</div> @endif
    </div>
</div>
@endsection