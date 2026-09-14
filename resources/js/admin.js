// Shared admin shell. Page-specific editors/charts/ingest stay in their Blade stacks.
const configuration = document.getElementById('admin-navigation');
if (configuration) {
    const navigation = JSON.parse(configuration.textContent);
    const main = document.getElementById('admin-content');
    const sidebar = document.getElementById('sidebarMenu');
    const overlay = document.getElementById('overlay');
    const menuButton = document.getElementById('hamburgerBtn');
    let submitting = false;
    const dirty = new Set();
    const originalValues = new WeakMap();
    const signature = form => JSON.stringify([...new FormData(form)].filter(([key]) => key !== '_token').map(([key, value]) => [key, value instanceof File ? `${value.name}:${value.size}:${value.lastModified}` : value]));
    const registerForms = () => main.querySelectorAll('form').forEach(form => {
        if (!originalValues.has(form)) originalValues.set(form, signature(form));
    });
    registerForms();
    window.adminResetFormBaseline = form => { originalValues.set(form, signature(form)); dirty.delete(form); };
    window.adminSubmit = form => { submitting = true; form.submit(); };
    window.adminHasUnsavedChanges = () => dirty.size > 0;

    function setSidebar(open) {
        sidebar.classList.toggle('-translate-x-full', !open);
        overlay.classList.toggle('hidden', !open);
        menuButton.setAttribute('aria-expanded', String(open));
        if (open) sidebar.querySelector('a')?.focus();
    }
    menuButton.addEventListener('click', () => setSidebar(sidebar.classList.contains('-translate-x-full')));
    overlay.addEventListener('click', () => { setSidebar(false); menuButton.focus(); });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            if (innerWidth < 1024 && !sidebar.classList.contains('-translate-x-full')) { setSidebar(false); menuButton.focus(); }
            closeNotification();
        }
    });
    function closeNotification() {
        document.getElementById('notif-dropdown')?.classList.add('hidden');
        document.getElementById('notif-btn')?.setAttribute('aria-expanded', 'false');
    }
    document.addEventListener('click', event => {
        if (event.target.closest('#notif-btn')) {
            const dropdown = document.getElementById('notif-dropdown');
            dropdown.classList.toggle('hidden');
            document.getElementById('notif-btn').setAttribute('aria-expanded', String(!dropdown.classList.contains('hidden')));
        } else if (!event.target.closest('#notif-wrapper')) closeNotification();
    });

    function legacyNavigation() {
        if (location.pathname.replace(/\/$/, '') !== new URL(navigation.home).pathname.replace(/\/$/, '')) return;
        const key = location.hash.slice(1);
        const target = Object.hasOwn(navigation.legacy, key) ? navigation.legacy[key] : null;
        if (target && new URL(target).pathname !== location.pathname.replace(/\/$/, '')) location.replace(target);
    }
    legacyNavigation();
    window.addEventListener('hashchange', legacyNavigation);

    function trackForm(event) {
        const form = event.target.closest('form');
        if (!form || !main.contains(form) || form.method.toLowerCase() === 'get' || event.target.hasAttribute('data-admin-filter')) return;
        if (signature(form) !== originalValues.get(form)) dirty.add(form); else dirty.delete(form);
    }
    document.addEventListener('input', trackForm);
    document.addEventListener('change', trackForm);
    document.addEventListener('submit', event => {
        if (!event.defaultPrevented && event.target.method.toLowerCase() !== 'get') submitting = true;
    });
    window.addEventListener('beforeunload', event => {
        if (dirty.size && !submitting) { event.preventDefault(); event.returnValue = ''; }
    });
    document.addEventListener('click', async event => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download')) return;
        if (!dirty.size || submitting) return;
        event.preventDefault();
        const result = await Swal.fire({title:'Perubahan belum disimpan', text:'Tinggalkan halaman dan abaikan perubahan?', icon:'warning', showCancelButton:true, confirmButtonText:'Ya, Tinggalkan', cancelButtonText:'Tetap di Sini', confirmButtonColor:'#2563eb', customClass:{popup:'rounded-3xl shadow-2xl',title:'font-black'}});
        if (result.isConfirmed) { submitting = true; location.assign(link.href); }
    });

    // Delegation survives replacement of rows after filtering or background polling.
    document.addEventListener('click', async event => {
        const toggle = event.target.closest('.toggle-btn');
        const resolve = event.target.closest('.resolve-btn');
        if (!toggle && !resolve) return;
        event.preventDefault();
        const button = toggle || resolve;
        const result = await Swal.fire({
            title:toggle ? button.dataset.title : 'Tandai Selesai?',
            text:toggle ? button.dataset.text : `Pesan dari "${button.dataset.name}" akan ditandai sebagai selesai.`,
            icon:'question', showCancelButton:true,
            confirmButtonColor:toggle ? button.dataset.color : '#16a34a', cancelButtonColor:'#6b7280',
            confirmButtonText:toggle ? button.dataset.confirm : 'Ya, Tandai Selesai', cancelButtonText:'Batal',
            customClass:{popup:'rounded-3xl shadow-2xl',title:'font-black'},
        });
        if (result.isConfirmed) window.adminSubmit(button.closest('form'));
    });

    let pendingRequest;
    let changingFilter = false;
    const filterStatus = document.createElement('p');
    filterStatus.className = 'text-sm text-gray-500 mb-3 hidden';
    filterStatus.setAttribute('role', 'status');
    main.prepend(filterStatus);
    async function refreshPage(url, userInitiated = false) {
        if (dirty.size || submitting || (!userInitiated && changingFilter)) return;
        pendingRequest?.abort();
        pendingRequest = new AbortController();
        if (userInitiated) { changingFilter = true; main.setAttribute('aria-busy','true'); filterStatus.textContent='Memuat…'; filterStatus.classList.remove('hidden'); }
        try {
            const response = await fetch(url, {signal:pendingRequest.signal, headers:{'X-Requested-With':'XMLHttpRequest','X-Silent-Polling':'true'}});
            if (response.redirected) { location.assign(response.url); return; }
            if (!response.ok) throw new Error('Request failed');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (!doc.getElementById('admin-content')) throw new Error('Invalid page');
            document.querySelectorAll('[data-admin-live]').forEach(current => {
                const next = doc.querySelector(`[data-admin-live="${current.dataset.adminLive}"]`);
                if (next) current.innerHTML = next.innerHTML;
            });
            if (userInitiated) history.pushState({}, '', url);
            registerForms();
            syncFilterState();
            filterStatus.classList.add('hidden');
        } catch (error) {
            if (error.name !== 'AbortError' && userInitiated) { filterStatus.textContent='Data belum dapat dimuat. Silakan coba lagi.'; filterStatus.classList.remove('hidden'); }
        } finally { changingFilter = false; main.removeAttribute('aria-busy'); }
    }
    function syncFilterState() {
        const query = new URL(location.href).searchParams;
        document.querySelectorAll('[data-admin-filter]').forEach(input => { input.value = query.get(input.dataset.adminFilter) || ''; });
        document.querySelectorAll('.aspirasi-filter-btn').forEach(button => {
            const active = button.id === `aspirasi-filter-${query.get('status') || 'all'}`;
            button.className = `aspirasi-filter-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all ${active ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'}`;
            button.setAttribute('aria-pressed', String(active));
        });
    }
    function applyFilter(key, value) {
        const url = new URL(location.href);
        value ? url.searchParams.set(key, value) : url.searchParams.delete(key);
        url.searchParams.delete('page');
        refreshPage(url.href, true);
    }
    let debounce;
    document.addEventListener('input', event => {
        const key = event.target.dataset.adminFilter;
        if (!key) return;
        clearTimeout(debounce);
        const value = event.target.value;
        if (key === 'q') debounce = setTimeout(() => applyFilter(key, value), 350);
        else applyFilter(key, value);
    });
    window.filterAspirasi = status => applyFilter('status', status === 'all' ? '' : status);
    syncFilterState();
    window.addEventListener('popstate', () => { syncFilterState(); refreshPage(location.href); });
    setInterval(() => {
        if (document.hidden || document.querySelector('[role="dialog"]:not(.hidden)') || document.querySelector('.swal2-container') || document.activeElement?.matches('input,textarea,select')) return;
        refreshPage(location.href);
    }, 15000);

    // Label existing fields without changing their visual layout or request names.
    main.querySelectorAll('input,select,textarea').forEach((field, index) => {
        if (field.type === 'hidden') return;
        if (!field.id) field.id = `admin-field-${index}`;
        if (!field.labels?.length) {
            const label = field.parentElement.querySelector('label');
            if (label) label.htmlFor = field.id;
            else field.setAttribute('aria-label', field.placeholder || field.name || 'Input');
        }
    });
}
