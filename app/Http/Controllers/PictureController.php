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
        $filter = $request->input('filter', []);
//        $filters = collect($filter)->map(function($item) {
//            return ['tag:' . $item];
//        });
            $pictures = $picture->search($keyword, function(Indexes $meilisearch, $query, $options) {
//                $options['facetsDistribution'] = ['tag'];
//                if ($filters->count() > 0) {
//                    $options['facetFilters'] = $filters;
//                }
                return $meilisearch->search($query, $options);
            });
            $data = $pictures->paginate(40);
            return PictureResource::collection($data);
//        } else {
//            $pictures = $picture->query()->where('id', '>', 100)->oldest()->paginate(40);
//        }
        // return PictureResource::collection($pictures);
    }

    public function all(Picture $picture)
    {
        return $picture->all(['url', 'width', 'height', 'title']);
    }
}
