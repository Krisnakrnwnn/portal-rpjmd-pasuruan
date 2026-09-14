@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-pengguna" class="content-section block font-sans">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Pengguna</h1>
            <p class="text-slate-500 text-sm">Administrasi akun pegawai dan hak akses sistem.</p>
          </div>
          <a href="{{ route('admin.pengguna.create', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-900 hover:bg-black text-white font-semibold text-sm rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-gray-900 w-max" aria-label="Kembali atau buka halaman pengguna form">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pegawai
          </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/75">
                <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama & Email</th>
                <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3.5 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Aksi</th>
              </tr>
            </thead>
            <tbody id="pengguna-tbody" data-admin-live="pengguna-tbody" class="divide-y divide-slate-100 text-sm">
              @foreach($users as $u)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                  <div class="text-xs text-slate-500">{{ $u->email }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->role == 'Super Admin' ? 'bg-gray-900 text-white border border-gray-900' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                    {{ $u->role }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                  <a href="{{ \App\Support\AdminNavigation::url('admin.pengguna.edit', ['user' => $u->id]) }}" class="text-xs px-3 py-1 bg-slate-100 text-slate-700 rounded-lg hover:bg-gray-900 hover:text-white transition-all font-semibold border border-slate-200 hover:border-transparent inline-block">Edit</a>
                  @if($u->id != auth()->id())
                  <form action="{{ route('admin.delete_user', $u->id) }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" class="inline">
                    @csrf
                    @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete(this.closest('form'), 'Hapus pengguna ini?')" class="text-xs px-3 py-1 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all font-semibold border border-rose-200 hover:border-transparent focus:outline-none">Hapus</button>
                  </form>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          {{-- Pagination Pengguna --}}
          <div data-admin-live="users-pagination"><x-admin.pagination :paginator="$users" /></div>
        </div>
      </section>
@endsection
