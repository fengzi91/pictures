<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Collect\CreateRequest;
use App\Http\Resources\CollectResource;
use App\Models\Collect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;


class CollectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index(Request $request)
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

        return CollectResource::collection($collects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CollectResource|\Illuminate\Http\Response
     */
    public function store(CreateRequest $request, Collect $collect)
    {
        $collect->fill($request->all());
        $collect->user_id = $request->user()->id;
        $collect->save();
        $collect->pictures()->sync($request->pictures);
        $collect->loadMissing('pictures');
        return CollectResource::make($collect);
    }

    /**
     * Display the specified resource.
     *
     * @param  Collect  $collect
     * @return CollectResource|\Illuminate\Http\Response
     */
    public function show(Request $request, Collect $collect)
    {
        $this->authorize('view', $collect);
        $collect->loadMissing(['pictures', 'user']);
        return CollectResource::make($collect);
    }

    public function checkPassword(Collect $collect, Request $request)
    {
        $request->validate([
            'password' => 'required|in:' . $collect->password,
        ], ['password.in' => '密码不正确']);
        return response('', 204);
    }

    public function like(Collect $collect, Request $request)
    {
        $user = $request->user();
        if ($liked = $user->hasLiked($collect)) {
            $user->unlike($collect);
        } else {
            $user->like($collect);
        }
        return response(['count' => $collect->likers()->count(), 'liked' => !$liked]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return CollectResource|\Illuminate\Http\Response
     */
    public function update(Request $request, Collect $collect)
    {
        $this->authorize('update', $collect);
        $request->validate($this->rules());
        $collect->fill($request->all());
        $collect->save();
        $collect->pictures()->sync($request->pictures);
        $collect->load('pictures');
        return CollectResource::make($collect);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function rules()
    {
        return [
            'title' => 'nullable|string|between:5,32',
            'password' => 'nullable|string|min:4,max:12',
            'pictures' => 'required_if:title,null|array',
            'pictures.*' => 'integer|exists:pictures,id'
        ];
    }
}
