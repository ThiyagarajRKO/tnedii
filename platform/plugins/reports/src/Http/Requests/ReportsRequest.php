<?php

namespace Impiger\Reports\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ReportsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
