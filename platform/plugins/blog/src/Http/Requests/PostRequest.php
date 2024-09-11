<?php

namespace Impiger\Blog\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Blog\Supports\PostFormat;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PostRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            /* @Customized By Sabari Shankar Parthiban start
           'name'        => 'required|max:120',*/
            'name'     => "required",
            /* @Customized By Sabari Shankar Parthiban end*/
            'description' => 'max:400',
            'categories'  => 'required',
            'format_type' => Rule::in(array_keys(PostFormat::getPostFormats(true))),
            'status'      => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
