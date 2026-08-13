<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_presents_the_offline_non_academic_class_concept(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('KELAS NONAKADEMIK TATAP MUKA');
        $response->assertSee('Lokasi & jadwal jelas');
        $response->assertDontSee('Live Class');
    }
}
