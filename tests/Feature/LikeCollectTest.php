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

    /**
     * 分享集接口可以正确返回用户是否点赞指定数据
     *
     * @return void
     */
    /** @test */
    public function user_get_collects_with_is_liked()
    {
        refreshRedis();
        $collects = Collect::factory()->has(Picture::factory())->count(10)->create();
        $user = create(User::class);
        $response = $this->get(route('api.collects.index'));
        $response->assertJsonFragment(['liked' => []]);
        // 随机点赞数据
        $likedCollects = $collects->random(3);

        $likedCollects->each(function($collect) use($user) {
            $user->like($collect);
        });

        $this->signIn($user);
        $response = $this->get(route('api.collects.index'));
        $responseLiked = [];
        foreach ($response->json('liked') as $liked) {
            $responseLiked[] = key($liked);
        }
        $likedCollects->each(function($collect) use ($responseLiked) {
            $this->assertTrue(in_array($collect->link, $responseLiked));
        });
        // 元素个数要相同
        $this->assertEquals(count($likedCollects), count($responseLiked));
        refreshRedis();
    }
}
