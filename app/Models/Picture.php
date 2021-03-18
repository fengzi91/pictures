<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Picture extends Model
{
    use HasFactory, Searchable;
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
}
