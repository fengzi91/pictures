<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function index(Request $request, Tag $tag)
    {
        $tags = Cache::remember('tags', 60 * 60 * 12 , function () {
            return Tag::where('type', 'picture')->get()->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            });
        });
        // 把 count 填充进去

        $tags = $tags->map(function($tagData) use ($tag) {
            $tagData['count'] = $tag->getCountById($tagData['id']);
            return $tagData;
        });
        return TagResource::collection($tags);
    }

}
