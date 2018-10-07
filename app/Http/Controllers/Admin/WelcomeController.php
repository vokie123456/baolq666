<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/5/2
 * Time: 上午12:33
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class WelcomeController extends BaseController
{
    public function Index(Request $request)
    {
        return view('admin.welcome.index', $this->outData);
    }

    public function Welcome()
    {
        return view('admin.welcome.welcome');
    }
}