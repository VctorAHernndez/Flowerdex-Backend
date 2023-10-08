<?php

namespace Tests\Feature;

use App\Models\Flower;
use App\Models\FlowerLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlowerLikesTest extends TestCase
{
    use RefreshDatabase;

    private $BASE_URI = '/api/likes';

    public function test_get_likes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->BASE_URI);

        $response->assertOk();
    }

    public function test_add_like(): void
    {
        $user = User::factory()->create();
        $flower = Flower::factory()->create();
    
        $response = $this->actingAs($user)->postJson($this->BASE_URI, ['flower_id' => $flower->id]);

        $response->assertCreated();

        $this->assertDatabaseHas('flower_likes', ['id' => $response['id']]);
    }

    public function test_add_redundant_like(): void
    {
        $user = User::factory()->create();
        $flower = Flower::factory()->create();
        FlowerLike::factory()->create(['flower_id' => $flower->id, 'user_id' => $user->id]);
        $this->assertDatabaseCount('flower_likes', 1);
    
        $response = $this->actingAs($user)->postJson($this->BASE_URI, ['flower_id' => $flower->id]);

        $response->assertConflict();
        $this->assertDatabaseCount('flower_likes', 1);
    }

    public function test_remove_existing_like(): void
    {
        $user = User::factory()->create();
        $flower = Flower::factory()->create();
        $like = FlowerLike::factory()->create(['user_id' => $user->id, 'flower_id' => $flower->id]);

        $response = $this->actingAs($user)->delete($this->BASE_URI.'/'.$like->id);

        $response->assertNoContent();
    }

    public function test_remove_nonexistant_like(): void
    {
        $user = User::factory()->create();
        FlowerLike::truncate();

        $response = $this->actingAs($user)->delete($this->BASE_URI.'/1234-1234-1234-1234');

        $response->assertNotFound();
    }

    public function test_remove_forbidden_like(): void
    {
        $currentUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $flower = Flower::factory()->create();
        $like = FlowerLike::factory()->create(['user_id' => $otherUser->id, 'flower_id' => $flower->id]);

        $response = $this->actingAs($currentUser)->delete($this->BASE_URI.'/'.$like->id);

        $response->assertForbidden();
    }
}
