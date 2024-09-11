<?php

namespace Impiger\Language\Http\Requests;

use Impiger\Support\Http\Requests\Request;

class ThemeTranslationRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'locale'       => 'required',
            'translations' => 'required|array',
        ];
    }
}
