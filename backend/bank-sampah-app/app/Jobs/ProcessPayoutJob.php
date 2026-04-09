<?php

namespace App\Jobs;

use App\Services\PembayaranService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPayoutJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $pembayaranId,
        private readonly ?int $actorUserId = null,
    ) {}

    public function handle(PembayaranService $pembayaranService): void
    {
        $pembayaranService->processPayoutForPembayaran($this->pembayaranId, $this->actorUserId);
    }
}
