import './style.css'

// =================================================
// Integrasi Global Widget Chatbot RPJMD Pasuruan AI
// =================================================
document.addEventListener("DOMContentLoaded", () => {
   const chatbotHTML = `
      <div id="ai-chatbot-widget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
        
        <!-- Jendela Chat (Disembunyikan secara default) -->
        <div id="chat-window" class="hidden w-80 sm:w-96 md:w-[400px] bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-gray-100 mb-4 overflow-hidden flex-col h-[500px] transform transition-all origin-bottom-right">
          
          <!-- Header Chat -->
          <div class="bg-gradient-to-r from-blue-700 to-[#041a42] p-5 flex justify-between items-center text-white shrink-0">
            <div class="flex items-center gap-3">
              <div class="relative">
                <div class="w-10 h-10 rounded-full ring-2 ring-yellow-400/50 bg-white/10 flex items-center justify-center overflow-hidden">
                  <svg class="w-6 h-6 text-yellow-300 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-blue-900 rounded-full animate-pulse"></span>
              </div>
              <div>
                <h4 class="font-bold text-base tracking-wide flex items-center gap-2">
                  RPJMD Pasuruan AI 
                  <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </h4>
                <p class="text-xs text-blue-200 font-medium opacity-90">Asisten Virtual RPJMD Kota Pasuruan</p>
              </div>
            </div>
            <button id="close-chat" class="text-blue-200 hover:text-white bg-white/5 hover:bg-white/20 p-2 rounded-full transition-colors cursor-pointer">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
 
          <!-- Tampilan Chat Messages -->
          <div class="flex-1 p-5 bg-[#F8FAFC] overflow-y-auto flex flex-col gap-4 bg-[url('https://www.transparenttextures.com/patterns/absurdity.png')]">
            
            <div class="flex justify-center mb-2">
              <span class="px-3 py-1 bg-gray-200/60 text-gray-500 rounded-full text-[10px] font-bold tracking-widest uppercase">Hari ini</span>
            </div>
 
            <!-- Robot Bubble -->
            <div class="flex justify-start group">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1">
                <span class="text-blue-600 font-bold text-xs">AI</span>
              </div>
              <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-3.5 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">
                Halo warga! 🙏 <br>Saya asisten AI Layanan Informasi RPJMD Kota Pasuruan. Ada yang ingin Anda ketahui tentang program prioritas, capaian pembangunan, atau dokumen RPJMD 2025-2029?
              </div>
            </div>
 
            <!-- User Bubble (Pertanyaan Simulasi User) -->
            <div class="flex justify-end">
              <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 max-w-[80%] shadow-sm font-medium leading-relaxed text-sm">
                Bagaimana cara mengakses dokumen RPJMD Kota Pasuruan 2025-2029?
              </div>
            </div>
 
            <!-- Animasi Sedang Mengetik (Typing indicator) -->
            <div class="flex justify-start">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mr-2 mt-1">
                <span class="text-blue-600 font-bold text-xs">AI</span>
              </div>
              <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-5 py-4 shadow-sm flex items-center gap-1.5">
                <div class="w-2.5 h-2.5 bg-blue-400 rounded-full animate-bounce"></div>
                <div class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                <div class="w-2.5 h-2.5 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
              </div>
            </div>
 
          </div>
 
          <!-- Area Input Chat -->
          <div class="p-4 bg-white border-t border-gray-100 flex items-center gap-3 shrink-0">
            <button class="p-2 text-gray-400 hover:text-blue-600 transition-colors bg-gray-50 rounded-full shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            </button>
            <input type="text" placeholder="Tanyakan seputar RPJMD Kota Pasuruan..." class="flex-1 bg-gray-50 border border-gray-200 rounded-full px-5 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
            <button class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 shadow-md hover:shadow-lg transition-transform hover:-translate-y-0.5 shrink-0 focus:outline-none">
              <svg class="w-5 h-5 translate-x-[-1px] translate-y-[1px]" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
            </button>
          </div>
 
        </div>
 
        <!-- Tombol Buka Tutup Chat (Widget Indikator) -->
        <button id="chat-toggle" class="group relative w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-800 rounded-full shadow-[0_10px_25px_rgba(37,99,235,0.5)] border-4 border-white/20 flex items-center justify-center text-white hover:scale-110 transition-all duration-300 focus:outline-none cursor-pointer mt-4">
          <svg class="w-8 h-8 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
          
          <!-- Red dot counter alert -->
          <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 border-2 border-white rounded-full flex items-center justify-center animate-bounce"></span>
        </button>
        
      </div>
    `;

   // Menanamkan elemen HTML chatbot ini langsung ke ujung tag <body> 
   document.body.insertAdjacentHTML('beforeend', chatbotHTML);

   // Logika Fungsional Vanilla JS Buka/Tutup
   const chatToggle = document.getElementById('chat-toggle');
   const chatWindow = document.getElementById('chat-window');
   const closeChat = document.getElementById('close-chat');

   function handleToggle() {
     if (chatWindow.classList.contains('hidden')) {
       // Operasi Buka
       chatWindow.classList.remove('hidden');
       chatWindow.classList.add('flex');
       chatWindow.animate([
         { opacity: 0, transform: 'scale(0.8) translateY(20px)' },
         { opacity: 1, transform: 'scale(1) translateY(0)' }
       ], { duration: 300, easing: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)' });
       
       // Sembunyikan dan kecilkan bola tombol chat agar fokus
       chatToggle.classList.add('scale-75', 'opacity-60');
     } else {
       // Operasi Tutup
       chatWindow.animate([
         { opacity: 1, transform: 'scale(1) translateY(0)' },
         { opacity: 0, transform: 'scale(0.8) translateY(20px)' }
       ], { duration: 200 }).onfinish = () => {
         chatWindow.classList.add('hidden');
         chatWindow.classList.remove('flex');
       };

       // Kembalikan bola tombol chat menjadi ukuran penuh
       chatToggle.classList.remove('scale-75', 'opacity-60');
     }
   }

   chatToggle.addEventListener('click', handleToggle);
   closeChat.addEventListener('click', handleToggle);
});// Tidak ada innerHTML di sini lagi, kita menggunakan file HTML terpisah.

