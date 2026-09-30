{{-- resources/views/layouts/partials/footer.blade.php --}}
<footer class="bg-white border-t border-gray-200 px-6 py-3">
    <div class="flex items-center justify-between text-xs text-gray-500">
        <p>
            &copy; {{ date('Y') }} <strong>{{ setting('school_name', 'Sekolah') }}</strong>. 
            All rights reserved.
        </p>
        <p>
            <i class="fas fa-code"></i> 
            SISARPRAS v{{ setting('app_version', '1.0.0') }}
        </p>
    </div>
</footer>