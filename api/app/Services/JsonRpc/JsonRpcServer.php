<?php

namespace App\Services\JsonRpc;

use App\Exceptions\JsonRpcException;
use Illuminate\Http\Request;

class JsonRpcServer
{
    public function handle(Request $request)
    {
        $content = \json_decode($request->getContent(), true);

        if (empty($content)) {
            throw new JsonRpcException('Parse error', JsonRpcException::PARSE_ERROR);
        }

        try {
            list($controllerName, $action) = explode('.', $content['method']);

            $controller = sprintf('%s\%s%s', 'App\Http\Controllers', ucfirst($controllerName), 'Controller');

            if (!class_exists($controller)) {
                return Response::error('Class not exist', $content['id']);
            }

            $result = (new $controller)->{$action}(...[$content['params']]);
            return Response::success($result, $content['id']);
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }
}
