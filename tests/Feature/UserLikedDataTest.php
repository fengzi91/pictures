<?php

namespace Tests\Feature;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLikedDataTest extends TestCase
{
    use RefreshDatabase;
    /**
     * 检查点赞后的数据是否在缓存中.
     *
     * @return void
     */

    /** @test */
    public function check_liked_in_cache()
    {
        refreshRedis();
        $collect = Collect::factory()->has(Picture::factory())->create();

        $user = create(User::class);
        // 清理缓存数据
        $user->refreshLiked();
        $result = $user->isLikedByCache([$collect->id], $type = 'collect');

        $this->assertCount(0, $result, '分享集在点赞数据缓存中 ->' . count($result) . 'user_id => ' . $user->id);

        $user->like($collect);

        $result = $user->isLikedByCache([$collect->id], $type = 'collect');
        $this->assertCount(1, $result, '分享集 id 没有正确放入点赞缓存中');
        // 取消赞
        $user->unlike($collect);
        $result = $user->isLikedByCache([$collect->id], $type = 'collect');
        $this->assertCount(0, $result, '分享集 id 没有从点赞缓存中移除');
        refreshRedis();
    }
}
