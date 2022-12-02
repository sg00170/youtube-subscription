<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 마이 페이지.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        
        return view('user.index', compact('user'));
    }
}
