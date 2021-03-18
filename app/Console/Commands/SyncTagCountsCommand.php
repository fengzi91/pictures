<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTagCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zt:sync-tag-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将标签下内容个数同步到 redis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 更新数据库并同步到 redis hash 表
        Tag::query()->chunk(10, function($tags) {
           foreach ($tags as $tag) {
               $count = DB::table('taggables')->where('tag_id', $tag->id)->count();
               $tag->count = $count;
               $tag->save();
               $tag->syncCountsToCache($count);
           }
        });
    }
}
