(() => {
    const root = document.getElementById('privia-app');
    if (!root) return;

    const state = { activeId: root.closest('body')?.dataset.activeConversation || null, isSending: false, requestId: 0, abortController: null };
    const userName = root.closest('body')?.dataset.userName?.trim() || '';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const list = document.getElementById('privia-conversation-list');
    const messages = document.getElementById('privia-messages');
    const input = document.getElementById('privia-input');
    const send = document.getElementById('privia-send');
    const composer = document.getElementById('privia-composer');
    const sidebar = document.getElementById('privia-sidebar');
    const backdrop = document.getElementById('privia-backdrop');
    const dialog = document.getElementById('privia-dialog');
    const dialogPanel = dialog.querySelector('.privia-dialog__panel');
    const dialogTitle = document.getElementById('privia-dialog-title');
    const dialogDescription = document.getElementById('privia-dialog-description');
    const dialogContext = document.getElementById('privia-dialog-context');
    const dialogInputLabel = document.getElementById('privia-dialog-input-label');
    const dialogInput = document.getElementById('privia-dialog-input');
    const dialogError = document.getElementById('privia-dialog-error');
    const dialogCancel = document.getElementById('privia-dialog-cancel');
    const dialogConfirm = document.getElementById('privia-dialog-confirm');
    const dialogClose = document.getElementById('privia-dialog-close');
    const dialogState = { mode: null, id: null, previousFocus: null, busy: false };

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
    const formatText = (value) => escapeHtml(value).replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    const api = async (url, options = {}) => {
        const response = await fetch(url, {
            ...options,
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.body ? { 'Content-Type': 'application/json' } : {}), ...(options.headers || {}) },
        });
        const body = await response.json().catch(() => ({}));
        if (!response.ok) throw Object.assign(new Error(body.message || 'Permintaan tidak berhasil.'), { body, status: response.status });
        return body;
    };

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-visible');
        backdrop.hidden = true;
    }

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.hidden = false;
        requestAnimationFrame(() => backdrop.classList.add('is-visible'));
    }

    function renderWelcome() {
        const greeting = userName ? `Halo ${escapeHtml(userName)}, saya PRivIA.` : 'Halo, saya PRivIA.';
        messages.innerHTML = `<div class="privia-welcome">
            <div class="privia-welcome__mark"><img src="/logo.png" alt=""></div>
            <h2>${greeting}</h2>
            <p>Asisten informasi Bapperida untuk membantu memahami RPJMD, dokumen perencanaan, dan program pembangunan Kabupaten Pasuruan.</p>
            <div class="privia-suggestions">
                ${['Apa itu RPJMD?', 'Cari dokumen perencanaan', 'Jelaskan program prioritas', 'Apa visi dan misi Kabupaten Pasuruan?', 'Bantu saya mencari informasi Bapperida'].map((text) => `<button type="button" class="privia-suggestion" data-suggestion="${escapeHtml(text)}">${escapeHtml(text)}</button>`).join('')}
            </div>
        </div>`;
        messages.querySelectorAll('[data-suggestion]').forEach((button) => button.addEventListener('click', () => { input.value = button.dataset.suggestion; composer.requestSubmit(); }));
    }

    function renderConversations(items) {
        if (!items.length) {
            list.innerHTML = '<p class="privia-empty-history">Belum ada percakapan.</p>';
            return;
        }
        list.innerHTML = items.map((item) => `<div class="privia-conversation-item ${String(item.id) === String(state.activeId) ? 'is-active' : ''}">
            <button type="button" class="privia-conversation-open" data-open-conversation="${item.id}">
                <span class="privia-conversation-title">${escapeHtml(item.title)}</span>
                <span class="privia-conversation-date">${new Date(item.updated_at).toLocaleDateString('id-ID')}</span>
            </button>
            <button type="button" class="privia-conversation-action" data-rename-conversation="${item.id}" data-conversation-title="${escapeHtml(item.title)}" aria-label="Ganti nama ${escapeHtml(item.title)}">✎</button>
            <button type="button" class="privia-conversation-action" data-delete-conversation="${item.id}" data-conversation-title="${escapeHtml(item.title)}" aria-label="Hapus ${escapeHtml(item.title)}">×</button>
        </div>`).join('');
        list.querySelectorAll('[data-open-conversation]').forEach((button) => button.addEventListener('click', () => openConversation(button.dataset.openConversation)));
        list.querySelectorAll('[data-rename-conversation]').forEach((button) => button.addEventListener('click', () => openRenameDialog(button.dataset.renameConversation, button.dataset.conversationTitle)));
        list.querySelectorAll('[data-delete-conversation]').forEach((button) => button.addEventListener('click', () => openDeleteDialog(button.dataset.deleteConversation, button.dataset.conversationTitle)));
    }

    function renderMessages(items) {
        messages.innerHTML = items.map((item) => {
            const sources = item.role === 'assistant' && item.metadata?.sources?.length ? `<div class="privia-sources"><strong>Sumber</strong><ul>${item.metadata.sources.map((source) => `<li>${escapeHtml(source.name)}${source.page ? ` · halaman ${escapeHtml(source.page)}` : ''}</li>`).join('')}</ul></div>` : '';
            return `<article class="privia-message privia-message--${item.role === 'user' ? 'user' : 'assistant'}">
                ${item.role === 'assistant' ? '<div class="privia-message__avatar" aria-hidden="true">AI</div>' : ''}
                <div class="privia-message__bubble">${formatText(item.content)}${sources}</div>
            </article>`;
        }).join('');
        messages.scrollTop = messages.scrollHeight;
    }

    function appendLoading() {
        messages.insertAdjacentHTML('beforeend', '<div id="privia-loading" class="privia-message"><div class="privia-message__avatar" aria-hidden="true">AI</div><div class="privia-message__bubble privia-loading">PRivIA sedang menyiapkan jawaban</div></div>');
        messages.scrollTop = messages.scrollHeight;
    }

    async function loadConversations() {
        const result = await api('/privia/conversations');
        renderConversations(result.conversations);
        return result.conversations;
    }

    async function openConversation(id) {
        state.requestId += 1;
        const requestId = state.requestId;
        state.activeId = String(id);
        state.abortController?.abort();
        state.abortController = new AbortController();
        messages.innerHTML = '<p class="privia-loading">Memuat percakapan</p>';
        closeSidebar();
        history.pushState({}, '', `/privia/c/${encodeURIComponent(id)}`);
        try {
            const result = await api(`/privia/conversations/${encodeURIComponent(id)}`, { signal: state.abortController.signal });
            if (requestId !== state.requestId) return;
            renderMessages(result.messages);
            await loadConversations();
        } catch (error) {
            if (error.name !== 'AbortError') { state.activeId = null; renderWelcome(); }
        }
    }

    function openRenameDialog(id, title) {
        openDialog('rename', id, title);
    }

    function openDeleteDialog(id, title) {
        openDialog('delete', id, title);
    }

    function openDialog(mode, id, title) {
        dialogState.mode = mode;
        dialogState.id = id;
        dialogState.busy = false;
        dialogState.previousFocus = document.activeElement;
        const isDelete = mode === 'delete';
        dialogTitle.textContent = isDelete ? 'Hapus percakapan' : 'Ganti nama percakapan';
        dialogDescription.textContent = isDelete
            ? 'Percakapan dan seluruh pesan di dalamnya akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.'
            : 'Ubah nama percakapan agar lebih mudah ditemukan di riwayat.';
        dialogContext.hidden = !isDelete;
        dialogContext.textContent = isDelete ? `“${title}”` : '';
        dialogInputLabel.hidden = isDelete;
        dialogInput.hidden = isDelete;
        dialogInput.value = isDelete ? '' : title;
        dialogConfirm.textContent = isDelete ? 'Hapus Percakapan' : 'Simpan Perubahan';
        dialogConfirm.classList.toggle('privia-dialog__button--danger', isDelete);
        dialogError.hidden = true;
        dialogError.textContent = '';
        dialog.hidden = false;
        document.body.classList.add('privia-dialog-open');
        requestAnimationFrame(() => (isDelete ? dialogConfirm : dialogInput).focus());
    }

    function closeDialog() {
        if (dialogState.busy) return;
        dialog.hidden = true;
        document.body.classList.remove('privia-dialog-open');
        dialogState.mode = null;
        dialogState.id = null;
        dialogState.previousFocus?.focus?.();
    }

    function setDialogBusy(isBusy) {
        dialogState.busy = isBusy;
        dialogInput.disabled = isBusy;
        dialogCancel.disabled = isBusy;
        dialogClose.disabled = isBusy;
        dialogConfirm.disabled = isBusy;
        dialogConfirm.textContent = isBusy
            ? (dialogState.mode === 'delete' ? 'Menghapus...' : 'Menyimpan...')
            : (dialogState.mode === 'delete' ? 'Hapus Percakapan' : 'Simpan Perubahan');
    }

    async function submitDialog() {
        if (dialogState.busy || !dialogState.mode) return;
        const { mode, id } = dialogState;
        const title = dialogInput.value.trim();
        if (mode === 'rename' && !title) {
            dialogError.textContent = 'Nama percakapan tidak boleh kosong.';
            dialogError.hidden = false;
            dialogInput.focus();
            return;
        }

        setDialogBusy(true);
        dialogError.hidden = true;
        try {
            if (mode === 'rename') {
                await api(`/privia/conversations/${id}`, { method: 'PATCH', body: JSON.stringify({ title }) });
            } else {
                await api(`/privia/conversations/${id}`, { method: 'DELETE' });
                if (String(state.activeId) === String(id)) {
                    state.activeId = null;
                    history.pushState({}, '', '/privia');
                    renderWelcome();
                }
            }
            setDialogBusy(false);
            closeDialog();
            await loadConversations();
        } catch (error) {
            dialogError.textContent = error.message || (mode === 'delete' ? 'Gagal menghapus percakapan. Silakan coba lagi.' : 'Gagal mengganti nama percakapan. Silakan coba lagi.');
            dialogError.hidden = false;
        } finally {
            if (!dialog.hidden) setDialogBusy(false);
        }
    }

    async function retryMessage(id) {
        if (state.isSending) return;
        state.isSending = true;
        send.disabled = true;
        appendLoading();
        try {
            const result = await api('/privia/messages/retry', { method: 'POST', body: JSON.stringify({ message_id: id }) });
            document.getElementById('privia-loading')?.remove();
            await openConversation(result.conversation.id);
        } catch (error) {
            document.getElementById('privia-loading')?.remove();
            showError(error.message, id);
        } finally { state.isSending = false; send.disabled = false; }
    }

    function showError(message, retryId = null) {
        messages.insertAdjacentHTML('beforeend', `<div class="privia-message"><div class="privia-message__avatar" aria-hidden="true">!</div><div class="privia-message__bubble">${escapeHtml(message)}${retryId ? `<br><button type="button" class="privia-retry" data-retry="${retryId}">Coba Lagi</button>` : ''}</div></div>`);
        messages.querySelector('[data-retry]')?.addEventListener('click', (event) => retryMessage(event.currentTarget.dataset.retry));
        messages.scrollTop = messages.scrollHeight;
    }

    async function sendMessage(event) {
        event?.preventDefault();
        if (state.isSending) return;
        const content = input.value.trim();
        if (!content) return;
        state.isSending = true;
        send.disabled = true;
        input.disabled = true;
        messages.querySelector('.privia-welcome')?.remove();
        messages.insertAdjacentHTML('beforeend', `<article class="privia-message privia-message--user"><div class="privia-message__bubble">${formatText(content)}</div></article>`);
        input.value = '';
        appendLoading();
        try {
            const result = await api('/privia/messages', { method: 'POST', body: JSON.stringify({ message: content, conversation_id: state.activeId ? Number(state.activeId) : null }) });
            document.getElementById('privia-loading')?.remove();
            state.activeId = String(result.conversation.id);
            history.pushState({}, '', `/privia/c/${encodeURIComponent(state.activeId)}`);
            await openConversation(state.activeId);
        } catch (error) {
            document.getElementById('privia-loading')?.remove();
            if (error.body?.conversation_id) state.activeId = String(error.body.conversation_id);
            showError(error.message, error.body?.user_message_id || null);
            await loadConversations();
        } finally {
            state.isSending = false;
            send.disabled = false;
            input.disabled = false;
            input.focus();
        }
    }

    dialogCancel.addEventListener('click', closeDialog);
    dialogClose.addEventListener('click', closeDialog);
    dialogConfirm.addEventListener('click', submitDialog);
    dialog.querySelector('[data-dialog-close]').addEventListener('click', closeDialog);
    dialogInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            submitDialog();
        }
    });
    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeDialog();
            return;
        }
        if (event.key !== 'Tab') return;
        const focusable = [...dialogPanel.querySelectorAll('button:not([disabled]), input:not([disabled])')];
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    document.getElementById('privia-new-chat').addEventListener('click', () => { state.activeId = null; history.pushState({}, '', '/privia'); renderWelcome(); closeSidebar(); });
    document.getElementById('privia-sidebar-open').addEventListener('click', openSidebar);
    document.getElementById('privia-sidebar-close').addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);
    composer.addEventListener('submit', sendMessage);
    input.addEventListener('keydown', (event) => { if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); composer.requestSubmit(); } });
    window.addEventListener('popstate', () => { const match = window.location.pathname.match(/\/privia\/c\/(\d+)/); match ? openConversation(match[1]) : (state.activeId = null, renderWelcome()); });

    (async () => {
        try {
            const conversations = await loadConversations();
            if (state.activeId) await openConversation(state.activeId);
            else if (!conversations.length) renderWelcome();
            else renderWelcome();
        } catch (error) { renderWelcome(); showError(error.message); }
    })();
})();
