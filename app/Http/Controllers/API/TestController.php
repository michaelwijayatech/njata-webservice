<?php

namespace App\Http\Controllers\API;

use App\Model\Haid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function get(){
//        $data = [
//            'id' => '01',
//            'name' => 'test',
//        ];

        $data = [];

        $start_date = '25-02-2019';
        $end_date = '09-03-2019';
        $empl_id = 'EMPLO20190204101805';

        $_table = new Haid();
        $_haids = DB::select(DB::raw("SELECT `id` as total FROM $_table->BASETABLE
                                    WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
                                    AND id_employee = '$empl_id' 
                                    AND is_active = $_table->STATUS_ACTIVE"));


        foreach ($_haids as $haids => $haid) {
            array_push($data, $haid->total);
        }

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
