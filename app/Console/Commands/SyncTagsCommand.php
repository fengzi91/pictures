<?php

namespace App\Console\Commands;

use App\Models\Picture;
use App\Models\Tag;
use Illuminate\Console\Command;


class SyncTagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zt:sync-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步 tags 图片';

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
        Picture::query()->whereDoesntHave('tags')->chunkById(100, function($pictures) {
           foreach($pictures as $picture) {
               $picture->syncTagsWithType([$picture->tag], 'picture');
               Tag::findOrCreate($picture->tag, 'picture')->countIncrement();
           }
           $this->info(sprintf('完成第 %d 到第 %d 的图片标签同步', $pictures->first()->id, $pictures->last()->id));
        });
    }
}
