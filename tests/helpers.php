<?php

function create($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return $class::factory()->count($times)->make($attributes);
}

/**
 * 清空 meilisearch 的文档
 * @param $model \Illuminate\Database\Eloquent\Model | string
 */
function refreshMeilisearch($model)
{
    // dump();
    Artisan::call('scout:flush', ['model' => (new \ReflectionClass($model))->getName()]);
}

/**
 * 清空 redis 数据库
 */
function refreshRedis()
{
    Illuminate\Support\Facades\Redis::command('FLUSHDB');
}
