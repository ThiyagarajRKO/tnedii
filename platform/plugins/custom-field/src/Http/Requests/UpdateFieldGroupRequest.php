<?php

namespace Impiger\CustomField\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateFieldGroupRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     */
    public function rules()
    {
        return [
            'order'         => 'integer|min:0|required',
            'rules'         => 'json|required',
            'group_items'   => 'json|required',
            'deleted_items' => 'json|nullable',
            'title'         => 'required|max:255',
            'status'        => ['required', Rule::in(BaseStatusEnum::values())],
        ];
    }
}
