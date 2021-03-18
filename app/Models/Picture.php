<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
use Spatie\Tags\HasTags;

class Picture extends Model
{
    use HasFactory, HasTags, Searchable;
    protected $fillable = ['title', 'tag', 'url', 'path', 'width', 'height'];

    protected $appends = ['url'];
    public function getUrlAttribute()
    {
        return $this->attributes['url']
            ?? Storage::disk($this->pictureDisk())->url($this->path);
    }

    public static function pictureDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
    }

    public function toSearchableArray()
    {
        $data = $this->toArray();
        $this->loadMissing('tags');
        $data['tag_name'] = $this->tags->map(function($tag) {
          return $tag->name;
        })->toArray();
        $data['tag_id'] = $this->tags->map(function($tag) {
            return $tag->id;
        })->toArray();
        return $data;
    }
}
