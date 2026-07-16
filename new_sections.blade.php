      <!-- ============================== -->
      <!-- SECTION: DOKUMEN / BANK DATA -->
      <!-- ============================== -->
      <section id="section-dokumen" class="content-section hidden">
        <!-- HEADER KATEGORI -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Kategori Dokumen</h1>
            <p class="text-gray-500 text-sm">Kelola kategori dokumen yang akan tampil di Portal Publik.</p>
          </div>
        </div>

        <!-- GRID KATEGORI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Form Tambah Kategori -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-black text-gray-900 mb-4 border-b pb-3">Tambah Kategori Baru</h3>
                    <form action="{{ route('admin.document-categories.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">Nama Kategori</label>
                            <input type="text" name="name" id="kategori-name" required placeholder="Contoh: Laporan Kinerja" class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none transition-all text-sm" oninput="document.getElementById('kategori-slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">Slug (Otomatis)</label>
                            <input type="text" name="slug" id="kategori-slug" required readonly placeholder="laporan-kinerja" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 outline-none text-sm text-gray-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-700 block mb-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="3" placeholder="Penjelasan singkat untuk menu..." class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 outline-none transition-all text-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 rounded-lg font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">Simpan Kategori</button>
                    </form>
                </div>
            </div>

            <!-- Tabel Kategori -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total Dokumen</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 whitespace-nowrap">
                            @forelse($documentCategories as $cat)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 text-sm">{{ $cat->name }}</p>
                                <p class="text-xs text-gray-500 max-w-xs truncate">{{ $cat->description }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full text-xs">{{ $cat->documents()->count() ?? 0 }} Dokumen</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <form action="{{ route('admin.document-categories.destroy', $cat->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus kategori ini? Pastikan tidak ada dokumen di dalamnya.')" class="text-xs px-3 py-1 bg-gray-50 text-gray-600 rounded hover:text-red-600 transition-colors">Hapus</button>
                                </form>
                            </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm font-bold">Belum ada kategori.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-gray-200 mb-12">

        <!-- HEADER DOKUMEN -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
          <div>
            <h1 class="text-3xl font-black text-gray-900 mb-1">Bank Data & Dokumen</h1>
            <p class="text-gray-500 text-sm">Kelola dokumen publik seperti RPJMD, RKPD, Hasil Riset, dll.</p>
          </div>
          <button onclick="showSection('section-dokumen-form')" class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-700 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Dokumen
          </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                  <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Dokumen</th>
                  <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                  <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 whitespace-nowrap">
                @forelse($publicDocuments as $doc)
                <tr class="hover:bg-blue-50/50 transition-colors">
                  <td class="px-6 py-4 font-bold text-gray-900 text-sm max-w-xs truncate">{{ $doc->title }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $doc->documentCategory->name ?? $doc->category }}</td>
                  <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                    <button onclick="editDocument({{ json_encode($doc) }})" class="text-xs px-3 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors">Edit</button>
                    <form action="{{ route('admin.delete_document', $doc->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus dokumen ini?')" class="text-xs px-3 py-1 bg-gray-50 text-gray-600 rounded hover:text-red-600 transition-colors">Hapus</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada dokumen.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- SECTION: DOKUMEN FORM -->
      <section id="section-dokumen-form" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-dokumen')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Tambah Dokumen</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form action="{{ route('admin.store_document') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Upload File PDF</label>
              <input type="file" name="file" accept=".pdf" required onchange="document.getElementById('add-doc-title').value = this.files[0].name.replace(/\.[^/.]+$/, '')" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-gray-50">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Judul Dokumen</label>
              <input type="text" name="title" id="add-doc-title" required placeholder="Contoh: Dokumen RPJMD Tahun 2025" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Kategori</label>
                <select name="document_category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                  @foreach($documentCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Tahun</label>
                <input type="number" name="year" required value="{{ date('Y') }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
              </div>
            </div>
            <button type="submit" class="w-full py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Simpan Dokumen</button>
          </form>
        </div>
      </section>

      <!-- SECTION: DOKUMEN EDIT -->
      <section id="section-dokumen-edit" class="content-section hidden">
        <div class="flex items-center gap-4 mb-8">
          <button onclick="showSection('section-dokumen')" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <h1 class="text-3xl font-black text-gray-900">Edit Dokumen</h1>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-2xl">
          <form id="form-edit-dokumen" action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Upload File PDF Baru (Opsional)</label>
              <input type="file" name="file" accept=".pdf" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-gray-50">
              <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengganti file dokumen yang lama.</p>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700">Judul Dokumen</label>
              <input type="text" name="title" id="edit-doc-title" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Kategori</label>
                <select name="document_category_id" id="edit-doc-category" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                  @foreach($documentCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700">Tahun</label>
                <input type="number" name="year" id="edit-doc-year" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
              </div>
            </div>
            <button type="submit" class="w-full py-3 rounded-xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Update Dokumen</button>
          </form>
        </div>
      </section>

      <script>
        function editDocument(doc) {
          const form = document.getElementById('form-edit-dokumen');
          form.action = `/admin/documents/${doc.id}`;
          
          document.getElementById('edit-doc-title').value = doc.title;
          document.getElementById('edit-doc-category').value = doc.document_category_id;
          document.getElementById('edit-doc-year').value = doc.year;
          
          showSection('section-dokumen-edit');
        }
      </script>

