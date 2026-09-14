<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Verifikasi Login Portal RPJMD</title>
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a">
    <div style="max-width:600px;margin:0 auto;padding:32px 16px">
        <div style="background:#ffffff;border-radius:16px;padding:32px;border:1px solid #dbeafe">
            <p style="margin:0 0 8px;color:#1d4ed8;font-weight:700">Bapperida Kabupaten Pasuruan</p>
            <h1 style="margin:0 0 20px;font-size:24px">Verifikasi login Portal RPJMD</h1>
            <p>Gunakan kode berikut untuk menyelesaikan login administrator:</p>
            <p style="margin:24px 0;padding:18px;background:#eff6ff;border-radius:12px;text-align:center;font-size:34px;letter-spacing:10px;font-weight:700;color:#1e3a8a">{{ $code }}</p>
            <p>Kode ini berlaku selama <strong>{{ config('admin-auth.otp.expires_minutes') }} menit</strong> dan hanya dapat digunakan satu kali.</p>
            <p>Jangan berikan kode ini kepada siapa pun. Jika Anda tidak mencoba login, abaikan email ini dan hubungi pengelola apabila aktivitas tersebut mencurigakan.</p>
            <p style="margin-top:24px;color:#64748b;font-size:13px">Waktu permintaan: {{ $requestedAt }}</p>
            @if (config('admin-auth.support_email'))
                <p style="color:#64748b;font-size:13px">Bantuan: {{ config('admin-auth.support_email') }}</p>
            @endif
        </div>
    </div>
</body>
</html>
