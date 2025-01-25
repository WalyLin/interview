<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    protected function success($msg = '操作成功', $data = [], $code = 200)
    {

        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $msg,
            'data' => $data
        ]);
    }

    protected function error($msg = 'fail', $code = 500, $data = [])
    {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $msg,
            'data' => []
        ]);
    }
}
