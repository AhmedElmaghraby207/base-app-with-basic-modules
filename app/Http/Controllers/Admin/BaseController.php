<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    function __construct()
    {
        $this->middleware(['admin.auth', 'web'], ['except' => [
            'home',
            'get_login',
            'post_login',
            'getForgotPassword',
            'postForgotPassword',
            'getResetPassword',
            'postResetPassword',
        ]]);
    }
}