<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_displayed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SKILLPATH');
    }
}
