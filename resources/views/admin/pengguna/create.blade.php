@extends('layouts.admin')
@section('title', $pageTitle . ' - Bapperida Admin')
@section('content')
<section id="section-pengguna-form" class="content-section block font-sans">
        <div class="flex items-center gap-4 mb-6">
          <a href="{{ route('admin.pengguna.index', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600" aria-label="Kembali atau buka halaman pengguna">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </a>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Pengguna Baru</h1>
            <p class="text-slate-500 text-sm">Daftarkan akun pengguna dan tentukan hak aksesnya.</p>
          </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 max-w-2xl">
          <form action="{{ route('admin.store_user') }}{{ \App\Support\AdminNavigation::context() ? '?'.http_build_query(\App\Support\AdminNavigation::context()) : '' }}" method="POST" class="space-y-6">
            @csrf
            @if($errors->any())<x-input-error :messages="$errors->all()" class="mb-4" role="alert" />@endif
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
              <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800" value="{{ old('name', $formDefaults['name'] ?? '') }}">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800" value="{{ old('email', $formDefaults['email'] ?? '') }}">
              </div>
              <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700">Role <span class="text-rose-500">*</span></label>
                <select name="role" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800 bg-white">
                  <option value="Admin" @selected((string) old('role', $formDefaults['role'] ?? '') === (string) ('Admin'))>Admin</option>
                  <option value="Super Admin" @selected((string) old('role', $formDefaults['role'] ?? '') === (string) ('Super Admin'))>Super Admin</option>
                  <option value="User" @selected((string) old('role', $formDefaults['role'] ?? '') === 'User')>User</option>
                </select>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-700">Password Sementara <span class="text-rose-500">*</span></label>
              <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-slate-800">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
              <a href="{{ route('admin.pengguna.index', request()->only(['q', 'status', 'category', 'category_id', 'page'])) }}" class="px-5 py-2.5 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all text-sm focus:outline-none focus:ring-2 focus:ring-slate-400" aria-label="Kembali atau buka halaman pengguna">Batalkan</a>
              <button type="submit" class="px-6 py-2.5 rounded-xl font-semibold bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition-all text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">Daftarkan Pengguna</button>
            </div>
          </form>
        </div>
      </section>
@endsection
