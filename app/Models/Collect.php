<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Collect extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'password'];

    protected $hidden = ['password'];

    protected $appends = ['full_link'];

    protected static function booted()
    {
        static::creating(function($collect) {
            $collect->link = self::createLink();
        });
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
