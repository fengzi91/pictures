<?php

namespace Tests\Feature;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeCollectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 用户可以对分享集点赞.
     *
     * @return void
     */
    /** @test */
    public function user_can_toggle_liked_collects()
    {
        $collect = Collect::factory()->has(Picture::factory())->create();

        $user = create(User::class);
        $this->actingAs($user);
        // dd(route('api.collects.like', ['collect' => $collect->link]));
        $response = $this->post(route('api.collects.like', ['collect' => $collect]));

        $response->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('liked', true);
        $this->assertTrue($user->hasLiked($collect));
        $this->assertTrue($collect->isLikedBy($user));

        // 再次执行点赞
        $response = $this->post(route('api.collects.like', ['collect' => $collect]));

        $response->assertStatus(200)
            ->assertJsonPath('count', 0)
            ->assertJsonPath('liked', false);
        $this->assertFalse($user->hasLiked($collect));
        $this->assertFalse($collect->isLikedBy($user));
    }
}
