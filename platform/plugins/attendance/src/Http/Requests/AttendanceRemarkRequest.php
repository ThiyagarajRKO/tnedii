<?php

namespace Impiger\Attendance\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AttendanceRemarkRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $attendanceRemark = new \Impiger\Attendance\Models\AttendanceRemark;
		$attendanceRemark = ($this->attendanceRemark) ? $attendanceRemark::find($this->attendanceRemark) : $this;
        $validationRules = [
            'training_title_id'=>'required',
	'entrepreneur_id'=>'required',
	'remark'=>'required',
	
            
        ];
        
        
        return $validationRules;
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            ];
    }
}
