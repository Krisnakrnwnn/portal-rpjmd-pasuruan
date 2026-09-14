@props(['route', 'module', 'active' => false])
<a href="{{ route($route) }}" @if($active) aria-current="page" @endif
    {{ $attributes->class(['nav-btn flex items-center px-3.5 py-2.5 rounded-xl transition-all w-full text-left font-medium text-sm focus-visible:outline-2 focus-visible:outline-blue-500', 'bg-gray-900 text-white shadow-sm font-semibold active' => $active, 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => !$active]) }}>
    {{ $slot }}
</a>
