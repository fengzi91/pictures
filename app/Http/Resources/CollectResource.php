<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        unset($data['id']);
        return $data;
    }
}
