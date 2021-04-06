<?php

namespace App\Models;

use App\Models\Traits\DateTimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Ramsey\Uuid\Uuid;

class Collect extends Model
{
    use HasFactory,
        DateTimeFormat,
        Likeable;

    const LIKE_TYPE_NAME = 'collect';

    protected $fillable = ['title', 'password'];

    protected $hidden = ['password'];

    protected $appends = ['full_link'];

    protected static function booted()
    {
        static::creating(function($collect) {
            $collect->link = self::createLink();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pictures()
    {
        return $this->belongsToMany(Picture::class);
    }
    public function getFullLinkAttribute()
    {
        return config('app.front_url') . '/collects/' . $this->link;
    }
    public static function createLink()
    {
        return Uuid::uuid4();
    }
}
