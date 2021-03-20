<?php

namespace Tests\Feature;

use App\Models\Collect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckCollectPasswordTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 错误的密码报错.
     *
     * @return void
     */

    /** @test */
    public function collect_password_is_not_verified_with_a_invalid_password()
    {
        $collect = create(Collect::class, ['password' => '1234']);

        $response = $this->postJson(route('api.collects.password.check', ['collect' => $collect]), ['password' => '3214']);

        $response->assertJsonValidationErrors('password');
    }

    /** @test */
    public function collect_password_verified_with_a_right_password()
    {
        $collect = create(Collect::class, ['password' => '1234']);

        $response = $this->postJson(route('api.collects.password.check', ['collect' => $collect]), ['password' => '1234']);

        $response->assertStatus(204);
    }
}
