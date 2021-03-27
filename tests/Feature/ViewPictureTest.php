<?php

namespace Tests\Feature;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use MeiliSearch\Client;
use Tests\TestCase;

class ViewPictureTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function show_index_pictures_list()
    {
        refreshMeilisearch(Picture::class);
        // 创建一条数据
        $picture = create(Picture::class);

        $response = $this->waitForPendingUpdates(function () {
            return $this->get(route('api.pictures.index'));
        });

        $response->assertJsonPath('meta.current_page', 1)
            ->assertSee(['title' => $picture->title])
            ->assertJsonPath('meta.total', 1);
        refreshMeilisearch(Picture::class);
    }
    /**
     * 搜索图片
     *
     * @return void
     */
    /** @test */
    public function search_all_pictures()
    {
        refreshMeilisearch(Picture::class);
        // 创建一条数据
        create(Picture::class, [], 10);
        $picture2 = create(Picture::class, ['title' => 'test search123 keyword']);
        $response = $this->waitForPendingUpdates(function () {
            return $this->get(route('api.pictures.index', ['keyword' => 'search123']));
        });

        $response->assertJsonPath('meta.current_page', 1)
            ->assertSee(['title' => $picture2->title])
            ->assertJsonPath('meta.total', 1);
        refreshMeilisearch(Picture::class);
    }
    /**
     * 显示一张图片的详情
     *
     * @return void
     */
    /** @test */
    public function show_single_picture()
    {
        $picture = create(Picture::class);
        $response = $this->get(route('api.pictures.show', $picture->uuid));
        $response->assertJsonPath('data.title', $picture->title);
        refreshMeilisearch(Picture::class);
    }

    /**
     * 用户上传的图片列表
     *
     * @return void
     */
    /** @test */
    public function show_user_pictures_list()
    {
        $user = create(User::class);
        $picture = create(Picture::class, ['user_id' => $user->id]);

        $this->get(route('api.user.pictures.index', ['user' => $user->uuid]))
            ->assertSee(['title' => $picture->title]);
        refreshMeilisearch(Picture::class);
    }

    /**
     * 搜索用户上传的图片
     *
     * @return void
     */
    /** @test */
    public function search_user_pictures()
    {
        refreshMeilisearch(Picture::class);

        $user = create(User::class);
        create(Picture::class, [], 10);
        $pictures = create(Picture::class, ['user_id' => $user->id, 'title' => 'search123'], 10);

        // 等待搜索更新完成
        $response = $this->waitForPendingUpdates(function () use ($user, $pictures) {
            return $this->get(route('api.user.pictures.index', ['user' => $user->uuid, 'keyword' => $pictures->first()->title]));
        });

        $response->assertJsonPath('meta.total', 10)
            ->assertSee(['title' => $pictures->first()->title]);
        refreshMeilisearch(Picture::class);
    }

    protected function waitForPendingUpdates($callback)
    {
        $index = resolve(Client::class)->index('pictures');
        $pendingUpdates = $index->getAllUpdateStatus();

        foreach ($pendingUpdates as $pendingUpdate) {
            if ('processed' !== $pendingUpdate['status']) {
                $index->waitForPendingUpdate($pendingUpdate['updateId']);
            }
        }

        return $callback();
    }
}
