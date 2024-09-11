<?php

namespace Impiger\Contact\Http\Requests;

use Impiger\Support\Http\Requests\Request;

class ContactRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function rules()
    {
        
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'content' => 'required',
            'phone' => 'nullable|numeric|digits_between:7,11'
        ];
        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $rules ['g-recaptcha-response'] = 'required|captcha';
        }
        return $rules;
        /* @Customized by Sabari Shankar Parthiban end */
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'    => trans('plugins/contact::contact.form.name.required'),
            'email.required'   => trans('plugins/contact::contact.form.email.required'),
            'email.email'      => trans('plugins/contact::contact.form.email.email'),
            'content.required' => trans('plugins/contact::contact.form.content.required'),
        ];
    }
}
