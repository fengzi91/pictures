<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PictureResource;
use App\Models\Picture;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request, Picture $picture)
    {
        $request->validate([
            'file' => 'required|image'
        ]);
        $file_path = $request->file('file')->storePublicly(
            'pictures', ['disk' => Picture::pictureDisk()]
        );
        [$width, $height] = getimagesize($request->file('file')->getPathname());
        $picture->height = $height;
        $picture->width = $width;
        $picture->path = $file_path;
        $picture->tag = '测试';
        $picture->user_id = $request->user()->id;
        $picture->save();
        return PictureResource::make($picture);
    }
}
