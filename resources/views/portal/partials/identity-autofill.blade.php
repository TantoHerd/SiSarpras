{{-- resources/views/portal/partials/identity-autofill.blade.php --}}

<div class="mb-6" x-data="identityAutofill()">
    <h3 class="text-lg font-bold text-ink-deep mb-4">Identitas Pemohon</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Search Input --}}
        <div class="md:col-span-2 relative">
            <label class="form-label">Ketik Nama Anda <span class="text-critical">*</span></label>
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-steel pointer-events-none"></i>
                <input type="text" 
                       x-model="search"
                       @input.debounce.300ms="fetchResults()"
                       @focus="open = true"
                       @click.away="open = false"
                       placeholder="Ketik nama atau NIS/NIP..."
                       class="form-input pl-11"
                       :class="selected ? 'bg-surface-soft pointer-events-none' : ''"
                       :readonly="selected">
                
                {{-- Clear Button (jika sudah dipilih) --}}
                <button type="button" 
                        x-show="selected" 
                        @click="reset()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-critical hover:text-critical/80 text-sm font-bold">
                    <i class="fas fa-times-circle"></i> Ganti
                </button>
            </div>

            {{-- Dropdown Hasil --}}
            <div x-show="open && results.length > 0" 
                 class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-hairline-soft max-h-60 overflow-y-auto">
                <template x-for="item in results" :key="item.type + '-' + item.id">
                    <button type="button" 
                            @click="select(item)"
                            class="w-full text-left px-4 py-3 hover:bg-surface-soft border-b border-hairline-soft last:border-0 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-ink-deep" x-text="item.name"></p>
                                <p class="text-sm text-steel">
                                    <span x-text="item.type === 'student' ? 'Siswa' : 'Guru/Staf'"></span> • 
                                    <span x-text="item.class"></span>
                                </p>
                            </div>
                            <span class="badge-neutral text-xs" x-text="item.id_number"></span>
                        </div>
                    </button>
                </template>
            </div>
            <div x-show="open && search.length >= 3 && results.length === 0 && !selected" 
                 class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-hairline-soft p-4 text-center">
                <p class="text-steel text-sm">Identitas tidak ditemukan.</p>
            </div>
        </div>

        {{-- NIS / NIP (Readonly) --}}
        <div>
            <label class="form-label">NIS / NIP</label>
            <input type="text" 
                x-model="idNumber" 
                name="identity_nis_nip"   {{-- ← FIX: Ganti name --}}
                id="identity_nis_nip"
                readonly 
                class="form-input bg-surface-soft" 
                placeholder="-">
        </div>

        {{-- No HP (Readonly) --}}
        <div>
            <label class="form-label">No HP</label>
            <input type="text" 
                x-model="phone" 
                name="identity_phone" 
                id="identity_phone"
                readonly 
                class="form-input bg-surface-soft" 
                placeholder="-">
        </div>
        
        {{-- Hidden Inputs untuk Backend --}}
        <input type="hidden" name="identity_name" x-model="name">
        <input type="hidden" name="identity_type" x-model="type">
        <input type="hidden" name="identity_id" x-model="id">
    </div>
</div>

@push('scripts')
<script>
    function identityAutofill() {
        return {
            search: '',
            results: [],
            open: false,
            selected: false,
            name: '',
            idNumber: '',
            phone: '',
            type: '',
            id: '',

            async fetchResults() {
                if (this.search.length < 3 || this.selected) {
                    this.results = [];
                    return;
                }
                try {
                    const res = await fetch(`{{ route('portal.search-identity') }}?q=${encodeURIComponent(this.search)}&type={{ $type ?? 'all' }}`);
                    this.results = await res.json();
                    this.open = true;
                } catch (e) {
                    console.error(e);
                }
            },

            select(item) {
                this.selected = true;
                this.open = false;
                this.search = item.name;
                this.name = item.name;
                this.idNumber = item.id_number;
                this.phone = item.phone;
                this.type = item.type;
                this.id = item.id;
            },

            reset() {
                this.selected = false;
                this.search = '';
                this.name = '';
                this.idNumber = '';
                this.phone = '';
                this.type = '';
                this.id = '';
                this.results = [];
            }
        }
    }
</script>
@endpush