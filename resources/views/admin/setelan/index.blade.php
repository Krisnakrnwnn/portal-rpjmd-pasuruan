@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-setelan" class="content-section block font-sans">
        <div class="mb-6">
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Setelan Konfigurasi</h1>
          <p class="text-slate-500 text-sm">Pengaturan server RPJMD, model AI chatbot, dan profil instansi.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- General Settings -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
            <h2 class="text-lg font-bold text-slate-900 mb-6 border-b border-slate-100 pb-3">Pengaturan Model AI Chatbot</h2>
            <form id="ai-settings-form" action="{{ \App\Support\AdminNavigation::url('admin.update_settings') }}" method="POST" class="space-y-6" data-test-url="{{ route('admin.test_ai_model') }}">
              @csrf
              <input type="hidden" name="provider" value="gemini">
              <div>
                <p class="text-sm font-semibold text-slate-700">Provider AI</p>
                <p class="text-sm text-slate-900">{{ $aiCatalog['gemini']['label'] ?? 'Google Gemini' }}</p>
                @error('provider')<p class="text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror
              </div>
              <div class="rounded-xl bg-slate-50 p-4 text-sm">
                <p class="font-semibold text-slate-700">Model aktif</p>
                <p id="active-model-display" class="font-bold text-slate-900 break-words">{{ $aiSettings ? $aiSettings['provider_label'].' — '.$aiSettings['model_label'].' ('.$aiSettings['model'].')' : 'Konfigurasi AI belum siap' }}</p>
                @if(!$aiSettings)
                  <p class="mt-2 text-rose-700" role="alert">Konfigurasi default tidak valid. Hubungi pengelola server.</p>
                @elseif($aiSettings['fallback'])
                  <p class="mt-2 text-amber-800" role="status">Setelan tersimpan tidak valid. Chatbot menggunakan default aplikasi sampai model yang valid disimpan.</p>
                @elseif($aiSettings['default'])
                  <p class="mt-2 text-slate-600">Belum ada model tersimpan; menggunakan default aplikasi.</p>
                @endif
              </div>
              <div>
                <label for="gemini_model" class="block text-sm font-semibold text-slate-700 mb-2">Model yang dipilih</label>
                <select name="gemini_model" id="gemini_model" required aria-describedby="ai-model-help ai-model-error" aria-invalid="{{ $errors->has('gemini_model') ? 'true' : 'false' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                  <option value="">Pilih model AI</option>
                  @foreach($aiCatalog['gemini']['models'] ?? [] as $modelId => $modelLabel)
                    <option value="{{ $modelId }}" @selected(old('gemini_model', $aiSettings['model'] ?? '') === $modelId)>{{ $modelLabel }}</option>
                  @endforeach
                </select>
                <p id="ai-model-help" class="text-xs text-slate-500 mt-2">Pilihan baru aktif setelah Simpan. Test Model memakai kuota API dan hanya menguji koneksi tanpa dokumen RPJMD.</p>
                <div id="ai-model-error">@error('gemini_model')<p class="text-sm text-rose-600 mt-2" role="alert">{{ $message }}</p>@enderror</div>
              </div>
              <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-3">
                <button type="button" id="test-ai-model" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-600 disabled:opacity-50">Test Model</button>
                <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white font-semibold rounded-xl transition-all shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                  Simpan Model AI
                </button>
              </div>
              <div id="ai-test-result" aria-live="polite" aria-atomic="true" class="text-sm text-slate-700 whitespace-pre-wrap break-words"></div>
            </form>
          </div>

          <!-- Info Panel -->
          <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 md:p-8 flex flex-col justify-between">
            <div>
              <div class="w-10 h-10 bg-slate-200 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <h3 class="text-lg font-bold mb-2 text-slate-900">Panduan Editor Profil</h3>
              <p class="text-slate-600 text-sm leading-relaxed">Gunakan editor di bawah ini untuk mengelola konten yang ditampilkan di halaman <strong>Profil Instansi</strong> pada portal publik.</p>
            </div>
            <ul class="mt-6 space-y-2.5 text-xs text-slate-500">
              <li class="flex items-start gap-2"><span class="text-slate-900 font-bold">✦</span> <span><strong class="text-slate-900">Sejarah:</strong> Narasi latar belakang Kabupaten Pasuruan</span></li>
              <li class="flex items-start gap-2"><span class="text-slate-900 font-bold">✦</span> <span><strong class="text-slate-900">Visi:</strong> Pernyataan visi utama pembangunan daerah</span></li>
              <li class="flex items-start gap-2"><span class="text-slate-900 font-bold">✦</span> <span><strong class="text-slate-900">Misi:</strong> Gunakan pemisah <code class="bg-slate-200 px-1 rounded text-slate-700 font-mono">|</code> antar butir misi</span></li>
            </ul>
          </div>
        </div>

        <!-- ===== PROFIL EDITOR SECTION ===== -->
        <div class="mb-4">
          <h2 class="text-lg font-bold text-slate-900">Editor Profil Instansi</h2>
          <p class="text-slate-500 text-sm">Konten ini akan dipublikasikan di halaman profil publik.</p>
        </div>

        <form action="{{ \App\Support\AdminNavigation::url('admin.update_profile') }}" method="POST" class="space-y-6">
          @csrf
          @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif

          {{-- Sejarah Card --}}
          @php $sejarah = $profiles->firstWhere('key','sejarah') @endphp
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-amber-50/50">
              <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">Sejarah Singkat</h3>
                <p class="text-xs text-slate-500">Narasi sejarah berdirinya Kabupaten Pasuruan.</p>
              </div>
            </div>
            <div class="p-6">
              <textarea
                name="profiles[sejarah]"
                id="editor-sejarah"
                rows="8"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all font-normal text-slate-800 text-sm leading-relaxed resize-none"
                placeholder="Tuliskan sejarah singkat Kabupaten Pasuruan di sini..."
              >{{ old('profiles.sejarah', $sejarah?->content) }}</textarea>
              <p class="text-xs text-slate-400 mt-2 text-right" id="count-sejarah">0 karakter</p>
            </div>
          </div>

          {{-- Visi Card --}}
          @php $visi = $profiles->firstWhere('key','visi') @endphp
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/80">
              <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">Visi Pembangunan</h3>
                <p class="text-xs text-slate-500">Pernyataan visi utama Kabupaten Pasuruan.</p>
              </div>
            </div>
            <div class="p-6">
              <textarea
                name="profiles[visi]"
                id="editor-visi"
                rows="3"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none transition-all font-semibold text-slate-900 text-sm leading-relaxed resize-none"
                placeholder="Contoh: Terwujudnya Kabupaten Pasuruan yang Maju, Sejahtera, dan Berkeadilan"
              >{{ old('profiles.visi', $visi?->content) }}</textarea>
              <p class="text-xs text-slate-400 mt-2 text-right" id="count-visi">0 karakter</p>
            </div>
          </div>

          {{-- Misi Card --}}
          @php $misi = $profiles->firstWhere('key','misi') @endphp
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-emerald-50/50">
              <div class="w-9 h-9 rounded-xl bg-emerald-600/10 border border-emerald-600/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
              </div>
              <div>
                <h3 class="font-bold text-slate-900 text-sm">Misi Pembangunan</h3>
                <p class="text-xs text-slate-500">Pisahkan tiap butir misi dengan tanda garis tegak <strong class="font-mono bg-slate-100 px-1 rounded text-slate-700">|</strong>.</p>
              </div>
            </div>
            <div class="p-6">
              <textarea
                name="profiles[misi]"
                id="editor-misi"
                rows="8"
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-normal text-slate-800 text-sm leading-relaxed resize-none"
                placeholder="Misi 1|Misi 2|Misi 3"
              >{{ old('profiles.misi', $misi?->content) }}</textarea>
              <div class="flex items-center justify-between mt-2">
                <p class="text-xs text-emerald-600 font-semibold" id="count-misi-butir">0 butir misi terdeteksi</p>
                <p class="text-xs text-slate-400" id="count-misi">0 karakter</p>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button type="submit" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white font-semibold py-3 px-8 rounded-xl shadow-sm transition-all text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Simpan Profil Instansi
            </button>
          </div>
        </form>
      </section>
@endsection
@push('scripts')
@include('admin.scripts.settings')
@endpush
