<?php

namespace App\Events;

class PembayaranVerified
{
    public function __construct(
        public readonly int $pembayaranId,
        public readonly ?int $actorUserId = null,
    ) {}
}
