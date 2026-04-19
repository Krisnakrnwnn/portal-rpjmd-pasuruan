import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'path'

export default defineConfig({
  plugins: [
    tailwindcss(),
  ],
  build: {
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'index.html'),
        profil: resolve(__dirname, 'profil.html'),
        berita: resolve(__dirname, 'berita.html'),
        layanan: resolve(__dirname, 'layanan.html'),
        kontak: resolve(__dirname, 'kontak.html'),
        admin: resolve(__dirname, 'admin.html'),
      }
    }
  }
})
