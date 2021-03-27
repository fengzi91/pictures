<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PictureResource;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Http\Request;
use MeiliSearch\Endpoints\Indexes;

class PictureController extends Controller
{

    public function index(Request $request, User $user)
    {
        $keyword = $request->input('keyword', '');
        if ($keyword) {
            $query = Picture::search($keyword, function(Indexes $meilisearch, $query, $options)  use ($user) {
                $options['filters'] = 'user_id = ' . $user->id;
                return $meilisearch->search($query, $options);
            });
        } else {
            $query = Picture::where('user_id', $user->id);
        }
        $pictures = $query->paginate();
        return PictureResource::collection($pictures);
    }
}
