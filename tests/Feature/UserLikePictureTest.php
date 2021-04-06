<?php

namespace Tests\Feature;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLikePictureTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function user_can_toggle_liked_pictures()
    {
        refreshRedis();
        $user = create(User::class);
        $picture = create(Picture::class);

        $this->actingAs($user);
        $response = $this->post(route('api.pictures.like', ['picture' => $picture]));

        $response->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('liked', true);

        $this->assertTrue($user->hasLiked($picture));
        $this->assertTrue($picture->isLikedBy($user));

        // 再次执行点赞
        $response = $this->post(route('api.pictures.like', ['picture' => $picture]));

        $response->assertStatus(200)
            ->assertJsonPath('count', 0)
            ->assertJsonPath('liked', false);
        $this->assertFalse($user->hasLiked($picture));
        $this->assertFalse($picture->isLikedBy($user));
        refreshRedis();
    }

    /** @test */
    public function user_can_get_pictures_with_is_liked()
    {
        refreshRedis();
        $pictures = create(Picture::class, [], 10);
        $user = create(User::class);
        $this->signIn($user);
        $response = $this->get(route('api.pictures.index'));
        $response->assertJsonFragment(['liked' => []]);

        // 随机点赞数据
        $likedPictures = $pictures->random(3);

        $likedPictures->each(function($picture) use($user) {
            $user->like($picture);
        });

        $response = $this->get(route('api.pictures.index'));
        $responseLiked = [];
        foreach ($response->json('liked') as $liked) {
            $responseLiked[] = key($liked);
        }
        $likedPictures->each(function($picture) use ($responseLiked) {
            $this->assertTrue(in_array($picture->link, $responseLiked));
        });
        // 元素个数要相同
        $this->assertEquals(count($likedPictures), count($responseLiked));
        refreshRedis();
    }
}
