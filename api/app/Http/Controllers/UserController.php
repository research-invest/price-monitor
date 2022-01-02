<?php

namespace App\Http\Controllers;

use App\Http\Methods\v1\UserMethods;

class UserController extends Controller
{
    public function create($params): array
    {
        return UserMethods::create($params);
    }
}
