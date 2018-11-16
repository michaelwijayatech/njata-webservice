<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function get(){
        $data = [
            'id' => '01',
            'name' => 'test',
        ];

        return response()->json($data);
    }

    public function post(){
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request    = json_decode($postdata);
            $t = $request->title;
            $b = $request->body;
        }

        $data = [
            'id' => 'ID_' . $t . '_new',
            'name' => 'NAME_' . $b,
        ];

        return response()->json($data);
    }
}
