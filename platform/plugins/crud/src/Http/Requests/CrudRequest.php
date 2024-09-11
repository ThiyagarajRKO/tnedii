<?php

namespace Impiger\Crud\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CrudRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name'   => 'required',
            // 'status' => Rule::in(BaseStatusEnum::values()),
            // 'module_name'    =>'required|alpha|min:2|unique:cruds',
            // 'module_title'    =>'required',
            // 'module_note'    =>'required',
            // 'module_db'        =>'required',
        ];
    }
}
