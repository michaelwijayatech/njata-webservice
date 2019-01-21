<?php

namespace App\Http\Controllers\API;

use App\Classes\GlobalClass;
use App\Model\Administrator;
use App\Model\Attendance;
use App\Model\Company;
use App\Model\Contact;
use App\Model\Employee;
use App\Model\GroupDetail;
use App\Model\GroupHeader;
use App\Model\Haid;
use App\Model\Holiday;
use App\Model\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DesktopController extends Controller
{
    //

    public function signin(){
        $_administrator = new Administrator();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $username = $request->username;
            $password = $request->password;

            $data_user = DB::table($_administrator->BASETABLE)
                ->where('user_name', '=', $username)
                ->first();

            if (!empty($data_user)) {
                if($_global_class->checkPassword($password, $data_user->password)){
                    if($data_user->is_active === $_administrator->STATUS_ACTIVE){
                        $feedback = [
                            "message" => $data_user->id,
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    }
                    else {
                        $feedback = [
                            "message" => "User inactive",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                } else {
                    $feedback = [
                        "message" => "Wrong Username or Password",
                        "status" => $_global_class->STATUS_ERROR,
                    ];

                    return response()->json($feedback);
                }
            } else {
                $feedback = [
                    "message" => "Wrong Username or Password",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function add_data(){
        date_default_timezone_set("Asia/Jakarta");
        $_global_class = new GlobalClass();
        $_table = null;

        $fields = [];
        $index = [];
        $data = array();
        $id = "";
        $i=0;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $table = $request->table;

            if(strtolower($table) === "company"){
                $_table = new Company();
                $fields = [
                    "name", "email", "address", "description", "phone_1", "phone_2", "phone_3", "phone_4"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "administrator"){
                $_table = new Administrator();
                $fields = [
                    "id_company", "first_name", "last_name", "user_name", "email", "dob"
                ];


                //<editor-fold desc="CHECK IS USERNAME EXIST">
                $user_name = $request->user_name;
                if(!$this->check_username($_table, $user_name)){
                    $feedback = [
                        "message" => "Username already exist.",
                        "status" => $_global_class->STATUS_ERROR,
                    ];

                    return response()->json($feedback);
                };
                //</editor-fold>

                $gender = $request->gender;
                if(strtolower($gender) === "male"){
                    $gender = $_table->GENDER_MALE;
                } else {
                    $gender = $_table->GENDER_FEMALE;
                }
                $data += ["gender" => $gender];

                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["role" => "administrator"];
                $data += ["password" => $_global_class->generatePassword("12345")];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "contact"){
                $_table = new Contact();
                $fields = [
                    "first_name", "last_name", "email", "id_company", "address", "description", "phone_1", "phone_2", "phone_3", "phone_4", "phone_5", "phone_6"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "standard"){
                $_table = new Standard();
                $fields = [
                    "name", "year", "nominal"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "holiday"){
                $_table = new Holiday();
                $fields = [
                    "date", "description"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "haid"){
                $_table = new Haid();
                $fields = [
                    "id_employee"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["date" => date("d-m-Y")];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "attendance"){
                $_table = new Attendance();
                $fields = [
                    "id_employee"
                ];

                $status = $request->status;
                if(strtolower($status) === "masuk"){
                    $status = $_table->STATUS_MASUK;
                } elseif(strtolower($status) === "setengah hari"){
                    $status = $_table->STATUS_SETENGAH_HARI;
                } elseif(strtolower($status) === "tidak masuk"){
                    $status = $_table->STATUS_TIDAK_MASUK;
                } elseif(strtolower($status) === "ijin"){
                    $status = $_table->STATUS_IJIN;
                }

                $_date = $request->date;
                if ($_date === ""){
                    $data += ["date" => date("d-m-Y")];
                } else {
                    $data += ["date" => $_date];
                }

                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["status" => $status];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "group_header"){
                $_table = new GroupHeader();
                $fields = [
                    "name"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "group_detail"){
                $_table = new GroupDetail();
                $fields = [
                    "name"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            foreach ($fields as $field) {
                ${$field} = $request->$field;
                $data += ["{$field}" => "${$field}"];
            }

            $check_insert = DB::table($_table->BASETABLE)->insert($data);

            if($check_insert){
                $feedback = [
                    "message" => $table . " Inserted successfully",
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function load_data(){
        date_default_timezone_set("Asia/Jakarta");
        $_global_class = new GlobalClass();
        $_table = null;
        $_data = null;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $table = $request->table;
            $id = $request->id;

            if (strtolower($table) === "company") {
                $_table = new Company();

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "administrator") {
                $_table = new Administrator();

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "employee") {
                $_table = new Employee();
                $_data = [];

                if(strtolower($id) === "all") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_employee) > 0) {
                        foreach ($_employee as $emplo => $emp) {
                            $_emp_id = $emp->id;

                            if ($emp->status === 1) {
                                $status = "Harian Atas";
                            } elseif ($emp->status === 2) {
                                $status = "Borongan";
                            } elseif ($emp->status === 3) {
                                $status = "Harian Bawah";
                            }

                            $temp = array(
                                "id" => $emp->id,
                                "first_name" => $emp->first_name,
                                "last_name" => $emp->last_name,
                                "status" => $status
                            );

                            array_push($_data, $temp);
                        }
                    }

                } else {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();

                    if ($_employee->status === 1) {
                        $status = "Harian Atas";
                    } elseif ($_employee->status === 2) {
                        $status = "Borongan";
                    } elseif ($_employee->status === 3) {
                        $status = "Harian Bawah";
                    }

                    $temp = array(
                        "id" => $_employee->id,
                        "first_name" => $_employee->first_name,
                        "last_name" => $_employee->last_name,
                        "status" => $status
                    );

                    array_push($_data, $temp);
                }
            }

            if (strtolower($table) === "contact") {
                $_table = new Contact();

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "standard") {
                $_table = new Standard();

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "holiday") {
                $_table = new Holiday();

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "haid") {
                $_data = [];
                $_table = new Employee();

                if(strtolower($id) === "all") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    $_table = new Haid();
                    $_month = date("m");
                    $_year = date("Y");

                    if (count($_employee) > 0){
                        foreach ($_employee as $emplo => $emp) {
                            $_emp_id = $emp->id;

                            if ($emp->status === 1){
                                $status = "Harian Atas";
                            } elseif ($emp->status === 2){
                                $status = "Borongan";
                            } elseif ($emp->status === 3){
                                $status = "Harian Bawah";
                            }

                            $_is_haid = DB::select(DB::raw("SELECT * FROM haid
                                                            WHERE id_employee = '$_emp_id'
                                                            AND SUBSTR(`date`,4,2) = '$_month'
                                                            AND SUBSTR(`date`,7,4) = '$_year'
                                                            AND is_active = '1'"));

                            $date = "";

                            if (count($_is_haid) > 0){
                                foreach ($_is_haid as $is_haid => $sh) {
                                    $date = $sh->date;
                                }
                            }

                            $temp = array(
                                "id" => $emp->id,
                                "first_name" => $emp->first_name,
                                "last_name" => $emp->last_name,
                                "status" => $status,
                                "date" => $date
                            );

                            array_push($_data, $temp);
                        }
                    }
                }
            }

            if (strtolower($table) === "attendance") {
                $_data = [];
                $_table = new Employee();

                if(strtolower($id) === "all") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    $_table = new Attendance();
                    $_date = date("d");
                    $_month = date("m");
                    $_year = date("Y");

                    if (count($_employee) > 0){
                        foreach ($_employee as $emplo => $emp) {
                            $_emp_id = $emp->id;

                            if ($emp->status === 1){
                                $status = "Harian Atas";
                            } elseif ($emp->status === 2){
                                $status = "Borongan";
                            } elseif ($emp->status === 3){
                                $status = "Harian Bawah";
                            }

                            $_is_att = DB::select(DB::raw("SELECT * FROM attendance
                                                            WHERE id_employee = '$_emp_id'
                                                            AND SUBSTR(`date`,1,2) = '$_date'
                                                            AND SUBSTR(`date`,4,2) = '$_month'
                                                            AND SUBSTR(`date`,7,4) = '$_year'
                                                            AND is_active = '1'"));

                            $att_status = "";
                            $att_id = "";

                            if (count($_is_att) > 0){
                                foreach ($_is_att as $is_att => $att) {
                                    $att_status = $att->status;
                                    $att_id = $att->id;
                                }
                            }

                            $temp = array(
                                "id" => $emp->id,
                                "first_name" => $emp->first_name,
                                "last_name" => $emp->last_name,
                                "status" => $status,
                                "attendance" => $att_status,
                                "attendance_id" => $att_id
                            );

                            array_push($_data, $temp);
                        }
                    }
                } elseif (strtolower($id) === "update_attendance") {
                    $_table = new Attendance();
                    $id_employee = $request->id_employee;
                    $date = $request->date;

                    $_data = DB::table($_table->BASETABLE)
                        ->where('id_employee' , '=', $id_employee)
                        ->where('date', '=', $date)
                        ->first();
                }
            }

            if (strtolower($table) === "group_header") {
                $_table = new GroupHeader();

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "group_detail") {
                $_table = new GroupDetail();
                $_data = [];

                if(strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_gds = DB::table($_table->BASETABLE)
                        ->where('id_group' , '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_gds) > 0) {
                        foreach ($_gds as $_gd => $gd) {
                            $_emp_id = $gd->id_employee;

                            $_employee = DB::table($_table->BASETABLE)
                                ->where('id' , '=', $_emp_id)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->first();

                            $temp = array(
                                "id" => $gd->id,
                                "id_employee" => $gd->id_employee,
                                "first_name" => $_employee->first_name,
                                "last_name" => $_employee->last_name
                            );

                            array_push($_data, $temp);
                        }
                    }
                }
            }

            $feedback = [
                "message" => $_data,
                "status" => $_global_class->STATUS_SUCCESS,
            ];

            return response()->json($feedback);
        }
    }

    public function update_data(){
        date_default_timezone_set("Asia/Jakarta");
        $_global_class = new GlobalClass();
        $_table = null;

        $fields = [];
        $index = [];
        $data = array();
        $id = "";
        $i=0;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $table = $request->table;
            $id = $request->id;

            if (strtolower($table) === "company") {
                $_table = new Company();
                $fields = [
                    "name", "email", "address", "description", "phone_1", "phone_2", "phone_3", "phone_4"
                ];
            }

            if (strtolower($table) === "administrator") {
                $_table = new Administrator();
                $fields = [
                    "id_company", "first_name", "last_name", "user_name", "email", "dob"
                ];

                //<editor-fold desc="CHECK IS USERNAME EXIST EXCEPT USER ID">
                $user_name = $request->user_name;
                if(!$this->check_username_but_id($_table, $id, $user_name)){
                    $feedback = [
                        "message" => "Username already exist.",
                        "status" => $_global_class->STATUS_ERROR,
                    ];

                    return response()->json($feedback);
                };
                //</editor-fold>

                $gender = $request->gender;
                if(strtolower($gender) === "male"){
                    $gender = $_table->GENDER_MALE;
                } else {
                    $gender = $_table->GENDER_FEMALE;
                }
                $data += ["gender" => $gender];
            }

            if (strtolower($table) === "contact") {
                $_table = new Contact();
                $fields = [
                    "first_name", "last_name", "email", "id_company", "address", "description", "phone_1", "phone_2", "phone_3", "phone_4", "phone_5", "phone_6"
                ];
            }

            if (strtolower($table) === "standard") {
                $_table = new Standard();
                $fields = [
                    "name", "year", "nominal"
                ];
            }

            if (strtolower($table) === "holiday") {
                $_table = new Holiday();
                $fields = [
                    "date", "description"
                ];
            }

            if (strtolower($table) === "attendance") {
                $_table = new Attendance();
                $fields = [

                ];

                $status = $request->status;
                if(strtolower($status) === "masuk"){
                    $status = $_table->STATUS_MASUK;
                } elseif(strtolower($status) === "setengah hari"){
                    $status = $_table->STATUS_SETENGAH_HARI;
                } elseif(strtolower($status) === "tidak masuk"){
                    $status = $_table->STATUS_TIDAK_MASUK;
                } elseif(strtolower($status) === "ijin"){
                    $status = $_table->STATUS_IJIN;
                }

                $data += ["status" => $status];

            }

            foreach ($fields as $field) {
                ${$field} = $request->$field;
                $data += ["{$field}" => "${$field}"];
            }

            $_data = DB::table($_table->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            $feedback = [
                "message" => $table . ' Updated Successfully.',
                "status" => $_global_class->STATUS_SUCCESS,
            ];

            return response()->json($feedback);
        }
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    public function check_username($_table, $user_name){
        $data_user = DB::table($_table->BASETABLE)
            ->where('user_name', '=', $user_name)
            ->first();

        if (!empty($data_user)) {
            return false;
        }
        return true;
    }
    public function check_username_but_id($_table, $id, $user_name){
        $data_user = DB::table($_table->BASETABLE)
            ->where('user_name', '=', $user_name)
            ->where('id', '!=' , $id)
            ->first();

        if (!empty($data_user)) {
            return false;
        }
        return true;
    }

    //region ADMINISTRATOR
    public function administrator_load_data(){
        $_administrator = new Administrator();
        $_global_class = new GlobalClass();

        $fields = [
            "id"
        ];
        $id="";
        $i=0;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            foreach ($fields as $field){
                ${$field} = $request->$field;

                if($i===0){
                    $id = $request->$field;
                }
                $i++;
            }

            if(strtolower($id) === "all"){
                $_data = DB::table($_administrator->BASETABLE)
                    ->where('role', '!=' , 'all')
                    ->where('is_active', '=', $_administrator->STATUS_ACTIVE)
                    ->get();

                $feedback = [
                    "message" => $_data,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $_data = DB::table($_administrator->BASETABLE)
                    ->where('id' , '=', $id)
                    ->where('is_active', '=', $_administrator->STATUS_ACTIVE)
                    ->first();

                $feedback = [
                    "message" => $_data,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function administrator_add_data(){
        $_administrator = new Administrator();
        $_global_class = new GlobalClass();

        $fields = [
            "first_name",
            "last_name",
            "user_name",
            "email",
            "dob",
            "gender"
        ];

        $index = [0,1,2,3,4];

        $data = array();
        $id="";
        $i=0;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);

            $generate_id = $_global_class->generateID($_administrator->NAME);
            $data += ["id" => $generate_id];
            $data += ["role" => "administrator"];
            $data += ["password" => $_global_class->generatePassword("12345")];
            $data += ["is_active" => "1"];

            foreach ($fields as $field) {
                ${$field} = $request->$field;

                if (in_array($i, $index)) {
                    $data += ["{$field}" => "${$field}"];
                }

                if($i === 2){
                    $data_user = DB::table($_administrator->BASETABLE)
                        ->where('user_name', '=', ${$field})
                        ->first();

                    if (!empty($data_user)) {
                        $feedback = [
                            "message" => "Username already exist.",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                }

                if($i === 5){
                    if(strtolower(${$field}) === "male"){
                        $gender = $_administrator->GENDER_MALE;
                    } else {
                        $gender = $_administrator->GENDER_FEMALE;
                    }
                    $data += ["{$field}" => $gender];
                }

                $i++;
            }

            $check_insert = DB::table($_administrator->BASETABLE)->insert($data);

            if($check_insert){
                $feedback = [
                    "message" => $generate_id,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }

        }
    }

    public function administrator_update_data(){
        $_administrator = new Administrator();
        $_global_class = new GlobalClass();

        $fields = [
            "id",
            "first_name",
            "last_name",
            "user_name",
            "email",
            "dob",
            "gender"
        ];

        $index = [1,2,3,4,5];

        $data = array();
        $id="";
        $i=0;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);

            $generate_id = $_global_class->generateID($_administrator->NAME);

            foreach ($fields as $field) {
                ${$field} = $request->$field;

                if (in_array($i, $index)) {
                    $data += ["{$field}" => "${$field}"];
                }

                if($i === 0){
                    $id = $request->$field;
                }

                if($i === 2){
                    $data_user = DB::table($_administrator->BASETABLE)
                        ->where('user_name', '=', ${$field})
                        ->where('id', '!=' , $id)
                        ->first();

                    if (!empty($data_user)) {
                        $feedback = [
                            "message" => "Username already exist.",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                }

                if($i === 6){
                    if(strtolower(${$field}) === "male"){
                        $gender = $_administrator->GENDER_MALE;
                    } else {
                        $gender = $_administrator->GENDER_FEMALE;
                    }
                    $data += ["{$field}" => $gender];
                }

                $i++;
            }

            $_data = DB::table($_administrator->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            $feedback = [
                "message" => 'Administrator Updated Successfully.',
                "status" => $_global_class->STATUS_SUCCESS,
            ];

            return response()->json($feedback);

        }
    }

    public function administrator_delete_data(){
        $_administrator = new Administrator();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $id = $request->id;

            $data = [
                "is_active" => $_administrator->STATUS_INACTIVE
            ];
            $data_users = DB::table($_administrator->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            if($data_users){
                $feedback = [
                    "message" => "Administrator deleted successfully.",
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function administrator_reset_password(){
        $_administrator = new Administrator();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $id = $request->id;
            $new_password = $request->new_password;

            $data = [
                "password" => $_global_class->generatePassword($new_password),
                "is_active" => $_administrator->STATUS_ACTIVE
            ];
            $data_users = DB::table($_administrator->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            if($data_users){
                $feedback = [
                    "message" => "Your password has been changed successfully.",
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }

        }
    }
    //endregion

    //region EMPLOYEE
    public function employee_load_data(){
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $fields = [
            "employee_id"
        ];
        $id="";
        $i=0;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            foreach ($fields as $field){
                ${$field} = $request->$field;

                if($i===0){
                    $id = $request->$field;
                }
                $i++;
            }

            if(strtolower($id) === "all"){
                $_data = DB::table($_employee->BASETABLE)
                    ->where('is_active', '=', $_employee->STATUS_ACTIVE)
                    ->get();

                $feedback = [
                    "message" => $_data,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $_data = DB::table($_employee->BASETABLE)
                    ->where('id' , '=', $id)
                    ->where('is_active', '=', $_employee->STATUS_ACTIVE)
                    ->first();

                $feedback = [
                    "message" => $_data,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function employee_add_data(){
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $id_company = $request->id_company;
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            $email = $request->email;
            $phone_1 = $request->phone_1;
            $phone_2 = $request->phone_2;
            $domicile_address = $request->domicile_address;
            $premi = $request->premi;
            $dob = $request->dob;
            $start_date = $request->start_date;
            $gender = $request->gender;
            $status = $request->status;

            $generate_id = $_global_class->generateID($_employee->NAME);

            if(strtolower($gender) === "male"){
                $gender = $_employee->GENDER_MALE;
            } else {
                $gender = $_employee->GENDER_FEMALE;
            }

            if(strtolower($status) === "harian_atas"){
                $status = $_employee->STATUS_HARIAN_ATAS;
            } else if(strtolower($status) === "harian_bawah"){
                $status = $_employee->STATUS_HARIAN_BAWAH;
            } else {
                $status = $_employee->STATUS_BORONGAN;
            }

            $data = [
                "id" => $generate_id,
                "id_company" => $id_company,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "phone_1" => $phone_1,
                "phone_2" => $phone_2,
                "domicile_address" => $domicile_address,
                "premi" => $premi,
                "dob" => $dob,
                "start_date" => $start_date,
                "gender" => $gender,
                "status" => $status,
                "is_active" => $_employee->STATUS_ACTIVE
            ];

            $check_insert = DB::table($_employee->BASETABLE)->insert($data);

            if($check_insert){
                $feedback = [
                    "message" => $generate_id,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function employee_update_data()
    {
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $fields = [
            "id",
            "id_company",
            "first_name",
            "last_name",
            "email",
            "phone_1",
            "phone_2",
            "domicile_address",
            "premi",
            "dob",
            "start_date",
            "gender",
            "status"
        ];

        $index = [1,2,3,4,5,6,7,8,9,10];

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);

            $id = "";
            $data = array();
            $i = 0;


            foreach ($fields as $field) {
                ${$field} = $request->$field;

                if (in_array($i, $index)) {
                    $data += ["{$field}" => "${$field}"];
                }

                if($i===0){
                    $id = $request->$field;
                }

                if($i===11){
                    if(strtolower($request->$field) === "male"){
                        $gender = $_employee->GENDER_MALE;
                    } else {
                        $gender = $_employee->GENDER_FEMALE;
                    }
                    $data += ["gender" => $gender];
                }

                if($i===12){
                    if(strtolower($request->$field) === "harian_atas"){
                        $status = $_employee->STATUS_HARIAN_ATAS;
                    } else if(strtolower($request->$field) === "harian_bawah"){
                        $status = $_employee->STATUS_HARIAN_BAWAH;
                    } else {
                        $status = $_employee->STATUS_BORONGAN;
                    }
                    $data += ["status" => $status];
                }

                $i++;
            }

            $_data = DB::table($_employee->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            if($_data){
                $feedback = [
                    "message" => 'Employee Updated Successfully.',
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function employee_add_image(){
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $employee_id = $request->employee_id;
            $image_ktp = $request->image_ktp;

            $data = [
                "image_ktp" => $image_ktp
            ];

            $_data = DB::table($_employee->BASETABLE)
                ->where('id', '=', $employee_id)
                ->update($data);

            if($_data){
                $feedback = [
                    "message" => 'Update employee image success.',
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }

    public function employee_add_image_data(){
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $fields = [
            "employee_id",
            "image_ktp",
            "image_kk",
            "image_bpjs_ketenagakerjaan",
            "image_bpjs_kesehatan"
        ];

        $index = [1,2,3,4];

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);

            $id = "";
            $data = array();
            $i=0;

            foreach ($fields as $field){
                ${$field} = $request->$field;

                if(in_array($i, $index)){
                    $data += ["{$field}" => "${$field}"];
                }

                if($i===0){
                    $id = $request->$field;
                }

                $i++;

            }

            $_data = DB::table($_employee->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            if($_data){
                $feedback = [
                    "message" => 'Update employee image success.',
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }

        }
    }

    public function employee_update_image_data(){
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);

            $data = array();

            $id = $request->employee_id;
            $image_ktp = $request->image_ktp;
            $image_kk = $request->image_kk;
            $image_bpjs_ketenagakerjaan = $request->image_bpjs_ketenagakerjaan;
            $image_bpjs_kesehatan = $request->image_bpjs_kesehatan;

            if($image_ktp !== null && $image_ktp !== ""){
                $data += ["image_ktp" => $image_ktp];
            }

            if($image_kk !== null && $image_kk !== ""){
                $data += ["image_kk" => $image_kk];
            }

            if($image_bpjs_ketenagakerjaan !== null && $image_bpjs_ketenagakerjaan !== ""){
                $data += ["image_bpjs_ketenagakerjaan" => $image_bpjs_ketenagakerjaan];
            }

            if($image_bpjs_kesehatan !== null && $image_bpjs_kesehatan !== ""){
                $data += ["image_bpjs_kesehatan" => $image_bpjs_kesehatan];
            }


            $_data = DB::table($_employee->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            if($_data){
                $feedback = [
                    "message" => 'Update employee image success.' . $image_ktp,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }

        }
    }

    public function employee_upload_image(){
        $_global_class = new GlobalClass();

        $name = $_global_class->generateID('IMG');
        $img = $name . '.jpg';
        $target_path 	= base_path('public/images/employee/') . $img;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            return response()->json([
                "message" => $img,
                "status" => $_global_class->STATUS_SUCCESS
            ]);
        } else {
            return response()->json([
                "message" => 'Something when wrong. Please try again later.',
                "status" => $_global_class->STATUS_ERROR
            ]);
        }
    }

    public function employee_resign(){
        $_employee = new Employee();
        $_global_class = new GlobalClass();

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $employee_id  = $request->employee_id;
            $end_date = $request->end_date;

            $data = [
                "end_date" => $end_date,
                "is_active" => $_employee->STATUS_INACTIVE
            ];

            $_data = DB::table($_employee->BASETABLE)
                ->where('id', '=', $employee_id)
                ->update($data);

            if($_data){
                $feedback = [
                    "message" => 'Update employee resign success.',
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            } else {
                $feedback = [
                    "message" => "There is something error. Please try again later.",
                    "status" => $_global_class->STATUS_ERROR,
                ];

                return response()->json($feedback);
            }
        }
    }
    //endregion
}
