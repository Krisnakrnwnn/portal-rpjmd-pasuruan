<script>
  // ===== PROFILE EDITOR: character counters =====
  function updateCounter(editorId, counterId) {
    const el = document.getElementById(editorId);
    const counter = document.getElementById(counterId);
    if (el && counter) {
      counter.textContent = el.value.length + ' karakter';
      el.addEventListener('input', () => counter.textContent = el.value.length + ' karakter');
    }
  }
  updateCounter('editor-sejarah', 'count-sejarah');
  updateCounter('editor-visi', 'count-visi');
  updateCounter('editor-misi', 'count-misi');

  const misiEl = document.getElementById('editor-misi');
  const misiButirEl = document.getElementById('count-misi-butir');
  if (misiEl && misiButirEl) {
    function updateMisiButir() {
      const butir = misiEl.value.split('|').filter(s => s.trim().length > 0).length;
      misiButirEl.textContent = butir + ' butir misi terdeteksi';
    }
    updateMisiButir();
    misiEl.addEventListener('input', updateMisiButir);
  }


  // Candidate changes never update the server-rendered active model label.
  (() => {
    const form = document.getElementById('ai-settings-form');
    if (!form) return;
    const select = form.querySelector('#gemini_model');
    const button = form.querySelector('#test-ai-model');
    const result = form.querySelector('#ai-test-result');
    let sequence = 0;
    let pending;
    function cancel() {
      sequence++;
      pending?.abort();
      button.disabled = false;
      button.textContent = 'Test Model';
      result.removeAttribute('aria-busy');
    }
    select.addEventListener('change', () => { cancel(); result.textContent = ''; });
    form.addEventListener('submit', cancel);
    button.addEventListener('click', async () => {
      if (!select.reportValidity()) return;
      cancel();
      const current = sequence;
      const model = select.value;
      const label = select.selectedOptions[0].textContent;
      const provider = form.elements.provider.value;
      const heading = 'Provider diuji: Google Gemini\nModel diuji: ' + label + ' (' + model + ')\n';
      pending = new AbortController();
      button.disabled = true;
      button.textContent = 'Menguji...';
      result.setAttribute('aria-busy', 'true');
      result.textContent = heading + 'Menghubungi model...';
      try {
        const response = await fetch(form.dataset.testUrl, {
          method: 'POST',
          headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': form.elements._token.value},
          body: JSON.stringify({provider, gemini_model: model}), signal: pending.signal,
        });
        if (response.redirected || !response.headers.get('content-type')?.includes('application/json')) throw new Error('Invalid response');
        const data = await response.json();
        if (current !== sequence || select.value !== model) return;
        if (response.status === 422) {
          result.textContent = heading + 'Pilih provider dan model yang tersedia dalam daftar.';
        } else if (response.status === 429 && typeof data.reply !== 'string') {
          result.textContent = heading + 'Batas pengujian tercapai. Tunggu sebentar lalu coba lagi.';
        } else if (typeof data.reply === 'string' && data.model === model && data.provider === provider) {
          result.textContent = heading + (response.ok && data.success ? 'Pengujian berhasil. Model aktif belum diubah.\n' : 'Pengujian belum berhasil.\n') + data.reply;
        } else { throw new Error('Invalid response'); }
      } catch (error) {
        if (current === sequence && error.name !== 'AbortError') result.textContent = heading + 'Pengujian gagal. Periksa koneksi atau masuk kembali, lalu coba lagi.';
      } finally {
        if (current === sequence) { button.disabled = false; button.textContent = 'Test Model'; result.removeAttribute('aria-busy'); }
      }
    });
  })();
</script>
