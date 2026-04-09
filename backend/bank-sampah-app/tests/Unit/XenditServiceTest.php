<?php

namespace Tests\Unit;

use App\Services\XenditService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class XenditServiceTest extends TestCase
{
    public function test_create_payout_sends_payload_and_headers(): void
    {
        Config::set('services.xendit.secret_key', 'test-api-key');
        Config::set('services.xendit.base_url', 'https://example.test');

        Http::fake([
            'https://example.test/v2/payouts*' => Http::response([
                'id' => 'disb-571f3644d2b4edf0745e9703',
                'status' => 'ACCEPTED',
            ], 200),
        ]);

        $service = $this->app->make(XenditService::class);
        $response = $service->createPayout(
            referenceId: 'payout-1',
            channelCode: 'ID_BCA',
            accountNumber: '000000000099',
            accountHolderName: 'Michael Chen',
            amount: 10000,
            currency: 'IDR',
            description: 'July payout',
            idempotencyKey: 'payout-1',
        );

        $this->assertSame('disb-571f3644d2b4edf0745e9703', $response['id']);

        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->url() === 'https://example.test/v2/payouts'
                && $request->hasHeader('Idempotency-key', 'payout-1')
                && ($payload['reference_id'] ?? null) === 'payout-1'
                && ($payload['channel_code'] ?? null) === 'ID_BCA'
                && ($payload['channel_properties']['account_number'] ?? null) === '000000000099'
                && ($payload['channel_properties']['account_holder_name'] ?? null) === 'Michael Chen'
                && (float) ($payload['amount'] ?? 0) === 10000.0
                && ($payload['currency'] ?? null) === 'IDR';
        });
    }

    public function test_get_balance_with_query(): void
    {
        Config::set('services.xendit.secret_key', 'test-api-key');
        Config::set('services.xendit.base_url', 'https://example.test');

        Http::fake([
            'https://example.test/balance*' => Http::response([
                'balance' => 1241231,
            ], 200),
        ]);

        $service = $this->app->make(XenditService::class);
        $response = $service->getBalance([
            'account_type' => 'CASH',
            'currency' => 'IDR',
        ]);

        $this->assertSame(1241231, $response['balance']);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://example.test/balance?account_type=CASH&currency=IDR';
        });
    }
}
