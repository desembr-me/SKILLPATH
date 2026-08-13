<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviewer_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'reviewer',
            'name' => 'Website Reviewer',
        ]);

        $response = $this->actingAs($user)->get(route('reviewer.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Reviewer');
    }
}
