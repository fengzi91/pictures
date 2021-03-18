<?php

namespace App\Http\Controllers;

use App\Http\Resources\PictureCollection;
use App\Http\Resources\PictureResource;
use App\Models\Picture;
use Illuminate\Http\Request;
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
        if ($picture->where('url', $request->url)->exists()) {
            return ['download' => true];
        }
        return ['download' => false];
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
            return PictureResource::collection($data);
    }

    public function all(Picture $picture)
    {
        return $picture->all(['url', 'width', 'height', 'title']);
    }
}
