<x-guest-layout>
    <div class="mb-8 overflow-hidden">
        <h2 class="text-3xl font-black text-blue-900 tracking-tight mb-2">Selamat Datang</h2>
        <p class="text-slate-500 text-sm font-medium">Masuk untuk mengakses portal manajemen RPJMD.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-xs font-black text-blue-900 uppercase tracking-widest pl-1">Alamat Email</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                    class="block w-full pl-12 pr-4 py-4 bg-white/50 border border-white/20 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all font-semibold text-slate-700 placeholder-slate-400" 
                    placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between pl-1">
                <label for="password" class="text-xs font-black text-blue-900 uppercase tracking-widest">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 transition-colors uppercase tracking-wider">Lupa Sandi?</a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-blue-400 group-focus-within:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                    class="block w-full pl-12 pr-4 py-4 bg-white/50 border border-white/20 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white outline-none transition-all font-semibold text-slate-700 placeholder-slate-400" 
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-white/20 text-blue-600 focus:ring-blue-500/20 bg-white/50 cursor-pointer">
            <label for="remember_me" class="ms-3 text-sm font-bold text-slate-600 cursor-pointer uppercase tracking-tight">Ingat Sesi Saya</label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-black text-lg rounded-2xl shadow-xl shadow-blue-900/20 hover:shadow-blue-900/40 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                Masuk Sekarang
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </button>
        </div>
    </form>
</x-guest-layout>
