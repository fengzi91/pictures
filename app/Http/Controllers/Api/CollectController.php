<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectResource;
use App\Models\Collect;
use Illuminate\Http\Request;
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
        $collects = QueryBuilder::for($query)
            ->allowedIncludes('pictures')
            ->allowedFilters(AllowedFilter::exact('user_id'))
            ->allowedSorts(['created_at', 'thumb_up', 'view_counts'])
            ->with('user')
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
    public function store(Request $request, Collect $collect)
    {
        $request->validate($this->rules());
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
        $collect->loadMissing('pictures');
        return CollectResource::make($collect);
    }

    public function checkPassword(Collect $collect, Request $request)
    {
        if ($request->input('password', false) && $collect->password === $request->input('password')) {
            return response(204);
        }
        return abort(422,'密码错误');
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
            'pictures' => 'array',
            'pictures.*' => 'integer'
        ];
    }
}
