<?php

namespace Tests\Feature;

use App\Models\Flower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlowersTest extends TestCase
{
    use RefreshDatabase;

    private $BASE_URI = '/api/flowers';

    public function test_get_flowers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->BASE_URI);

        $response->assertOk();
    }

    public function test_get_flower(): void
    {
        $user = User::factory()->create();
        $flower = Flower::factory()->create();

        $response = $this->actingAs($user)->get($this->BASE_URI.'/'.$flower->id);

        $response->assertOk();
    }
}
