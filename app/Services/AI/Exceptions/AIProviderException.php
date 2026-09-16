<?php

namespace App\Services\AI\Exceptions;

use RuntimeException;

class AIProviderException extends RuntimeException
{
    // Never attach the original exception: it may contain credentials or prompts.
    public function __construct(public readonly string $category, public readonly int $status = 500)
    {
        parent::__construct('Layanan AI belum dapat memproses permintaan.');
    }

    public function previewStatus(): int
    {
        return match ($this->category) {
            'configuration' => 503,
            'connection' => 504,
            'invalid_response' => 502,
            default => match ($this->status) {
                429 => 429,
                503 => 503,
                default => 502,
            },
        };
    }

    public function previewMessage(): string
    {
        return match ($this->category) {
            'configuration' => 'Konfigurasi layanan AI belum siap. Hubungi pengelola server.',
            'connection' => 'Koneksi AI terputus atau melewati batas waktu. Silakan coba lagi.',
            'invalid_response' => 'Model tidak memberikan jawaban teks yang dapat digunakan. Pengujian belum berhasil.',
            default => match ($this->status) {
                429 => 'Kuota atau batas permintaan AI tercapai. Silakan coba lagi nanti.',
                503 => 'Layanan AI sedang sibuk. Silakan coba lagi nanti.',
                400, 401, 403, 404 => 'Model tidak tersedia atau akses layanan ditolak. Hubungi pengelola server.',
                default => 'Pengujian model belum berhasil. Silakan coba lagi nanti.',
            },
        };
    }
}
