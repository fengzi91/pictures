<?php

namespace Tests\Feature;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowCollectPasswordIfCreatorTest extends TestCase
{
    use RefreshDatabase;
    /**
     *  只有专辑创建者请求接口时返回明文密码
     *
     * @return void
     */
    /** @test */
    public function show_collect_password_only_creator()
    {
        $user = create(User::class);
        $password = Str::random(6);
        $collect = Collect::factory()
            ->for($user)
            ->has(Picture::factory()->count(5))->create(['password' => $password]);
        $otherUser = create(User::class);
        $this->signIn($otherUser);
        $response = $this->get(route('api.collects.show', ['collect' => $collect, 'password' => $password]));
        // 普通用户浏览看不到密码
        $response->assertJsonMissing(['data' => 'password']);

        dump($user->id, $collect->user_id);
        // 创建者登录
        $this->signIn($user);

        $response = $this->get(route('api.collects.show', $collect));
        // 创建者浏览可以看到密码
        $response->assertJsonFragment(['password' => $password]);
    }
}
