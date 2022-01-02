<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TgServiceController extends Controller
{
    public function message(Request $request)
    {
        var_dump($request->all());
        die();
    }
}
