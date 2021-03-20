<?php

namespace Tests\Feature;

use App\Models\Collect;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class CreateCollectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 标题必须在 5 - 32 个字符之间
     *
     * @return void
     */

    /** @test */
    public function title_length()
    {
        $this->signIn()->withExceptionHandling();
        $collect = make(Collect::class, ['title' => '1234']);

        $response = $this->postJson(route('api.collects.store'), $collect->toArray());

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');

        $collect->title = Str::random(33);
        $response = $this->postJson(route('api.collects.store'), $collect->toArray());

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
        $collect->title = Str::random(5);
        $response = $this->postJson(route('api.collects.store'), $collect->toArray());

        $response->assertStatus(201);
        $response->assertJsonMissingValidationErrors('title');
    }
    /**
     * 图片必须在图片表中
     *
     * @return void
     */
    /** @test */
    public function picture_id_is_existed()
    {
        $this->signIn()->withExceptionHandling();
        $collect = make(Collect::class, ['pictures' => [999999]])->toArray();
        $response = $this->postJson(route('api.collects.store'), $collect);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('pictures.0');
    }

    /** @test */
    public function title_or_pictures_is_required()
    {
        $this->signIn()->withExceptionHandling();
        $collect = make(Collect::class, ['title' => null])->toArray();
        $response = $this->postJson(route('api.collects.store'), $collect);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('pictures');
    }
    /**
     * 用户可以创建分享集.
     *
     * @return void
     */

    /** @test */
    public function can_create_collect()
    {
        $pictureUser = create(User::class);
        $pictures = Picture::factory()->for($pictureUser)->count(10)->create();
        $user = create(User::class);
        $collect = make(Collect::class, ['user_id' => $user->id]);

        $createData = [
            'title' => $collect->title,
            'pictures' => $pictures->map(function($picture) {
                return $picture->id;
            })->toArray()
        ];

        $response = $this->postJson(route('api.collects.store'), $createData);
        $response->assertUnauthorized();

        $this->actingAs($user);
        $response = $this->postJson(route('api.collects.store'), $createData);
        $response->assertStatus(201);

        $this->assertEquals($user->collects->count(), 1);
        $this->assertEquals($user->collects->first()->title, $collect->title);

    }
}
