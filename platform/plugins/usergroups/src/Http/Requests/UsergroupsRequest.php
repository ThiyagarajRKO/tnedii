<?php

namespace Impiger\Usergroups\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UsergroupsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required|unique:usergroups,name,'.$this->usergroups.',id,deleted_at,NULL',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return ['name.required'=>'The user group name field is required',
	'name.unique'=>'The user group name  has already been taken.'
	
            ];
    }
}
