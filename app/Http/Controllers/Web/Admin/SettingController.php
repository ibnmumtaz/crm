<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $config = [
            'mail_mailer' => config('mail.default'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ];

        return view('settings.index', compact('config'));
    }
}
