<?php

namespace App\Http\Methods\v1;

use App\Services\JsonRpc\Response;
use Illuminate\Support\Facades\Validator;

class UserMethods
{

    public static function create($params)
    {
        $validator = Validator::make($params, [
            'url' => 'required|url',
            'date' => 'required|integer',
        ]);

        if ($validator->fails() && ($errors = $validator->errors())) {
            return Response::error($errors);
        }


    }
}
