<?php

namespace App\Listeners;

use App\Events\PembayaranVerified;
use App\Jobs\ProcessPayoutJob;
use Illuminate\Support\Facades\Log;

class QueuePayoutForPembayaran
{
    public function handle(PembayaranVerified $event): void
    {
        ProcessPayoutJob::dispatch($event->pembayaranId, $event->actorUserId);
        Log::info('Payout queued', [
            'pembayaran_id' => $event->pembayaranId,
            'queued_by' => $event->actorUserId,
        ]);
    }
}
