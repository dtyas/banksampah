<?php

namespace App\Services;

use App\Models\AuditTrail;

class AuditTrailService
{
    /**
     * Record immutable audit trail entry for financial and operational events.
     */
    public function record(
        string $action,
        string $entityType,
        ?int $entityId,
        ?int $actorUserId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?float $amount = null,
        ?array $meta = null,
    ): AuditTrail {
        return AuditTrail::query()->create([
            'actor_user_id' => $actorUserId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'amount' => $amount,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
