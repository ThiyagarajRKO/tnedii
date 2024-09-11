<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function refreshCaptcha()
    {
        return response()->json(['customcaptcha' => customcaptcha_img(CUSTOM_CAPTCHA_CONFIG)]);
    }
}