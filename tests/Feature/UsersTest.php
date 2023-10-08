<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    private $LOGIN_URI = '/api/token';
    private $SIGNUP_URI = '/api/signup';
    private $CURRENT_USER_URI = '/api/user';

    public function test_get_current_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->CURRENT_USER_URI);

        $response->assertOk();
        $response->assertJson(['id' => $user->id]);
    }

    public function test_login(): void
    {
        $FAKE_PASSWORD = '1234';

        $user = User::factory()->create(['password' => Hash::make($FAKE_PASSWORD)]);
    
        $response = $this->postJson($this->LOGIN_URI, ['email' => $user->email, 'password' => $FAKE_PASSWORD]);

        $response->assertOk();
    }

    public function test_login_with_empty_email(): void
    {
        User::factory()->create();
    
        $response = $this->postJson($this->LOGIN_URI, ['email' => '', 'password' => '1234']);

        $response->assertUnprocessable();
    }

    public function test_login_with_invalid_email(): void
    {
        User::factory()->create();
    
        $response = $this->postJson($this->LOGIN_URI, ['email' => 'this-is-not-an-email', 'password' => '1234']);

        $response->assertUnprocessable();
    }

    public function test_login_with_wrong_email(): void
    {
        $FAKE_PASSWORD = '1234';

        User::factory()->create(['email' => 'test@flowerdex.io', 'password' => Hash::make($FAKE_PASSWORD)]);
    
        $response = $this->postJson($this->LOGIN_URI, ['email' => 'wrong@flowerdex.io', 'password' => $FAKE_PASSWORD]);

        $response->assertUnprocessable();
    }

    public function test_login_with_empty_password(): void
    {
        User::factory()->create();
    
        $response = $this->postJson($this->LOGIN_URI, ['email' => 'test@flowerdex.io', 'password' => '']);

        $response->assertUnprocessable();
    }

    public function test_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('1234')]);
    
        $response = $this->postJson($this->LOGIN_URI, ['email' => $user->email, 'password' => '4321']);

        $response->assertUnprocessable();
    }

    public function test_signup(): void
    {
        $preCount = DB::table('users')->count();

        $response = $this->postJson($this->SIGNUP_URI, ['email' => 'test@flowerdex.io', 'password' => '1234', 'name' => 'Test User']);

        $postCount = DB::table('users')->count();
        
        $response->assertCreated();
        $this->assertEquals($preCount + 1, $postCount);
    }
}
