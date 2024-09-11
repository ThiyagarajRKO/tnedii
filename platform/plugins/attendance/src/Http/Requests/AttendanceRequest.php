<?php

namespace Impiger\Attendance\Http\Requests;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AttendanceRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        $attendance = new \Impiger\Attendance\Models\Attendance;
		$attendance = ($this->attendance) ? $attendance::find($this->attendance) : $this;
        $validationRules = [
            'attendance_date' => 'required|date|before_or_equals:today'
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
