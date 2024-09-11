<?php

namespace Impiger\Workflows\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class WorkflowPermissionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
