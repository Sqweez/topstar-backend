<?php

namespace App\Http\Controllers\mobile\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {
        return $request->all();
    }

    public function me() {

    }
}
