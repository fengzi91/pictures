<?php
namespace App\Services;


use App\Http\Resources\CollectResource;
use App\Models\Collect;
use App\Contracts\CollectContract;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CollectService implements CollectContract
{

    public function getList($request)
    {
        $query = Collect::query();
        if ($keyword = $request->keyword) {
            $query->where('title', 'LIKE', "%{$keyword}%");
        }

        if ($type = $request->input('type', false)) {
            if ($type === 'liked') {
                $filter = $request->input('filter');
                $user_id = isset($filter['user_id']) ? $filter['user_id'] : Auth::id();
                $query->whereHas('likers', function($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            }
            $queryBuilder = QueryBuilder::for($query);
        } else {
            $queryBuilder = QueryBuilder::for($query)->allowedFilters(AllowedFilter::exact('user_id'));
        }
        $collects = $queryBuilder->allowedIncludes('pictures')
            ->allowedSorts(['created_at', 'thumb_up', 'view_counts'])
            ->with('user')
            ->withCount('likers')
            ->has('pictures')
            ->whereNull('password')
            ->paginate(10);
        $result = CollectResource::collection($collects);
        $result->additional($this->checkIsLiked($collects));
        return $result;
    }

    public function checkIsLiked($collects)
    {
        if (!Auth::check()) {
            return ['liked' => []];
        }
        $liked = Auth::user()->isLikedByCache($collects->pluck('id')->toArray(), 'collect');
        $additional['liked'] = $this->likedIdToUuid($liked, $collects);
        return $additional;
    }

    public function likedIdToUuid($likedIds, $collects)
    {
        return $collects->map(function($collect) use ($likedIds) {
            if (in_array($collect->id, $likedIds)) {
                return [$collect->link => true];
            }
            return null;
        })->filter()->values();
    }
}
