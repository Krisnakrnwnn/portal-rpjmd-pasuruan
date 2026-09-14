# Runbook OTP Email Administrator

## Tujuan

Panduan operasional untuk gangguan pengiriman OTP dan pemulihan akses administrator Portal RPJMD.

## Pemeriksaan awal

1. Pastikan aplikasi, database, cache, dan waktu server sehat.
2. Periksa konfigurasi mail Laravel tanpa menampilkan nilai credential.
3. Verifikasi domain pengirim, status penyedia email, kuota, reputasi, serta folder spam penerima.
4. Periksa event `otp_delivery_failed` dan `otp_resend_rate_limited` pada `admin_auth_events`.
5. Periksa log aplikasi. Log OTP tidak boleh berisi kode, password, isi session, atau secret SMTP/API.
6. Uji pengiriman pada staging menggunakan akun administrator khusus pengujian.

## Gangguan pengiriman

- Jika hanya satu akun terdampak, pastikan email akun benar dan masih dapat diakses melalui prosedur verifikasi identitas resmi.
- Jika banyak akun terdampak, eskalasi ke pengelola layanan email dan hentikan percobaan berulang yang dapat memperburuk rate limit.
- Jangan memberikan OTP melalui kanal lain dan jangan mengubah email akun tanpa verifikasi manual oleh pihak berwenang.
- Setelah layanan pulih, minta administrator login kembali agar memperoleh challenge baru.

## Rollback terkontrol

`ADMIN_OTP_ENABLED=false` hanya boleh digunakan sebagai tindakan insiden yang disetujui pemilik sistem. Catat alasan, pemberi persetujuan, waktu mulai, dan batas waktu pemulihan. Perubahan harus dilakukan melalui secret/environment management, diikuti refresh konfigurasi aplikasi. Aktifkan kembali OTP sesegera mungkin.

Saat bypass aktif, login yang berhasil dicatat sebagai event `otp_feature_bypassed`. Pantau event ini dan investigasi setiap penggunaan di luar jendela insiden.

## Pemulihan akses akun

1. Verifikasi identitas administrator melalui prosedur organisasi di luar aplikasi.
2. Super Admin berwenang memperbarui akun hanya setelah verifikasi tersebut selesai.
3. Jangan pernah meminta password atau OTP lama pengguna.
4. Catat perubahan akun melalui audit administratif.
5. Minta pengguna login ulang dan konfirmasi OTP diterima pada alamat baru.

## Pemeriksaan setelah pemulihan

1. Pastikan OTP baru dapat dikirim dan kode lama ditolak.
2. Pastikan route admin tetap tidak dapat diakses sebelum OTP berhasil.
3. Pastikan scheduler menjalankan `admin-otp:prune` setiap hari.
4. Tinjau metrik waktu kirim, kegagalan teknis, penyelesaian challenge, dan event bypass.
5. Dokumentasikan akar masalah serta tindakan pencegahan.
