{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')
@section('page_subtitle', 'Daftar semua notifikasi Anda')

@section('content')

<div class="space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-success/10 border border-success/20">
            <i class="fas fa-check-circle text-success text-lg mt-0.5"></i>
            <p class="text-body-sm text-ink">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-body-sm text-steel">
                Total <span class="text-body-sm-bold text-ink-deep">{{ $notifications->total() }}</span> notifikasi
            </p>
        </div>
        @if($notifications->count() > 0)
            <div class="flex items-center gap-2">
                @if(auth()->user()->unreadNotifications()->count() > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="btn-ghost">
                            <i class="fas fa-check-double"></i>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif
                <form method="POST" 
                      action="{{ route('notifications.destroy-all') }}"
                      onsubmit="return confirm('Hapus semua notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash"></i>
                        Hapus Semua
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($notifications as $notif)
            @php
                $isUnread = !$notif->read_at;
                $data = $notif->data;
            @endphp

            <div class="card {{ $isUnread ? 'border-primary/30 bg-primary/5' : '' }} hover:border-primary/30 transition-colors">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-lg bg-warning/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-warning text-lg"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3 mb-1">
                            <div class="min-w-0 flex-1">
                                <p class="text-body-sm-bold text-ink-deep">
                                    Stok Menipis
                                    @if($isUnread)
                                        <span class="badge-info text-xs ml-1">Baru</span>
                                    @endif
                                </p>
                                <p class="text-body-sm text-ink mt-1">
                                    {{ $data['message'] ?? 'Notifikasi' }}
                                </p>
                            </div>
                            <p class="text-caption text-steel whitespace-nowrap">
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Info --}}
                        @if(isset($data['item_name']))
                            <div class="flex flex-wrap items-center gap-3 mt-2 text-caption">
                                <span class="text-steel">
                                    <i class="fas fa-box text-xs"></i>
                                    {{ $data['item_name'] }}
                                </span>
                                @if(isset($data['quantity']) && isset($data['min_stock']))
                                    <span class="text-critical font-bold">
                                        <i class="fas fa-arrow-down text-xs"></i>
                                        Stok: {{ $data['quantity'] }} (min: {{ $data['min_stock'] }})
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 mt-3">
                            @if(isset($data['url']))
                                <a href="{{ route('notifications.read', $notif->id) }}" 
                                   class="text-caption text-primary hover:underline font-bold">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                    Lihat Barang
                                </a>
                            @endif

                            @if($isUnread)
                                <a href="{{ route('notifications.read', $notif->id) }}" 
                                   class="text-caption text-steel hover:text-primary">
                                    <i class="fas fa-check text-xs"></i>
                                    Tandai Dibaca
                                </a>
                            @endif

                            <form method="POST" 
                                  action="{{ route('notifications.destroy', $notif->id) }}"
                                  class="inline"
                                  onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-caption text-critical hover:underline">
                                    <i class="fas fa-trash text-xs"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="card py-16 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-circle bg-surface-soft mb-5">
                    <i class="far fa-bell text-steel text-3xl"></i>
                </div>
                <h3 class="text-heading-sm text-ink-deep mb-2">Tidak Ada Notifikasi</h3>
                <p class="text-subtitle-md text-steel max-w-md mx-auto">
                    Anda belum menerima notifikasi apapun.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="card">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@endsection