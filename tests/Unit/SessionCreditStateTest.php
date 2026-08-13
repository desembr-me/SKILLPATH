<?php

namespace Tests\Unit;

use App\Models\SessionCredit;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SessionCreditStateTest extends TestCase
{
    public function test_available_credit_respects_expiration(): void
    {
        $active = new SessionCredit(['status' => 'available', 'expires_at' => Carbon::now()->addDay()]);
        $expired = new SessionCredit(['status' => 'available', 'expires_at' => Carbon::now()->subDay()]);
        $used = new SessionCredit(['status' => 'used', 'expires_at' => Carbon::now()->addDay()]);

        $this->assertTrue($active->isAvailable());
        $this->assertFalse($expired->isAvailable());
        $this->assertFalse($used->isAvailable());
    }
}
