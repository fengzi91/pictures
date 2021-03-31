<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
use Overtrue\LaravelLike\Traits\Likeable;
use Ramsey\Uuid\Uuid;
use Spatie\Tags\HasTags;

class Picture extends Model
{
    use HasFactory, HasTags, Searchable,Likeable;

    const LIKE_TYPE_NAME = 'picture';

    protected $fillable = ['title', 'url', 'path', 'width', 'height'];

    protected $appends = ['url'];

    protected static function booted()
    {
        static::creating(function($collect) {
            $collect->uuid = Uuid::uuid4();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
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
