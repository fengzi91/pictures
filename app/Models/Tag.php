<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Redis;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel
{
    use HasFactory;

    protected $hash_prefix = 'tag_counts';
    protected $field_prefix = 'tag_';

    public function countIncrement()
    {
        // 获取今日 Redis 哈希表名称
        $hash = $this->getHashPrefix();

        // 字段名称，如：tag_1
        $field = $this->getHashField();

        // 当前统计值，如果存在就自增，否则就为数据库中的值
        $count = Redis::hGet($hash, $field);
        if (!$count) {
            $count = $this->attributes['count'];
        }
        $count++;
        // 数据写入 Redis，字段已存在会被更新
        Redis::hSet($hash, $field, $count);
    }

    public function syncCountsToCache($count = null)
    {
        // 获取昨日的哈希表名称
        $hash = $this->getHashPrefix();

        $field = $this->getHashField();
        if (!$count) {
            $count = $this->count;
        }
        Redis::hSet($hash, $field, $count);
    }

    public function getCountAttribute()
    {
        $hash = $this->getHashPrefix();
        $field = $this->getHashField();

        $count = Redis::hGet($hash, $field) ? : 0;

        return $count;
    }
    // 通过 ID 直接从缓存中取统计
    public function getCountById($id)
    {
        $hash = $this->getHashPrefix();
        $field = $this->getHashField($id);

        return (int) Redis::hGet($hash, $field) ? : 0;
    }
    public function getHashPrefix()
    {
        return $this->hash_prefix;
    }

    public function getHashField($id = null)
    {
        // 字段名称，如：tag_1
        $id = $id ?: $this->id;
        return $this->field_prefix . $id;
    }
}
