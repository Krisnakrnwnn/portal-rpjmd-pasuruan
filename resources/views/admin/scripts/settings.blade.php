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


  // ===== SETELAN MODEL AI LOGIC =====
  window.selectModel = function(modelName) {
    const input = document.getElementById('gemini_model');
    if (!input) return;
    input.value = modelName;
    input.dispatchEvent(new Event('change', {bubbles:true}));
    document.getElementById('active-model-display').innerText = modelName;
    
    document.querySelectorAll('.model-badge').forEach(btn => {
      if (btn.getAttribute('onclick').includes(`'${modelName}'`)) {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-blue-600 border-blue-600 text-white shadow-md";
      } else {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300";
      }
    });
  };

  document.getElementById('gemini_model')?.addEventListener('input', function(e) {
    const val = e.target.value.trim();
    document.getElementById('active-model-display').innerText = val ? val : 'Tidak diset';
    
    document.querySelectorAll('.model-badge').forEach(btn => {
      if (btn.getAttribute('onclick').includes(`'${val}'`)) {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-blue-600 border-blue-600 text-white shadow-md";
      } else {
        btn.className = "model-badge px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300";
      }
    });
  });


</script>
