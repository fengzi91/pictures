<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectResource;
use App\Models\Collect;
use Illuminate\Http\Request;

class CollectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CollectResource|\Illuminate\Http\Response
     */
    public function store(Request $request, Collect $collect)
    {
        $request->validate([
            'title' => 'nullable|string|between:5,32',
            'password' => 'nullable|string|min:4,max:12',
            'pictures' => 'array',
            'pictures.*' => 'integer',
        ]);
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
    public function show(Collect $collect)
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
