<?php
namespace App\Models\Traits;

use Illuminate\Support\Facades\Redis;

trait HasLikeData {


//    public function isLiked($ids, $)
//    {
//
//    }
    // 缓存 key 前缀
    protected $like_cache_prefix = 'user_like_';

    // 缓存数据过期时间
    protected $like_cache_expire = 60 * 60 * 24;

    protected $liked_model_map = [
        'collect' => \App\Models\Collect::class,
        'picture' => \App\Models\Picture::class,
    ];

    public function isLikedByCache($ids, $type = 'collect')
    {
        $temp_key = 'temp_user_' . $this->id . '_' . $type . '_check';
        Redis::command('SADD', collect([$temp_key])->concat($ids)->toArray());
        $result = Redis::command('SINTER', [$this->likeCacheKey($type), $temp_key]);
        Redis::command('DEL', [$temp_key]);
        return $result;
    }

    public function getLikedData($type = 'collect')
    {
        if ($this->hasCached($type)) {
            return Redis::command('SMEMBERS', [$this->likeCacheKey($type)]);
        }
        return [];
    }

    /**
     * @param array $ids
     * @param string $type
     */
    public function addLikedData($ids, $type = 'collect')
    {
        Redis::command('SADD', collect([$this->likeCacheKey($type)])->concat($ids)->toArray());
        Redis::command('EXPIRE', [$this->likeCacheKey($type), $this->like_cache_expire]);
    }

    public function refreshLiked($type = null)
    {
        if ($type) {
            $this->refreshLikedByType($type);
        } else {
            foreach ($this->liked_model_map as $key => $model) {
                $this->refreshLikedByType($key);
            }
        }
    }

    protected function refreshLikedByType($type)
    {
        $model = $this->liked_model_map[$type];
        $data = $this->likes()->whereHas('likeable', function($q) use ($model) {
            $q->where('likeable_type', $model);
        })->pluck('likeable_id');
        $params = collect([$this->likeCacheKey($type)])->concat($data)->toArray();
        Redis::command('SADD', $params);
        // 设置过期时间
        Redis::command('EXPIRE', [$this->likeCacheKey($type), $this->like_cache_expire]);
        return $data;
    }

    protected function hasCached($type)
    {
        return Redis::command('SCARD', [$this->likeCacheKey($type)]) > 0;
    }

    protected function likeCacheKey($type)
    {
        return $this->like_cache_prefix . $this->id . '_' . $type;
    }
}
