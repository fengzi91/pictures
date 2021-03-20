<?php

namespace App\Http\Requests\Api\Collect;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|between:5,32',
            'password' => 'nullable|string|min:4,max:12',
            'pictures' => 'required_if:title,null|array',
            'pictures.*' => 'integer|exists:pictures,id'
        ];
    }

    public function messages()
    {
        return [
          'pictures.required_if' => '分享集必须包含图片或者填写标题'
        ];
    }
}
