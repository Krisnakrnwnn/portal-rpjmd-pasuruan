<?php

namespace App\Services;

use App\Models\AdminAuthEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthSecurityEventRecorder
{
    public function record(
        Request $request,
        string $event,
        string $outcome,
        ?User $user = null,
        array $metadata = [],
    ): void {
        try {
            AdminAuthEvent::create([
                'user_id' => $user?->id,
                'event' => $event,
                'outcome' => $outcome,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 512),
                'metadata' => $metadata === [] ? null : $metadata,
            ]);
        } catch (Throwable $exception) {
            // Authentication and logout must remain available when audit storage is degraded.
            Log::warning('Penyimpanan audit autentikasi tidak tersedia.', [
                'event' => $event,
                'outcome' => $outcome,
                'user_id' => $user?->id,
                'exception_type' => $exception::class,
            ]);
        }
    }
}
