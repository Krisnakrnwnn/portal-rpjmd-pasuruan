<?php

namespace App\Console\Commands;

use App\Models\AdminLoginOtp;
use Illuminate\Console\Command;

class PruneAdminLoginOtps extends Command
{
    protected $signature = 'admin-otp:prune';

    protected $description = 'Remove expired administrator OTP challenges after the operational retention period';

    public function handle(): int
    {
        $cutoff = now()->subHours(config('admin-auth.otp.retention_hours'));

        $deleted = AdminLoginOtp::query()
            ->where(function ($query) use ($cutoff) {
                $query->where('expires_at', '<', $cutoff)
                    ->orWhere('used_at', '<', $cutoff)
                    ->orWhere('cancelled_at', '<', $cutoff);
            })
            ->delete();

        $this->info("Pruned {$deleted} administrator OTP challenge(s).");

        return self::SUCCESS;
    }
}
