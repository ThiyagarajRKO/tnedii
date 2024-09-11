<?php

namespace Impiger\ACL\Http\Requests;

use Impiger\Support\Http\Requests\Request;

class RoleCreateRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        /* @Customized By Sabari shankar Parthiban Start */
        $roles = new \Impiger\ACL\Models\Role;
        $roles = ($this->roles) ? $roles ::find($this->roles) : $this;
        /* @Customized By Sabari shankar Parthiban End */
        return [
            /* @Customized By Sabari shankar Parthiban Start */
//            'name'        => 'required|max:60|min:3',
            // 'entity_type' => 'sometimes|required',
            // 'entity_id' => 'sometimes|required',
            'name' => \App\Utils\CrudHelper::customValidationRules("required|max:60|min:3|unique:roles,name,entity_type,entity_id,deleted_at", $roles),
            /* @Customized By Sabari shankar Parthiban End */
            'description' => 'required|max:255',
        ];
    }

}
