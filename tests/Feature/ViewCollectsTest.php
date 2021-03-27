<?php

namespace Tests\Feature;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewCollectsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 默认显示不包含密码的分享集列表
     *
     * @return void
     */

    /** @test */
    public function show_collects_without_password()
    {
        $collects = Collect::factory()->has(Picture::factory()->count(3), 'pictures')->count(10)->create();
        $hasPasswordCollect = Collect::factory()->has(Picture::factory()->count(3), 'pictures')->create(['password' => 'test']);

        $response = $this->get(route('api.collects.index'));
        // 可以看到任意一个不包含密码的分享集
        $response->assertSee(['title' => $collects->random()->title])
            // 无法看到包含密码的分享集
            ->assertDontSee(['title' => $hasPasswordCollect->title]);
    }

    /**
     * 游客可以查看任何未加密的分享集
     *
     * @return void
     */
    /** @test */
    public function guest_can_view_any_without_password_collects()
    {
        $collect = Collect::factory()->has(Picture::factory()->count(3), 'pictures')->create();

        $response = $this->get(route('api.collects.show', $collect));

        $response->assertSee(['title' => $collect->title])
            ->assertJsonCount(3, 'data.pictures');
    }

    /**
     * 游客需要输入密码来查看加密的分享集
     */
    /** @test */
    public function guest_need_to_password_to_view_collect_with_password()
    {
        $collect = Collect::factory()->has(Picture::factory()->count(3), 'pictures')->create(['password' => 'test']);

        $response = $this->get(route('api.collects.show', $collect));

        $response->assertForbidden();

        $response = $this->get(route('api.collects.show', ['collect' => $collect, 'password' => 'test']));
        $response->assertSee(['title' => $collect->title])
            ->assertJsonCount(3, 'data.pictures');
    }
    /**
     * 创建者无需输入密码即可查看自己创建的分享集
     *
     * @return void
     */
    /** @test */
    public function creator_can_view_collect_without_password()
    {
        $user = create(User::class);
        $collect = Collect::factory()
            ->has(Picture::factory()->count(3), 'pictures')
            ->create([
                'user_id' => $user->id,
                'password' => 'test'
            ]);
        $this->actingAs($user);
        $response = $this->get(route('api.collects.show', $collect));

        $response->assertSee(['title' => $collect->title])
            ->assertJsonCount(3, 'data.pictures');
    }
}
