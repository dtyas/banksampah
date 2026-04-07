<?php

namespace Tests\Unit;

use App\Models\Sampah;
use App\Models\Transaksi;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_order_calculates_totals(): void
    {
        $sampahA = new Sampah(['id' => 1, 'harga_per_kg' => 2000]);
        $sampahA->id = 1;
        $sampahB = new Sampah(['id' => 2, 'harga_per_kg' => 3000]);
        $sampahB->id = 2;

        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $repository
            ->shouldReceive('findSampahByIds')
            ->once()
            ->with([1, 2])
            ->andReturn(new Collection([$sampahA, $sampahB]));

        $repository
            ->shouldReceive('storeOrder')
            ->once()
            ->andReturn(new Transaksi());

        $service = new OrderService($repository);

        $result = $service->storeOrder([
            'user_id' => 1,
            'nasabah_id' => 1,
            'tanggal' => '2026-04-08',
            'diskon_persen' => 10,
            'items' => [
                ['sampah_id' => 1, 'berat' => 2],
                ['sampah_id' => 2, 'berat' => 3],
            ],
        ]);

        $this->assertSame(5.0, $result['ringkasan']['total_berat']);
        $this->assertSame(13000.0, $result['ringkasan']['total_harga_sebelum_diskon']);
        $this->assertSame(10.0, $result['ringkasan']['diskon_persen']);
        $this->assertSame(11700.0, $result['ringkasan']['total_harga_setelah_diskon']);
    }
}
