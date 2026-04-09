<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class XenditAuthService
{
    public function request(): PendingRequest
    {
        return Http::withBasicAuth($this->secretKey(), '');
    }

    private function secretKey(): string
    {
        return (string) config('services.xendit.secret_key');
    }
}
