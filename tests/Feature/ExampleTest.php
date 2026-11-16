<?php

namespace Tests\Feature;

use App\Models\SubscriptionPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_displays_saved_subscription_package_details(): void
    {
        SubscriptionPackage::create([
            'nama_package' => 'Paket Pelajar',
            'description' => 'Akses semua bacaan pilihan',
            'price' => 125000,
            'color' => '#2F6B4F',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Paket Pelajar')
            ->assertSee('Akses semua bacaan pilihan')
            ->assertSee('125.000,00')
            ->assertSee('background: linear-gradient(135deg, #ffffff, #2F6B4F 100%)', false);
    }
}
