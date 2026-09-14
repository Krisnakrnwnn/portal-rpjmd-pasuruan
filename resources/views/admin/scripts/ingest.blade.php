<script>
  // ===== INGEST CHATBOT LOGIC =====
  const ingestForm = document.getElementById('ingest-form');
  const pdfInput = document.getElementById('pdf_file');
  const filePreview = document.getElementById('file-name-preview');
  const ingestProgressCard = document.getElementById('ingest-progress-card');
  const btnIngest = document.getElementById('btn-start-ingest');
  const btnText = document.getElementById('btn-text');
  const spinner = btnIngest ? btnIngest.querySelector('.spinner') : null;
  let pollingInterval = null;
  let activeIngestionId = null;

  pdfInput?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      filePreview.classList.remove('hidden');
      filePreview.querySelector('span').textContent = this.files[0].name;
    }
  });

  ingestForm?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    if (btnIngest) {
      btnIngest.disabled = true;
      btnIngest.classList.add('loading');
    }
    if (spinner) spinner.classList.remove('hidden');
    if (btnText) btnText.innerText = 'Memulai...';

    try {
      const response = await fetch("{{ route('admin.chatbot.ingest') }}", {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
      const data = await response.json();
      if (data.success) {
        startPolling(data.ingestion_id, formData.get('pdf_file').name);
        Swal.fire({ icon: 'success', title: 'Ingest Dimulai', text: data.message, timer: 2000, showConfirmButton: false });
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      Swal.fire('Error', error.message || 'Gagal memulai ingest', 'error');
      resetIngestButton();
    }
  });

  function resetIngestButton() {
    if (btnIngest) {
        btnIngest.disabled = false;
        btnIngest.classList.remove('loading');
        if (spinner) spinner.classList.add('hidden');
        if (btnText) btnText.innerText = 'Mulai Proses Ingest';
    }
  }

  function startPolling(id, fileName) {
    if (ingestProgressCard) ingestProgressCard.classList.remove('hidden');
    const fileTitle = document.getElementById('ingest-file-title');
    if (fileTitle) fileTitle.textContent = fileName;
    if (btnIngest) btnIngest.disabled = true;
    
    activeIngestionId = id;
    if (pollingInterval) clearInterval(pollingInterval);
    
    pollingInterval = setInterval(async () => {
      try {
        const res = await fetch(`/admin/chatbot/ingest-status/${id}`);
        const data = await res.json();
        
        updateIngestUI(data);
        
        if (data.status === 'completed' || data.status === 'failed' || data.status === 'cancelled') {
          clearInterval(pollingInterval);
          if (data.status === 'completed') {
            Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: 'Dokumen berhasil di-ingest sepenuhnya.',
                confirmButtonColor: '#2563eb'
            }).then(() => {
                location.assign({{ Illuminate\Support\Js::from(route('admin.ingest.index')) }});
            });
          } else if (data.status === 'cancelled') {
            Swal.fire('Batal', 'Proses ingest telah dibatalkan.', 'info').then(() => {
                location.assign({{ Illuminate\Support\Js::from(route('admin.ingest.index')) }});
            });
          } else {
            Swal.fire('Gagal', 'Terjadi kesalahan: ' + data.error, 'error');
            resetIngestButton();
          }
        }
      } catch (e) {
        console.error('Polling error:', e);
      }
    }, 2000);
  }

  window.cancelActiveIngestion = async function() {
    if (!activeIngestionId) return;
    
    const confirmCancel = await Swal.fire({
      title: 'Batalkan Ingest?',
      text: 'Proses pemrosesan dokumen PDF akan dihentikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Batalkan!',
      cancelButtonText: 'Tidak'
    });

    if (confirmCancel.isConfirmed) {
      try {
        const response = await fetch(`/admin/chatbot/ingest/${activeIngestionId}/cancel`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        });
        const data = await response.json();
        if (data.success) {
          if (pollingInterval) clearInterval(pollingInterval);
          Swal.fire('Dibatalkan', data.message, 'success').then(() => {
            location.assign({{ Illuminate\Support\Js::from(route('admin.ingest.index')) }});
          });
        } else {
          Swal.fire('Gagal', data.message, 'error');
        }
      } catch (error) {
        console.error('Error canceling ingest:', error);
        Swal.fire('Error', 'Gagal membatalkan proses ingest.', 'error');
      }
    }
  };

  window.deleteIngestion = async function(id, fileName) {
    const confirmDelete = await Swal.fire({
      title: 'Hapus Dokumen?',
      text: `Apakah Anda yakin ingin menghapus dokumen "${fileName}"? AI tidak akan lagi menjawab pertanyaan dari file ini.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    });

    if (confirmDelete.isConfirmed) {
      try {
        const response = await fetch(`/admin/chatbot/ingest/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        });
        const data = await response.json();
        if (data.success) {
          Swal.fire('Terhapus!', data.message, 'success').then(() => {
            location.assign({{ Illuminate\Support\Js::from(route('admin.ingest.index')) }});
          });
        } else {
          Swal.fire('Gagal', data.message, 'error');
        }
      } catch (error) {
        console.error('Error deleting ingestion:', error);
        Swal.fire('Error', 'Gagal menghapus dokumen.', 'error');
      }
    }
  };

  function updateIngestUI(data) {
    const bar = document.getElementById('ingest-progress-bar');
    const pct = document.getElementById('ingest-percentage');
    const info = document.getElementById('ingest-pages-info');
    const statusText = document.getElementById('ingest-status-text');
    const eta = document.getElementById('ingest-eta');

    bar.style.width = data.progress + '%';
    pct.textContent = data.progress + '%';
    info.textContent = `${data.processed} / ${data.total} Halaman`;
    
    if (data.status === 'processing') {
      statusText.innerHTML = `
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
        </span>
        Sedang memproses halaman ${data.processed + 1}...
      `;
      
      if (data.estimated_seconds > 0) {
        const mins = Math.floor(data.estimated_seconds / 60);
        const secs = data.estimated_seconds % 60;
        eta.textContent = mins > 0 ? `± ${mins}m ${secs}d` : `± ${secs} detik`;
      } else {
        eta.textContent = 'Menghitung...';
      }
    } else if (data.status === 'completed') {
      statusText.textContent = '✅ Selesai!';
      statusText.className = 'text-sm text-green-600 font-bold';
      eta.textContent = 'Selesai';
    } else if (data.status === 'failed') {
      statusText.textContent = '❌ Gagal';
      statusText.className = 'text-sm text-red-600 font-bold';
      eta.textContent = '-';
    }
  }


@if($activeIngestion)
startPolling(@js($activeIngestion->id), @js($activeIngestion->original_name));
@endif
window.addEventListener('pagehide', () => clearInterval(pollingInterval));

</script>
