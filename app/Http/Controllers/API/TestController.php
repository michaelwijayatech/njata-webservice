<?php

namespace App\Http\Controllers\API;

use App\Classes\GlobalClass;
use App\Model\Attendance;
use App\Model\Chop;
use App\Model\Employee;
use App\Model\Haid;
use App\Model\Holiday;
use App\Model\Standard;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{
    public function get1(){
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

    public function get(){
        $data = [];
        $_global_class = new GlobalClass();
        $id = 'all';
        $_table = new Employee();
        $_year = date("Y");
        $_month = date("m");
        $start_date = '25-02-2019';
        $end_date = '02-03-2019';
        $_start_date = explode('-', $start_date);
        $_end_date = explode('-', $end_date);
        $_potongan_bpjs = false;

        $_data = [];
        $_chop_date_arr = [];
        $_pokok = 0;
        $_premi = 0;
        $_haid = 0;
        $_tot = 0;
        $_pot_bpjs = 0;
        $_std_harian = 0;
        $_std_haid = 0;
        $_masuk = 0;
        $_setengah_hari = 0;
        $_ijin = 0;
        $_tidak_masuk = 0;

        $_stat_harian_atas = $_table->STATUS_HARIAN_ATAS;
        $_stat_harian_bawah = $_table->STATUS_HARIAN_BAWAH;

        $ctr = 0;

        if (strtolower($id) === "all") {
            $_table = new Employee();
            $_empls = DB::table($_table->BASETABLE)
                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                ->where('status', '=', $_table->STATUS_HARIAN_ATAS)
                ->orWhere('status', '=', $_table->STATUS_HARIAN_BAWAH)
                ->get();
            if (count($_empls) > 0) {
                foreach ($_empls as $empls => $empl) {
                    $empl_id = $empl->id;
                    $empl_name = $empl->first_name . ' ' . $empl->last_name;

                    $_haids = DB::table('haid')
                        ->where('id_employee', '=', $empl_id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->orderBy('date', 'DESC')
                        ->first();
                    $_h_date_res = [];
                    if (!empty($_haids)) {
                        $_h_date = explode("-", $_haids->date);

                        $_haid_date = date('Y-m-d', strtotime($_haids->date));

                        $_haid_start = date('Y-m-d', strtotime($start_date));
                        $_haid_end = date('Y-m-d', strtotime($end_date));

                        $_h_date_res = [];
                        array_push($_h_date_res, $_haids->date);
                        array_push($_h_date_res, $_haid_date);
                        array_push($_h_date_res, $_haid_start);
                        array_push($_h_date_res, $_haid_end);

//                        if (((string)$_h_date[2] === (string)$_start_date[2]) or ((string)$_h_date[2] === (string)$_end_date[2])){
//                            if ((string)$_h_date[1] === (string)$_start_date[1]) {
//                                if ((int)$_h_date[0] >= (int)$_start_date[0]){
//                                    $_h_date_res = $_h_date[0] .  " Start : " . $_h_date[0] . ' - ' . $_h_date[1] . ' - ' . $_h_date[2];
//                                } else {
//                                    $_h_date_res = ["no"];
//                                }
//                            } else if ((string)$_h_date[1] === (string)$_end_date[1]) {
//                                if ((int)$_h_date[0] <= (int)$_start_date[0]){
//                                    $_h_date_res = $_h_date[0] .  " End : " . $_h_date[0] . ' - ' . $_h_date[1] . ' - ' . $_h_date[2];
//                                } else {
//                                    $_h_date_res = ["no"];
//                                }
//                            } else {
//                                $_h_date_res = ["no"];
//                            }
//                        } else {
//                            $_h_date_res = ["no"];
//                        }
                        if (($_haid_date >= $_haid_start) && ($_haid_date <= $_haid_end)){
                            $_h_date_res =  "is between";
                        }else{
                            $_h_date_res = "no";
                        }
                    }
                    $temp = array(
                        "id_employee" => $empl_id,
                        "employee_name" => $empl_name,
                        "date" => $_h_date_res,
//                        "haids" => $_haids,
                    );

                    array_push($_data, $temp);
                }
            }


            return response()->json($_data);
        }
    }

    public function get2(){
        $_global_class = new GlobalClass();
        $id = 'all';
        $_table = new Employee();
        $_year = date("Y");
        $_month = date("m");
        $start_date = '25-02-2019';
        $end_date = '02-03-2019';
        $_start_date = explode('-', $start_date);
        $_end_date = explode('-', $end_date);
        $_potongan_bpjs = false;

        $_data = [];
        $_chop_date_arr = [];
        $_pokok = 0;
        $_premi = 0;
        $_haid = 0;
        $_tot = 0;
        $_pot_bpjs = 0;
        $_std_harian = 0;
        $_std_haid = 0;
        $_masuk = 0;
        $_setengah_hari = 0;
        $_ijin = 0;
        $_tidak_masuk = 0;

        $_stat_harian_atas = $_table->STATUS_HARIAN_ATAS;
        $_stat_harian_bawah = $_table->STATUS_HARIAN_BAWAH;

        if (strtolower($id) === "all") {
            $_table = new Standard();
            $_stds = DB::table($_table->BASETABLE)
                ->where('year', '=', $_year)
                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                ->get();
            if (count($_stds) > 0) {
                foreach ($_stds as $stds => $std) {
                    if ($std->name === "upah_harian"){
                        $_std_harian = $_global_class->removeMoneySeparator($std->nominal);
                    }
                    if ($std->name === "cuti_haid"){
                        $_std_haid = $_global_class->removeMoneySeparator($std->nominal);
                    }
                }
            }

            $_table = new Chop();
            if ($_start_date[1] !== $_end_date[1]){
                $_chops = DB::select(DB::raw("SELECT * FROM $_table->BASETABLE
                                                            WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
                                                            AND is_active = $_table->STATUS_ACTIVE"));
            } else {
                $_chops = DB::table($_table->BASETABLE)
                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->get();
            }
            if (count($_chops) > 0) {
                foreach ($_chops as $chops => $chop) {
                    $chop_number = $chop->number;
                    if ($chop_number === (string)$_table->NUMBER_SINGAPORE){
                        $chop_date = $chop->date;
                        array_push($_chop_date_arr, $chop_date);
                        array_push($_chop_date_arr, $chop_date);
                    } else {
                        $chop_date = $chop->date;
                        array_push($_chop_date_arr, $chop_date);
                    }
                }
            }

            $_table = new Holiday();
            if ($_start_date[1] !== $_end_date[1]){
                $_holidays = DB::select(DB::raw("SELECT COUNT(`id`) as total
                                                FROM $_table->BASETABLE
                                                WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
                                                AND is_active = $_table->STATUS_ACTIVE"));

                foreach ($_holidays as $holidays => $holiday) {
                    $_holiday = $holiday->total;
                }

            } else {
                $_holiday = DB::table($_table->BASETABLE)
                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->count();
            }

            $_table = new Employee();
            $_empls = DB::table($_table->BASETABLE)
                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                ->where('status', '=', $_table->STATUS_HARIAN_ATAS)
                ->orWhere('status', '=', $_table->STATUS_HARIAN_BAWAH)
                ->get();
            if (count($_empls) > 0) {
                foreach ($_empls as $empls => $empl) {
                    $empl_id = $empl->id;
                    $empl_name = $empl->first_name . ' ' . $empl->last_name;
                    $empl_premi = $_global_class->removeMoneySeparator($empl->premi);
                    $empl_stat = $empl->status;
                    $empl_start_work_date = $empl->start_date;
                    $empl_pot_bpjs = $_global_class->removeMoneySeparator($empl->potongan_bpjs);

                    $_table = new Attendance();

                    if ($_start_date[1] !== $_end_date[1]){
                        $_atts = DB::select(DB::raw("SELECT * FROM $_table->BASETABLE
                                                            WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
                                                            AND id_employee = '$empl_id' 
                                                            AND is_active = $_table->STATUS_ACTIVE"));
                    } else {
                        $_atts = DB::table($_table->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('id_employee', '=', $empl_id)
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->get();
                    }
                    if (count($_atts) > 0) {
                        foreach ($_atts as $atts => $att) {
                            $att_stat = $att->status;
                            $att_date = $att->date;
                            if ($att_stat === (string)$_table->STATUS_MASUK){
                                $_pokok += $_std_harian;
                                $_masuk += 1;
//                                        CHECK IF TODAY IS CHOP
                                if ($empl_stat === $_stat_harian_atas){
                                    $_premi += $empl_premi;
                                }
                                if ($empl_stat === $_stat_harian_bawah){
                                    $ctr_temp = 0;
                                    for ($i = 0; $i < count($_chop_date_arr); $i++) {
                                        if ($_chop_date_arr[$i] === $att_date){
                                            $ctr_temp++;
                                        }
                                    }

                                    $_premi += ($empl_premi * $ctr_temp);

//                                            if (in_array($att_date, $_chop_date_arr)){
//                                                $_premi += $empl_premi;
//                                            }
                                }
                            }
                            if ($att_stat === (string)$_table->STATUS_IJIN){
                                $_pokok += $_std_harian;
                                $_premi += 0;
                                $_ijin += 1;
                            }
                            if ($att_stat === (string)$_table->STATUS_SETENGAH_HARI){
                                $_pokok += ($_std_harian / 2);
                                $ctr_temp = 0;
                                for ($i = 0; $i < count($_chop_date_arr); $i++) {
                                    if ($_chop_date_arr[$i] === $att_date){
                                        $ctr_temp++;
                                    }
                                }
                                $_premi += (($empl_premi * $ctr_temp) / 2);
                                $_setengah_hari += 1;
                            }

                            if ($att_stat === (string)$_table->STATUS_TIDAK_MASUK){
                                $_tidak_masuk += 1;
                            }
                        }
                    }
                    // END ATTS

//                            if ($empl_stat === $_stat_harian_atas){
//                                $_premi += ($_masuk * $empl_premi);
//                                $_premi += ($_setengah_hari * ($empl_premi / 2));
//                            }
//                            if ($empl_stat === $_stat_harian_bawah){
//                                $_premi += 999;
//                            }


                    //UPAH LIBUR
                    if ($_global_class->checkDifferenceBetweenTwoDate($empl_start_work_date, date("d-m-Y")) >= 12){
                        $upah_libur = $_holiday * $_std_harian;
                        $_pokok += $upah_libur;
                    }

                    //UPAH HAID
                    $_table = new Haid();
                    $ctr_haid = 0;
                    $_h_date = 0;
                    if ($_start_date[1] !== $_end_date[1]){

                        $_haids = DB::table($_table->BASETABLE)
                            ->where('id_employee', '=', $empl_id)
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->orderBy('date', 'DESC')
                            ->first();

                        if (!empty($_haids)){
                            $_h_date = explode('-',$_haids->date);

                            /**
                             * CHECK YEAR
                             */
                            if (($_h_date[2] === $_start_date[2]) OR ($_h_date[2] === $_end_date[2]) ) {
                                /**
                                 * CHECK FIRST MONTH
                                 */
                                if ($_h_date[1] === $_start_date[1]){
                                    if ($_h_date[0] >= $start_date[0]){
                                        $_haid += $_std_haid;
                                    }
                                }

                                /**
                                 * CHECK SECOND MONTH
                                 */
                                if ($_h_date[1] === $_end_date[1]){
                                    if ($_h_date[0] >= $_end_date[0]){
                                        $_haid += $_std_haid;
                                    }
                                }
                            }
                        }

                    } else {
                        $_haids = DB::table($_table->BASETABLE)
                            ->where('id_employee', '=', $empl_id)
                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->count();
                        if ($_haids > 0){
                            $_haid += $_std_haid;
                        }
                    }


                    if ($_potongan_bpjs){
                        $_pot_bpjs += $empl_pot_bpjs;
                    }

                    $_tot = ($_pokok + $_premi + $_haid) - $_pot_bpjs;

                    $temp = array(
                        "id_employee" => $empl_id,
                        "employee_name" => $empl_name,
                        "pokok" => $_pokok,
                        "haid" => $_haid,
                        "premi" => $_premi,
                        "potongan_bpjs" => $_pot_bpjs,
                        "msit" => $_masuk . ' | ' . $_setengah_hari . ' | ' . $_ijin . ' | ' . $_tidak_masuk,
                        "libur" => $_holiday,
                        "total" => $_tot,
                        "std_harian" => $_std_harian,
                        "std_cuti" => $_std_haid,
                        "rajang" => count($_chop_date_arr),
                        "_rajang" => $_chop_date_arr,
                        "_haid_date" => $_h_date,
                    );

                    array_push($_data, $temp);

                    // RESET VARIABLE
                    $_pokok = 0;
                    $_premi = 0;
                    $_haid = 0;
                    $_tot = 0;
                    $_pot_bpjs = 0;
                    $_masuk = 0;
                    $_setengah_hari = 0;
                    $_ijin = 0;
                    $_tidak_masuk = 0;
                }
            }
            // END EMPLOYEE
        }

        return response()->json($_data);
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

    // Simple table
    public function BasicTable($header, $data)
    {
        // Header
        foreach($header as $col)
//            $fpdf->Cell(40,7,$col,1);
        $this->Ln();
        // Data
        foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(40,6,$col,1);
            $this->Ln();
        }
    }

    public function pdf(){
        date_default_timezone_set("Asia/Jakarta");
        $_global_class = new GlobalClass();
        $_table = null;
        $_date_now = date("d-m-Y");
        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $table = $request->table;
            if (strtolower($table) === "gaji_harian") {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $potongan_bpjs = $request->potongan_bpjs;
                $libur = null;
                $rajang = null;
                $_datas = $request->datas;

                $_total_pokok = 0;
                $_total_premi = 0;
                $_total_haid = 0;
                $_total_bpjs = 0;
                $_total_final = 0;

                $fpdf = new Fpdf('L','mm','A4');
                $fpdf->AddPage();
                $fpdf->SetFont('Arial', 'B', 12);
                $fpdf->Cell(0, 0, 'Gaji Harian');
                $fpdf->Ln(2);
                $fpdf->SetFont('Arial', '', 10);
                $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date);
                $fpdf->Ln(10);

                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(50,7,'Name',1);
                $fpdf->Cell(25,7,'M | S | I | T',1, 0, 'C');
                $fpdf->Cell(30,7,'Pokok',1, 0, 'C');
                $fpdf->Cell(30,7,'Premi',1, 0, 'C');
                $fpdf->Cell(30,7,'Haid',1, 0, 'C');
                $fpdf->Cell(30,7,'BPJS',1, 0, 'C');
                $fpdf->Cell(30,7,'Total',1, 0, 'C');
                $fpdf->Cell(50,7,'Name',1);
                $fpdf->Ln();

                $fpdf->SetFont('Arial', '', 10);

                $datas = explode("@", $_datas);
                for($i=0; $i<count($datas)-1;$i++){
                    $fpdf->Cell(50,5, explode("#", $datas[$i])[1],1);
                    $fpdf->Cell(25,5, explode("#", $datas[$i])[2],1, 0, 'C');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[4],1, 0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[5],1,0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[6],1, 0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[7],1, 0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[10],1,0, 'R');
                    $fpdf->Cell(50,5, explode("#", $datas[$i])[1],1);
                    $fpdf->Ln();

                    $_total_pokok += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[4]);
                    $_total_premi += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[5]);
                    $_total_haid += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[6]);
                    $_total_bpjs += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[7]);
                    $_total_final += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[10]);
                }
                $fpdf->Cell(50,5, "",1);
                $fpdf->Cell(25,5, "",1, 0, 'C');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_pokok, 0),1, 0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_premi, 0),1,0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_haid, 0),1, 0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_bpjs, 0),1, 0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_final, 0),1,0, 'R');
                $fpdf->Cell(50,5, "",1);
                $fpdf->Ln();
                $target_path = base_path('public/pdf/');
                $file_name = $_date_now . '_gaji_harian.pdf';
                $file_path = $target_path . $file_name;
                $fpdf->Output($file_path, 'F');

                $feedback = [
                    "message" => $file_name,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function pdf1(){
        $start_date = '04-03-2019';
        $end_date = '09-03-2019';
        $libur = '1';
        $rajang = '2';

        $fpdf = new Fpdf('L','mm','A4');
        $fpdf->AddPage();
        $fpdf->SetFont('Arial', 'B', 12);
        $fpdf->Cell(0, 0, 'Gaji Harian');
        $fpdf->Ln(2);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date . ' | Libur : ' . $libur . ' | Rajang : ' . $rajang);
        $fpdf->Ln(10);
        $header = array('Name', 'Capital', 'Area (sq km)', 'Pop. (thousands)');
//        foreach($header as $cool)
        $fpdf->SetFont('Arial', 'B', 10);
        $fpdf->Cell(50,7,'Name',1);
        $fpdf->Cell(25,7,'M | S | I | T',1, 0, 'C');
        $fpdf->Cell(30,7,'Pokok',1, 0, 'C');
        $fpdf->Cell(30,7,'Premi',1, 0, 'C');
        $fpdf->Cell(30,7,'Haid',1, 0, 'C');
        $fpdf->Cell(30,7,'BPJS',1, 0, 'C');
        $fpdf->Cell(30,7,'Total',1, 0, 'C');
        $fpdf->Cell(50,7,'Name',1);
        $fpdf->Ln();
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->Cell(50,7,'Faulina Diah Setyani',1);
        $fpdf->Cell(25,7,'6 | 0 | 0 | 0',1, 0, 'C');
        $fpdf->Cell(30,7,'903.600',1, 0, 'R');
        $fpdf->Cell(30,7,'903.600',1,0, 'R');
        $fpdf->Cell(30,7,'903.600',1, 0, 'R');
        $fpdf->Cell(30,7,'903.600',1, 0, 'R');
        $fpdf->Cell(30,7,'9.903.600',1,0, 'R');
        $fpdf->Cell(50,7,'Faulina Diah Setyani',1);
        $fpdf->Output();
        exit;
////        return response()->file($fpdf->Output());
//        $filename = $fpdf->Output();
//        $path = storage_path($filename);
//
//        return Response::make(file_get_contents($path), 200, [
//            'Content-Type' => 'application/pdf',
//            'Content-Disposition' => 'inline; filename="'.$filename.'"'
//        ]);
    }

}
