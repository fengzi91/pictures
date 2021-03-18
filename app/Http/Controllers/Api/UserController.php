<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectResource;
use App\Http\Resources\UserResource;
use App\Models\Collect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'photo' => ['nullable', 'image', 'max:1024'],
            'introduction' => ['nullable', 'string', 'max:100']
        ])->validate();

        if ($request->hasFile('photo')) {
            $request->user()->updateProfilePhoto($request->file('photo'));
        }
        $request->user()->forceFill([
            'introduction' => $request->input('introduction')
        ])->save();
        return UserResource::make($request->user());
    }

    /**
     * 获取用户分享集
     * @param Request $request
     */
    public function collect(Request $request)
    {
        $query = Collect::where('user_id', $request->user()->id);
        $collects = QueryBuilder::for($query)
            ->allowedIncludes('pictures')
            ->paginate(10);
        return CollectResource::collection($collects);
    }
}
