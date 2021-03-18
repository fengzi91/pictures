<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Collect
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $password 密码
 * @property string|null $title 标题
 * @property string $link 链接
 * @property mixed|null $counts 统计字段
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_link
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Picture[] $pictures
 * @property-read int|null $pictures_count
 * @method static \Database\Factories\CollectFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collect query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereCounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collect whereUserId($value)
 */
	class Collect extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Picture
 *
 * @property int $id
 * @property string|null $title
 * @property string $tag
 * @property string|null $url
 * @property int $width
 * @property int $height
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PictureFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Picture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Picture query()
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Picture whereWidth($value)
 */
	class Picture extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

