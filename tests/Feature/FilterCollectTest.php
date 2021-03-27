<?php

namespace Tests\Feature;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterCollectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 查看用户的分享集
     *
     * @return void
     */
    /** @test */
    public function anyone_can_see_collects_by_user()
    {
        $collects = Collect::factory()->count(10)->has(Picture::factory()->count(3))->create();

        $user = create(User::class);
        $userCollects = Collect::factory()->count(10)->has(Picture::factory()->count(3))->create(['user_id' => $user->id]);

        $response = $this->get(route('api.collects.index', ['filter[user_id]' => $user->id]));

        $response->assertSee(['title' => $userCollects->random()->title])
            ->assertDontSee($collects->random()->title);
    }

    /**
     * 指定用户赞过的分享集
     *
     * @return void
     */
    /** @test */
    public function anyone_can_filter_collect_by_user_liked()
    {
        $collects = Collect::factory()->count(10)->has(Picture::factory()->count(3))->create();
        $user = create(User::class);
        $likedCollects = tap($collects->random(3), function($collects) use ($user) {
            $collects->each(function($collect) use ($user) {
                $user->like($collect);
            });
        });
        $url = route('api.collects.index', ['type' => 'liked', 'filter' => ['user_id' => $user->id]]);

        $response = $this->get($url);
        $response->assertSee(['title' => $likedCollects->random()->title])
            ->assertJsonCount(3, 'data');
    }
    /**
     * 用户可以查看自己点赞的分享集
     *
     * @return void
     */
    /** @test */
    public function user_can_filter_collect_by_type_liked()
    {
        $collects = Collect::factory()->count(10)->has(Picture::factory()->count(3))->create();
        $user = create(User::class);
        $likedCollects = tap($collects->random(3), function($collects) use ($user) {
            $collects->each(function($collect) use ($user) {
                $user->like($collect);
            });
        });
        $url = route('api.collects.index', ['type' => 'liked']);
        $this->actingAs($user);
        $response = $this->get($url);
        $response->assertSee(['title' => $likedCollects->random()->title])
            ->assertJsonCount(3, 'data');
    }
}
