<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PictureCollection;
use App\Http\Resources\PictureResource;
use App\Models\Picture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use MeiliSearch\Endpoints\Indexes;

class PictureController extends Controller
{
    public function store(Request $request, Picture $picture)
    {
        if(Picture::where(['url' => $request->url])->exists()) {
            return response()->noContent();
        }
        $picture->fill($request->all());
        $picture->save();
        return response()->noContent(201);
    }

    public function show(Request $request, Picture $picture)
    {
        $picture->loadCount('likers');
        $picture->isLiked = false;
        if (Auth::check()) {
            $picture->isLiked = Auth::user()->hasLiked($picture);
        }
        return PictureResource::make($picture);
    }

    public function index(Request $request, Picture $picture)
    {
        $keyword = $request->input('keyword', '');
        $tag = $request->input('tag', 0);
        $filters = null;
        if ($tag > 0) {
            $filters .= 'tag_id = ' . $tag;
        }
        $pictures = $picture->search($keyword, function(Indexes $meilisearch, $query, $options)  use ($filters) {
            if ($filters) {
                $options['filters'] = $filters;
            }
            return $meilisearch->search($query, $options);
        });
        $data = $pictures->paginate(40);
        $additional = [];
        if (!Auth::check()) {
            $additional['liked'] = [];
        } else {
            $liked = Auth::user()->isLikedByCache($data->pluck('id')->toArray(), Picture::LIKE_TYPE_NAME);
            $additional['liked'] = $this->likedIdToUuid($liked, $data);
        }

        return PictureResource::collection($data)->additional($additional);
    }

    public function all(Picture $picture)
    {
        return $picture->all(['url', 'width', 'height', 'title']);
    }

    public function like(Picture $picture, Request $request)
    {
        $user = $request->user();
        if ($liked = $user->hasLiked($picture)) {
            $user->unlike($picture);
        } else {
            $user->like($picture);
        }
        return response(['count' => $picture->likers()->count(), 'liked' => !$liked]);
    }

    protected function likedIdToUuid($likedIds, $data)
    {
        return $data->map(function($picture) use ($likedIds) {
            if (in_array($picture->id, $likedIds)) {
                return [$picture->link => true];
            }
            return null;
        })->filter()->values();
    }
}
