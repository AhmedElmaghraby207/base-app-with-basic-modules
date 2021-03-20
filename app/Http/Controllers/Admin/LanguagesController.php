<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Session;

class LanguagesController extends AdminController
{
    public function changeLanguage($lang)
    {
        if ($lang == 'en' || $lang == 'ar') {
            if (!Session::has('language')) {
                Session::put('language', $lang);
            } else {
                Session::put('language', $lang);
            }
            return redirect()->back();
        } else {
            abort(400);
        }
    }
}