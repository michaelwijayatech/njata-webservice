<?php

namespace App\Http\Controllers\API;

use App\Classes\GlobalClass;
use App\Model\Account;
use App\Model\Accountancy;
use App\Model\Administrator;
use App\Model\Attendance;
use App\Model\Balance;
use App\Model\Carton;
use App\Model\Chop;
use App\Model\Company;
use App\Model\Contact;
use App\Model\Distributor;
use App\Model\Employee;
use App\Model\Expense;
use App\Model\GroupDetail;
use App\Model\GroupHeader;
use App\Model\Haid;
use App\Model\Holiday;
use App\Model\Income;
use App\Model\Payment;
use App\Model\Product;
use App\Model\Purchase;
use App\Model\RevisionSalary;
use App\Model\Sales;
use App\Model\SalesDetail;
use App\Model\Standard;
use App\Model\Supplier;
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
                ->first(['id', 'password', 'role', 'is_active']);

            if (!empty($data_user)) {
                if($_global_class->checkPassword($password, $data_user->password)){
                    if($data_user->is_active === $_administrator->STATUS_ACTIVE){
                        $feedback = [
                            "message" => $data_user,
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

            if(strtolower($table) === "product"){
                $_table = new Product();
                $fields = [
                    "name", "price", "gram"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "distributor"){
                $_table = new Distributor();
                $fields = [
                    "name", "address", "phone_number"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "supplier"){
                $_table = new Supplier();
                $fields = [
                    "name", "address", "phone_number"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
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

            if(strtolower($table) === "group_detail_employee"){
                $_table = new GroupDetail();
                $fields = [
                    "id_group", "id_employee"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "revision_salary"){
                $_table = new RevisionSalary();
                $fields = [
                    "id_employee", "msit", "pokok", "premi", "haid", "potongan_bpjs", "total_before", "total_revisi", "total_after"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["date" => date("d-m-Y")];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "purchase"){
                $_table = new Purchase();
                $fields = [
                    "id_supplier", "name", "description", "nominal"
                ];

                $nominal = $request->nominal;
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $generate_date = date("d-m-Y");
                $data += ["date" => $generate_date];
                $data += ["is_active" => $_table->STATUS_ACTIVE];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $local_insert = DB::table($_table->BASETABLE)->insert($data);

                if($local_insert){
                    $check_acc = $this->accountancy('ADD', 'CREDIT', $generate_id, date("d-m-Y"), $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Accountancy Inserted successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                } else {
                    $feedback = [
                        "message" => "There is something error. Please try again later.",
                        "status" => $_global_class->STATUS_ERROR,
                    ];

                    return response()->json($feedback);
                }
            }

            if(strtolower($table) === "sales"){
                $_table = new Sales();
                $fields = [
                    "id_distributor", "nota_number", "total"
                ];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["date" => date("d-m-Y")];
                $data += ["paid" => "0"];
                $data += ["is_active" => $_table->STATUS_ACTIVE];

                $check_insert = DB::table($_table->BASETABLE)->insert($data);

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

            if(strtolower($table) === "sales_detail"){
                $_table = new SalesDetail();
                $fields = [
                    "id_sales", "id_product", "quantity", "price", "total"
                ];
                $id_helper = $request->id_helper;
                $generate_id = $_global_class->generateID($_table->NAME) . $id_helper;
                $data += ["id" => $generate_id];
                $data += ["is_active" => $_table->STATUS_ACTIVE];
            }

            if(strtolower($table) === "payment"){
                $_table = new Payment();
                $fields = [
                    "id_sales", "nominal"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["date" => date("d-m-Y")];
                $data += ["is_active" => $_table->STATUS_ACTIVE];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $check_insert = DB::table($_table->BASETABLE)->insert($data);

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

            if(strtolower($table) === "expenses"){
                $_table = new Expense();
                $fields = [
                    "id_account", "name", "nominal", "description"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["date" => date("d-m-Y")];
                $data += ["is_active" => $_table->STATUS_ACTIVE];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $local_insert = DB::table($_table->BASETABLE)->insert($data);

                if($local_insert){
                    $nominal = $request->nominal;
                    $check_acc = $this->accountancy('ADD', 'CREDIT', $generate_id, date("d-m-Y"), $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Accountancy Inserted successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                } else {
                    $feedback = [
                        "message" => "There is something error. Please try again later.",
                        "status" => $_global_class->STATUS_ERROR,
                    ];

                    return response()->json($feedback);
                }
            }

            if(strtolower($table) === "income"){
                $_table = new Income();
                $fields = [
                    "name", "nominal", "description"
                ];
                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
                $data += ["date" => date("d-m-Y")];
                $data += ["is_active" => $_table->STATUS_ACTIVE];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $local_insert = DB::table($_table->BASETABLE)->insert($data);

                if($local_insert){
                    $nominal = $request->nominal;
                    $check_acc = $this->accountancy('ADD', 'DEBIT', $generate_id, date("d-m-Y"), $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Accountancy Inserted successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                } else {
                    $feedback = [
                        "message" => "There is something error. Please try again later.",
                        "status" => $_global_class->STATUS_ERROR,
                    ];

                    return response()->json($feedback);
                }
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

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "administrator") {
                $_table = new Administrator();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "employee") {
                $_table = new Employee();
                $_data = [];

                if (strtolower($id) === "all") {
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
                        ->where('id', '=', $id)
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

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "product") {
                $_table = new Product();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "distributor") {
                $_table = new Distributor();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "supplier") {
                $_table = new Supplier();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "standard") {
                $_table = new Standard();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "holiday") {
                $_table = new Holiday();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "chop") {
                $_table = new Chop();
                $date = date("d-m-Y");

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('date', '=', $date)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "haid") {
                $_data = [];
                $_table = new Employee();

                if (strtolower($id) === "all") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('gender', '=', $_table->GENDER_FEMALE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    $_table = new Haid();
                    $_month = date("m");
                    $_year = date("Y");

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

                            $_is_haid = DB::select(DB::raw("SELECT * FROM haid
                                                            WHERE id_employee = '$_emp_id'
                                                            AND SUBSTR(`date`,4,2) = '$_month'
                                                            AND SUBSTR(`date`,7,4) = '$_year'
                                                            AND is_active = '1'"));

                            $date = "";

                            if (count($_is_haid) > 0) {
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

                if (strtolower($id) === "all") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    $_table = new Attendance();
                    $_date = date("d");
                    $_month = date("m");
                    $_year = date("Y");

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

                            $_is_att = DB::select(DB::raw("SELECT * FROM attendance
                                                            WHERE id_employee = '$_emp_id'
                                                            AND SUBSTR(`date`,1,2) = '$_date'
                                                            AND SUBSTR(`date`,4,2) = '$_month'
                                                            AND SUBSTR(`date`,7,4) = '$_year'
                                                            AND is_active = '1'"));

                            $att_status = "";
                            $att_id = "";

                            if (count($_is_att) > 0) {
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
                        ->where('id_employee', '=', $id_employee)
                        ->where('date', '=', $date)
                        ->first();
                } elseif (strtolower($id) === "advance_attendance_by_date") {
                    $_table = new Attendance();
                    $date = $request->date;

                    $_data = DB::table($_table->BASETABLE)
                        ->join('employee', 'employee.id', '=', 'attendance.id_employee')
                        ->where('attendance.date', '=', $date)
                        ->select('attendance.id', 'attendance.date', 'attendance.status', 'employee.first_name', 'employee.last_name')
                        ->get();
                }
            }

            if (strtolower($table) === "group_header") {
                $_table = new GroupHeader();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "group_detail") {
                $_table = new GroupDetail();
                $_data = [];

                if (strtolower($id) === "all") {
                    $_table = new Employee();
                    $_data = DB::table($_table->BASETABLE)
                        ->select('id', 'first_name', 'last_name')
                        ->where('status', '=', $_table->STATUS_BORONGAN)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->whereNotIn('id', function ($query) {
                            $query->select('id_employee')->from('group_detail');
                        })
                        ->get();
                } else {
                    $_gds = DB::table($_table->BASETABLE)
                        ->where('id_group', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_gds) > 0) {
                        foreach ($_gds as $_gd => $gd) {
                            $_table = new Employee();
                            $_emp_id = $gd->id_employee;

                            $_employee = DB::table($_table->BASETABLE)
                                ->where('id', '=', $_emp_id)
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

            if (strtolower($table) === "purchase") {
                $_table = new Purchase();
                $start_date = $request->start_date;
                $end_date = $request->end_date;

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->join('supplier', 'supplier.id', '=', 'purchase.id_supplier')
                        ->where('purchase.date', '>=', $start_date)
                        ->where('purchase.date', '<=', $end_date)
                        ->where('purchase.is_active', '=', $_table->STATUS_ACTIVE)
                        ->select('purchase.id', 'purchase.date', 'purchase.name as p_name', 'purchase.nominal', 'supplier.name as s_name')
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->join('supplier', 'supplier.id', '=', 'purchase.id_supplier')
                        ->where('purchase.id_supplier', '=', $id)
                        ->where('purchase.date', '>=', $start_date)
                        ->where('purchase.date', '<=', $end_date)
                        ->where('purchase.is_active', '=', $_table->STATUS_ACTIVE)
                        ->select('purchase.id', 'purchase.date', 'purchase.name as p_name', 'purchase.nominal', 'supplier.name as s_name')
                        ->get();
                }
            }

            if (strtolower($table) === "purchase_detail") {
                $_table = new Purchase();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "carton") {
                $_table = new GroupHeader();
                $_data = [];
                $_date = date("d-m-Y");

                if (strtolower($id) === "all") {
                    $_ghs = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_ghs) > 0) {
                        foreach ($_ghs as $ghs => $gh) {
                            $gh_id = $gh->id;
                            $gh_name = $gh->name;

                            $_table = new Carton();
                            $cartons = DB::table($_table->BASETABLE)
                                ->where('id_group', '=', $gh_id)
                                ->where('date', '=', $_date)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->first();

                            if (!empty($cartons)) {
                                $temp = array(
                                    "id" => $cartons->id,
                                    "id_group" => $gh_id,
                                    "group_name" => $gh_name,
                                    "carton" => $cartons->carton
                                );

                                array_push($_data, $temp);
                            } else {
                                $temp = array(
                                    "id" => "",
                                    "id_group" => $gh_id,
                                    "group_name" => $gh_name,
                                    "carton" => "-"
                                );

                                array_push($_data, $temp);
                            }

                        }
                    }
                } elseif (strtolower($id) === "carton_by_date"){
                    $_date = $request->date;
                    $_ghs = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_ghs) > 0) {
                        foreach ($_ghs as $ghs => $gh) {
                            $gh_id = $gh->id;
                            $gh_name = $gh->name;

                            $_table = new Carton();
                            $cartons = DB::table($_table->BASETABLE)
                                ->where('id_group', '=', $gh_id)
                                ->where('date', '=', $_date)
                                ->first();

                            if (!empty($cartons)) {
                                $temp = array(
                                    "id" => $cartons->id,
                                    "id_group" => $gh_id,
                                    "group_name" => $gh_name,
                                    "carton" => $cartons->carton
                                );

                                array_push($_data, $temp);
                            } else {
                                $temp = array(
                                    "id" => "",
                                    "id_group" => $gh_id,
                                    "group_name" => $gh_name,
                                    "carton" => "-"
                                );

                                array_push($_data, $temp);
                            }

                        }
                    }
                } elseif (strtolower($id) === "carton_by_date_all"){
                    $_date = $request->date;
                    $_ghs = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_ghs) > 0) {
                        foreach ($_ghs as $ghs => $gh) {
                            $gh_id = $gh->id;
                            $gh_name = $gh->name;

                            $_table = new Carton();
                            $cartons = DB::table($_table->BASETABLE)
                                ->where('id_group', '=', $gh_id)
                                ->where('date', '=', $_date)
                                ->get();

                            if (count($cartons) > 0) {
                                foreach ($cartons as $carton => $cart) {
                                    $temp = array(
                                        "id" => $cart->id,
                                        "id_group" => $gh_id,
                                        "group_name" => $gh_name,
                                        "carton" => $cart->carton
                                    );

                                    array_push($_data, $temp);
                                }
                            } else {
                                $temp = array(
                                    "id" => "",
                                    "id_group" => $gh_id,
                                    "group_name" => $gh_name,
                                    "carton" => "-"
                                );

                                array_push($_data, $temp);
                            }
                        }
                    }
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "sales") {
                $_table = new Sales();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {

                    $_data = DB::table($_table->BASETABLE)
                        ->join('distributor', 'distributor.id', '=', 'sales.id_distributor')
                        ->where('sales.nota_number', '=', $id)
                        ->where('sales.is_active', '=', $_table->STATUS_ACTIVE)
                        ->select('sales.id as sales_id', 'sales.id_distributor as sales_id_distributor', 'sales.nota_number', 'sales.date', 'sales.total', 'sales.paid', 'distributor.name as distributor_name')
                        ->get();
                }
            }

            if (strtolower($table) === "sales_by_distributor") {
                $_table = new Sales();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id_distributor', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                }
            }

            if (strtolower($table) === "sales_detail") {
                $_table = new SalesDetail();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {

                    $_data = DB::table($_table->BASETABLE)
                        ->join('product', 'product.id', '=', 'sales_detail.id_product')
                        ->where('sales_detail.id_sales', '=', $id)
                        ->where('sales_detail.is_active', '=', $_table->STATUS_ACTIVE)
                        ->select('sales_detail.id as sd_id', 'sales_detail.id_product as sd_id_product', 'sales_detail.quantity as sd_quantity', 'sales_detail.price as sd_price', 'sales_detail.total as sd_total', 'product.name as product_name')
                        ->get();
                }
            }

            if (strtolower($table) === "gaji_borongan") {
                $_table = new GroupHeader();
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $_potongan_bpjs = $request->potongan_bpjs;
                $_start_date = explode('-', $start_date);
                $_end_date = explode('-', $end_date);

                $_data = [];
                $cartons = null;
                $haid = 0;
                $haid_name = null;
                $ijin = 0;
                $ijin_name = null;
                $ijin_name_arr = [];
                $tidak_masuk = 0;
                $tidak_masuk_name = null;
                $tidak_masuk_name_arr = [];
                $potongan_bpjs = 0;
                $_total = 0;
                $upah_borongan = 0;
                $cuti_haid = 0;
                $upah_harian = 0;
                $_upah_libur = 0;

                $_stat_harian_atas = $_table->STATUS_HARIAN_ATAS;
                $_stat_harian_bawah = $_table->STATUS_HARIAN_BAWAH;

                if (strtolower($id) === "all") {
                    $_ghs = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_ghs) > 0) {
                        foreach ($_ghs as $ghs => $gh) {
                            $gh_id = $gh->id;

                            $_carton = 0;
                            $_table = new Carton();
                            $_cartons_temps = DB::table($_table->BASETABLE)
                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_month)
                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();
                            if ($_start_date[1] !== $_end_date[1]) {
                                $_cartons_temps = DB::table($_table->BASETABLE)
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                    ->get();
                            }
                            if (count($_cartons_temps) > 0) {
                                foreach ($_cartons_temps as $_cartons_temp => $_cartonstemp) {
                                    $_cartonst_date = $_cartonstemp->date;
                                    $_cartons_date = date('Y-m-d', strtotime($_cartonst_date));

                                    $_cartons_start = date('Y-m-d', strtotime($start_date));
                                    $_cartons_end = date('Y-m-d', strtotime($end_date));

                                    if (($_cartons_date >= $_cartons_start) && ($_cartons_date <= $_cartons_end)) {
                                        $_carton += (int)$_cartonstemp->carton;
                                    }
                                }
                            }
//                            if ($_start_date[1] !== $_end_date[1]) {
//                                $_cartons = DB::select(DB::raw("SELECT SUM(carton) as total FROM $_table->BASETABLE
//                                                            WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
//                                                            AND id_group = '$gh_id'
//                                                            AND is_active = $_table->STATUS_ACTIVE"));
//
//                                foreach ($_cartons as $cartons => $carton) {
//                                    $_carton = $carton->total;
//                                }
//                            } else {
//                                $_carton = DB::table('carton')
//                                    ->where('id_group', $gh_id)
//                                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                    ->sum('carton');
//                            }

//                            $_table = new Holiday();
//                            $_holiday = DB::table($_table->BASETABLE)
//                                ->where('date', '>=', $start_date)
//                                ->where('date', '<=', $end_date)
//                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                ->count();

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

                            $_year = date("Y");
                            $_table = new Standard();
                            $_standarts = DB::table($_table->BASETABLE)
                                ->where('year', '=', $_year)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();

                            if (count($_standarts) > 0) {
                                foreach ($_standarts as $standarts => $standart) {
                                    if ($standart->name === "upah_borongan") {
                                        $upah_borongan = $_global_class->removeMoneySeparator($standart->nominal);
                                    }
                                    if ($standart->name === "cuti_haid") {
                                        $cuti_haid = $_global_class->removeMoneySeparator($standart->nominal);
                                    }
                                    if ($standart->name === "upah_harian") {
                                        $upah_harian = $_global_class->removeMoneySeparator($standart->nominal);
                                    }
                                }
                            }

                            $_haids = DB::table('haid')
                                ->where('date', '>=', $start_date)
                                ->where('date', '<=', $end_date)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();

                            if (count($_haids) > 0) {
                                foreach ($_haids as $_haid => $hid) {
                                    $_checkgroup = DB::table('group_detail')
                                        ->where('id_employee', '=', $hid->id_employee)
                                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                        ->first();

                                    if (!empty($_checkgroup)) {
                                        if ($_checkgroup->id_group === $gh_id) {
                                            $haid = $haid + 1;

                                            $_employees = DB::table('employee')
                                                ->where('id', '=', $hid->id_employee)
                                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                                ->first();

                                            $haid_name .= $_employees->first_name . ' ' . $_employees->last_name . '@!#';
                                        }
                                    }

                                }
                            } else {
                                $haid = 0;
                            }

                            if ($_potongan_bpjs) {
                                $_gds = DB::table('group_detail')
                                    ->where('id_group', '=', $gh_id)
                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                    ->get();

                                if (count($_gds) > 0) {
                                    foreach ($_gds as $gds => $gd) {
                                        $gd_id_emp = $gd->id_employee;
                                        $_table = new Employee();
                                        $emplo = DB::table($_table->BASETABLE)
                                            ->where('id', '=', $gd_id_emp)
                                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                            ->first();
                                        $pot_bpjs = $_global_class->removeMoneySeparator($emplo->potongan_bpjs);
                                        $potongan_bpjs = $potongan_bpjs + $pot_bpjs;
                                    }
                                }
                            } else {
                                $potongan_bpjs = 0;
                            }

                            //CEK ABSENSI
                            $_gds = DB::table('group_detail')
                                ->where('id_group', '=', $gh_id)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();

                            if (count($_ghs) > 0) {
                                foreach ($_gds as $gds => $gd) {
                                    $gd_id_empl = $gd->id_employee;

                                    $_atts = DB::table('attendance')
                                        ->join('employee', 'employee.id', '=', 'attendance.id_employee')
                                        ->where('attendance.date', '>=', $start_date)
                                        ->where('attendance.date', '<=', $end_date)
                                        ->where('attendance.id_employee', '=', $gd_id_empl)
                                        ->select('employee.id', 'employee.first_name', 'employee.last_name', 'attendance.id', 'attendance.status', 'employee.start_date')
                                        ->get();

                                    if (count($_atts) > 0) {
                                        foreach ($_atts as $atts => $att) {
                                            $att_stat = $att->status;
                                            $empl_name = $att->first_name . ' ' . $att->last_name;

                                            if ($att_stat === '3') {
                                                $ijin = $ijin + 1;

                                                if (!in_array($empl_name, $ijin_name_arr)) {
                                                    array_push($ijin_name_arr, $empl_name);
                                                    $ijin_name .= $empl_name . '@!#';
                                                }
                                            }
                                        }
                                    }

                                    $_atts2 =  DB::table('employee')
                                        ->where('id', '=', $gd_id_empl)
                                        ->where('is_active', '=' , '1')
                                        ->get();

                                    if (count($_atts2) > 0) {
                                        foreach ($_atts2 as $atts2 => $att2) {
                                            $empl_start_work_date = $att2->start_date;
                                            if ($_global_class->checkDifferenceBetweenTwoDate($empl_start_work_date, date("d-m-Y")) >= 12){
                                                $upah_libur = $_holiday * $upah_harian;
                                                $_upah_libur += $upah_libur;
                                            }
                                        }
                                    }
                                }
                            }

                            //HITUNG TOTAL

                            $total_carton = (int)$_carton * $upah_borongan;
                            $total_haid = $haid * $cuti_haid;
                            $total_ijin = $ijin * $upah_harian;
                            $_total = ($total_carton + $total_haid + $total_ijin + $_upah_libur) - $potongan_bpjs;

                            $temp = array(
                                "id_group" => $gh_id,
                                "group_name" => $gh->name,
                                "carton" => $_carton,
                                "haid" => $haid,
                                "haid_name" => $haid_name,
                                "potongan_bpjs" => $potongan_bpjs,
                                "ijin" => $ijin,
                                "ijin_name" => $ijin_name,
                                "total" => $_total,
                                "upah_libur" => $_upah_libur,
                                "info_empl" => $_atts,
                            );

                            array_push($_data, $temp);

                            $cartons = null;
                            $haid = 0;
                            $haid_name = null;
                            $ijin = 0;
                            $ijin_name = null;
                            $ijin_name_arr = [];
                            $potongan_bpjs = 0;
                            $_upah_libur = 0;
                            $_total = 0;
                        }
                    }
                } // else here
            }

            if (strtolower($table) === "gaji_harian") {
                $_table = new Employee();
                $_year = date("Y");
                $_month = date("m");
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $_start_date = explode('-', $start_date);
                $_end_date = explode('-', $end_date);
                $_potongan_bpjs = $request->potongan_bpjs;

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

                    $_chops = [];
                    $_table = new Chop();
                    $_chops_temps = DB::table($_table->BASETABLE)
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_month)
                        ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                    if ($_start_date[1] !== $_end_date[1]) {
                        $_chops_temps = DB::table($_table->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->get();
                    }
                    if (count($_chops_temps) > 0) {
                        foreach ($_chops_temps as $_chops_temp => $_chopstemp) {
                            $_chopst_date = $_chopstemp->date;
                            $_chops_date = date('Y-m-d', strtotime($_chopst_date));

                            $_chops_start = date('Y-m-d', strtotime($start_date));
                            $_chops_end = date('Y-m-d', strtotime($end_date));

                            if (($_chops_date >= $_chops_start) && ($_chops_date <= $_chops_end)) {
                                $temp1 = array(
                                    "id" => $_chopstemp->id,
                                    "date" => $_chopstemp->date,
                                    "number" => $_chopstemp->number,
                                );

                                array_push($_chops, $temp1);
                            }
                        }
                    }
//                    if ($_start_date[1] !== $_end_date[1]){
//                        $_chops = DB::select(DB::raw("SELECT * FROM $_table->BASETABLE
//                                                            WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
//                                                            AND is_active = $_table->STATUS_ACTIVE"));
//                    } else {
//                        $_chops = DB::table($_table->BASETABLE)
//                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                            ->get();
//                    }
                    if (count($_chops) > 0) {
                        foreach ($_chops as $chops => $chop) {
                            $chop_number = $chop['number'];
                            if ($chop_number === (string)$_table->NUMBER_SINGAPORE){
                                $chop_date = $chop['date'];
                                array_push($_chop_date_arr, $chop_date);
                                array_push($_chop_date_arr, $chop_date);
                            } else {
                                $chop_date = $chop['date'];
                                array_push($_chop_date_arr, $chop_date);
                            }
                        }
                    }

                    $_holiday = 0;
                    $_table = new Holiday();
                    $_holiday_temps = DB::table($_table->BASETABLE)
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_month)
                        ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                    if ($_start_date[1] !== $_end_date[1]) {
                        $_holiday_temps = DB::table($_table->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->get();
                    }
                    if (count($_holiday_temps) > 0) {
                        foreach ($_holiday_temps as $_holiday_temp => $_holidaytemp) {
                            $_holst_date = $_holidaytemp->date;
                            $_hols_date = date('Y-m-d', strtotime($_holst_date));

                            $_hols_start = date('Y-m-d', strtotime($start_date));
                            $_hols_end = date('Y-m-d', strtotime($end_date));

                            if (($_hols_date >= $_hols_start) && ($_hols_date <= $_hols_end)) {
                                $_holiday++;
                            }
                        }
                    }
//                    if ($_start_date[1] !== $_end_date[1]){
//                        $_holidays = DB::select(DB::raw("SELECT COUNT(`id`) as total
//                                                        FROM $_table->BASETABLE
//                                                        WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
//                                                        AND is_active = $_table->STATUS_ACTIVE"));
//
//                        foreach ($_holidays as $holidays => $holiday) {
//                            $_holiday = $holiday->total;
//                        }
//                    } else {
//                        $_holiday = DB::table($_table->BASETABLE)
//                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                            ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                            ->count();
//                    }

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

                            $_atts = [];
                            $_table = new Attendance();
                            $_atts_temps = DB::table($_table->BASETABLE)
                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_month)
                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                                ->where('id_employee', '=', $empl_id)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();
                            if ($_start_date[1] !== $_end_date[1]) {
                                $_atts_temps = DB::table($_table->BASETABLE)
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                                    ->where('id_employee', '=', $empl_id)
                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                    ->get();
                            }
                            if (count($_atts_temps) > 0) {
                                foreach ($_atts_temps as $_atts_temp => $_attstemp) {
                                    $_attst_date = $_attstemp->date;
                                    $_atts_date = date('Y-m-d', strtotime($_attst_date));

                                    $_atts_start = date('Y-m-d', strtotime($start_date));
                                    $_atts_end = date('Y-m-d', strtotime($end_date));

                                    if (($_atts_date >= $_atts_start) && ($_atts_date <= $_atts_end)) {
                                        $temp1 = array(
                                            "att_id" => $_attstemp->id,
                                            "date" => $_attstemp->date,
                                            "status" => $_attstemp->status,
                                        );

                                        array_push($_atts, $temp1);
                                    }
                                }
                            }


//                            $_atts = DB::table($_table->BASETABLE)
////                                ->where('date', '>=', $start_date)
////                                ->where('date', '<=', $end_date)
////                                ->whereBetween('date', [$start_date, $end_date])
//                                ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                                ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                                ->where('id_employee', '=', $empl_id)
//                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                ->get();


//                            if ($_start_date[1] !== $_end_date[1]){
//                                $_atts = DB::table($_table->BASETABLE)
//                                    ->where('id_employee', '=', $empl_id)
//                                    ->where(\DB::raw('(`date` >= '.$start_date.' OR `date` <= '.$end_date.')'))
//                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                    ->get();

//                                $_atts = DB::select(DB::raw("SELECT * FROM attendance
//                                                            WHERE id_employee = '$_emp_id'
//                                                            AND SUBSTR(`date`,1,2) = '$_date'
//                                                            AND SUBSTR(`date`,4,2) = '$_month'
//                                                            AND SUBSTR(`date`,7,4) = '$_year'
//                                                            AND is_active = '1'"));

//                                $_atts = DB::select(DB::raw("SELECT * FROM $_table->BASETABLE
//                                                            WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
//                                                            AND id_employee = '$empl_id'
//                                                            AND is_active = $_table->STATUS_ACTIVE"));
//                            } else {
//                                $_atts = DB::table($_table->BASETABLE)
//                                ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                                ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                                ->where('id_employee', '=', $empl_id)
//                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                ->get();
//                            }
                            if (count($_atts) > 0) {
                                foreach ($_atts as $atts => $att) {
                                    $att_stat = $att['status'];
                                    $att_date = $att['date'];
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
                            $_haids = DB::table($_table->BASETABLE)
                                ->where('id_employee', '=', $empl_id)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->orderBy('date', 'DESC')
                                ->first();
                            if (!empty($_haids)) {
                                $_h_date = $_haids->date;
                                $_haid_date = date('Y-m-d', strtotime($_h_date));

                                $_haid_start = date('Y-m-d', strtotime($start_date));
                                $_haid_end = date('Y-m-d', strtotime($end_date));

                                if (($_haid_date >= $_haid_start) && ($_haid_date <= $_haid_end)) {
                                    $_haid += $_std_haid;
                                }
                            }
                            $ctr_haid = 0;
//                            if ($_start_date[1] !== $_end_date[1]){

//                                $_haids = DB::table($_table->BASETABLE)
//                                    ->where('id_employee', '=', $empl_id)
//                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                    ->orderBy('date', 'DESC')
//                                    ->first();
//                                if (!empty($_haids)){
//                                    $_h_date = $_haids->date;
//                                    $_haid_date = date('Y-m-d', strtotime($_h_date));
//
//                                    $_haid_start = date('Y-m-d', strtotime($start_date));
//                                    $_haid_end = date('Y-m-d', strtotime($end_date));
//
//                                    if (($_haid_date >= $_haid_start) && ($_haid_date <= $_haid_end)) {
//                                        $_haid += $_std_haid;
//                                    }
//                                    /**
//                                     * CHECK YEAR
//                                     */
//                                    if ((explode('-',$_h_date)[2] === $_start_date[2]) AND (explode('-',$_h_date)[2] === $_end_date[2]) ) {
//                                        /**
//                                         * CHECK FIRST MONTH
//                                         */
//                                        if ((string)explode('-',$_h_date)[1] === (string)$_start_date[1]){
//                                            if ((string)explode('-',$_h_date)[0] >= (string)$start_date[0]){
//                                                $_haid += $_std_haid;
//                                            }
//                                        }
//
//                                        /**
//                                         * CHECK SECOND MONTH
//                                         */
//                                        if ((string)explode('-',$_h_date)[1] === (string)$_end_date[1]){
//                                            if ((string)explode('-',$_h_date)[0] <= (string)$_end_date[0]){
//                                                $_haid += $_std_haid;
//                                            }
//                                        }
//                                    }
//                                }

//                            } else {
//                                $_haids = DB::table($_table->BASETABLE)
//                                    ->where('id_employee', '=', $empl_id)
//                                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                    ->count();
//                                if ($_haids > 0){
//                                    $_haid += $_std_haid;
//                                }
//                            }


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

            }

            if (strtolower($table) === "account") {
                $_table = new Account();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "expenses") {
                $_table = new Expense();
                if (strtolower($id) === "all") {
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $_data = DB::table($_table->BASETABLE)
                        ->where('date', '>=', $start_date)
                        ->where('date', '<=', $end_date)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                }
            }

            if (strtolower($table) === "income") {
                $_table = new Income();
                if (strtolower($id) === "all") {
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $_data = DB::table($_table->BASETABLE)
                        ->where('date', '>=', $start_date)
                        ->where('date', '<=', $end_date)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } else {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id', '=', $id)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
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

            if (strtolower($table) === "administrator_role") {
                $_table = new Administrator();
                $fields = [
                    "role"
                ];
            }

            if (strtolower($table) === "employee") {
                $_table = new Employee();
                $fields = [
                    "id_company", "first_name", "last_name", "email", "phone_1", "phone_2", "domicile_address", "premi", "potongan_bpjs", "dob", "start_date"
                ];

                $gender = $request->gender;
                if(strtolower($gender) === "male"){
                    $gender = $_table->GENDER_MALE;
                } else {
                    $gender = $_table->GENDER_FEMALE;
                }
                $data += ["gender" => $gender];

                $status = $request->status;
                if(strtolower($status) === "borongan"){
                    $status = $_table->STATUS_BORONGAN;
                } else if(strtolower($status) === "harian_bawah"){
                    $status = $_table->STATUS_HARIAN_BAWAH;
                } else if(strtolower($status) === "harian_atas"){
                    $status = $_table->STATUS_HARIAN_ATAS;
                }
                $data += ["status" => $status];
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

            if (strtolower($table) === "product") {
                $_table = new Product();
                $fields = [
                    "name", "price", "gram"
                ];
            }

            if (strtolower($table) === "distributor") {
                $_table = new Distributor();
                $fields = [
                    "name", "phone_number", "address"
                ];
            }

            if (strtolower($table) === "supplier") {
                $_table = new Supplier();
                $fields = [
                    "name", "phone_number", "address"
                ];
            }

            if (strtolower($table) === "holiday") {
                $_table = new Holiday();
                $fields = [
                    "date", "description"
                ];
            }

            if (strtolower($table) === "sales_paid") {
                $_table = new Sales();
                $fields = [
                    "paid"
                ];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $_data = DB::table($_table->BASETABLE)
                    ->where('id', '=', $id)
                    ->update($data);

                if ($_data){
                    $_date = date("d-m-Y");
                    $nominal = $request->new_paid;
                    $id_payment = $request->id_payment;
                    $check_acc = $this->accountancy('add', 'debit', $id_payment, $_date, $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Payment Inserted successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                }
            }

            if (strtolower($table) === "purchase") {
                $_table = new Purchase();
                $fields = [
                    "date", "name", "description", "nominal"
                ];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $_data = DB::table($_table->BASETABLE)
                    ->where('id', '=', $id)
                    ->update($data);

                if ($_data){
                    $nominal = $request->nominal;
                    $date = $request->date;
                    $check_acc = $this->accountancy('update', 'credit', $id, $date, $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Accountancy Updated successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                }
            }

            if (strtolower($table) === "chop") {
                $_table = new Chop();
                $_date = date("d-m-Y");

                $fields = [
                    "number"
                ];

                $chops = DB::table($_table->BASETABLE)
                    ->where('date', '=', $_date)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->first();

                if (!empty($chops)){
                    $id = $chops->id;
                } else {
                    $generate_id = $_global_class->generateID($_table->NAME);
                    $data += ["id" => $generate_id];
                    $data += ["date" => $_date];
                    $data += ["number" => $request->number];
                    $data += ["is_active" => $_table->STATUS_ACTIVE];

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

            if (strtolower($table) === "carton") {
                $_table = new Carton();
                $id = $request->id;
                $carton = $request->carton;
                $_date = date("d-m-Y");

                $fields = [
                    "carton"
                ];

                $cartons = DB::table($_table->BASETABLE)
                    ->where('id_group' , '=', $id)
                    ->where('date', '=', $_date)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->first();

                if (!empty($cartons)){
                    $id = $cartons->id;
                } else {
                    $generate_id = $_global_class->generateID($_table->NAME);
                    $data += ["id" => $generate_id];
                    $data += ["id_group" => $id];
                    $data += ["date" => $_date];
                    $data += ["carton" => $carton];
                    $data += ["is_active" => $_table->STATUS_ACTIVE];

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

            if (strtolower($table) === "carton_by_date") {
                $_table = new Carton();
                $id = $request->id;
                $carton = $request->carton;
                $_date = $request->date;

                $fields = [
                    "carton"
                ];

                $cartons = DB::table($_table->BASETABLE)
                    ->where('id_group' , '=', $id)
                    ->where('date', '=', $_date)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->first();

                if (!empty($cartons)){
                    $id = $cartons->id;
                } else {
                    $generate_id = $_global_class->generateID($_table->NAME);
                    $data += ["id" => $generate_id];
                    $data += ["id_group" => $id];
                    $data += ["date" => $_date];
                    $data += ["carton" => $carton];
                    $data += ["is_active" => $_table->STATUS_ACTIVE];

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

            if (strtolower($table) === "account") {
                $_table = new Account();
                $fields = [
                    "name", "number"
                ];
            }

            if (strtolower($table) === "expenses") {
                $_table = new Expense();
                $fields = [
                    "id_account", "date", "name", "nominal", "description"
                ];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $_data = DB::table($_table->BASETABLE)
                    ->where('id', '=', $id)
                    ->update($data);

                if ($_data){
                    $nominal = $request->nominal;
                    $date = $request->date;
                    $check_acc = $this->accountancy('update','credit', $id, $date, $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Accountancy Updated successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                }
            }

            if (strtolower($table) === "income") {
                $_table = new Income();
                $fields = [
                    "date", "name", "nominal", "description"
                ];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $_data = DB::table($_table->BASETABLE)
                    ->where('id', '=', $id)
                    ->update($data);

                if ($_data){
                    $nominal = $request->nominal;
                    $date = $request->date;
                    $check_acc = $this->accountancy('update','debit', $id, $date, $nominal);
                    if ($check_acc){
                        $feedback = [
                            "message" => "Accountancy Updated successfully",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];

                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "There is something error. Please try again later. [Accountancy]",
                            "status" => $_global_class->STATUS_ERROR,
                        ];

                        return response()->json($feedback);
                    }
                }
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

    public function destroy_data(){
        date_default_timezone_set("Asia/Jakarta");
        $_global_class = new GlobalClass();
        $_table = null;

        $_date = date("d-m-Y");

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

            if (strtolower($table) === "group_detail") {
                $_table = new GroupDetail();
            }

            if (strtolower($table) === "chop") {
                $_table = new Chop();

                $chops = DB::table($_table->BASETABLE)
                    ->where('date', '=', $_date)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->first();

                if (!empty($chops)){
                    $id = $chops->id;
                }
            }

            if (strtolower($table) === "attendance") {
                $_table = new Attendance();
            }

            if (strtolower($table) === "carton") {
                $_table = new Carton();
            }

            $_data = DB::table($_table->BASETABLE)
                ->where('id', '=', $id)
                ->delete();

            $feedback = [
                "message" => $table . ' Destroy Successfully.',
                "status" => $_global_class->STATUS_SUCCESS,
            ];

            return response()->json($feedback);

        }
    }

    public function accountancy($STATUS, $PART, $ID_ACTIVITY, $DATE, $NOMINAL){
        /**
         * STATUS   => ADD / UPDATE / DESTROY
         * PART     => DEBIT / CREDIT
         */

        $_global_class = new GlobalClass();
        $_table = new Accountancy();
//
//
//        $acc_id = $this->accountancy_get_id_by_activity($ID_ACTIVITY);
//        $acc_date = $this->accountancy_get_date_by_activity($ID_ACTIVITY);
//        $acc_saldo_before = $_global_class->removeMoneySeparator($this->accountancy_check_saldo_before($acc_id));
//        $acc_nominal_temp = $_global_class->removeMoneySeparator($NOMINAL);

        $data_acc = array();
        $data_acc += ["date" => $DATE];
        if (strtolower($PART) === "debit"){
            $data_acc += ["debit" => $NOMINAL];
            $data_acc += ["credit" => '-'];
//            $acc_saldo_now = $_global_class->addMoneySeparator(((int)$acc_saldo_before + (int)$acc_nominal_temp), 0);
//            $data_acc += ["saldo" => $acc_saldo_now];
//
//            $data = [
//                'id' => $acc_id,
//                'date' => $acc_date,
//                'saldo' => $acc_saldo_before,
//                'nominal' => $acc_nominal_temp,
//                'now' => $acc_saldo_now
//            ];
//
//            return response()->json($data);
        } else {
            $data_acc += ["debit" => '-'];
            $data_acc += ["credit" => $NOMINAL];

/*//            $acc_saldo_now = (int)$acc_saldo_before - (int)$acc_nominal_temp;
//            $data_acc += ['saldo' => $_global_class->addMoneySeparator($acc_saldo_now, 0)];*/
        }
        $data_acc += ["is_active" => $_table->STATUS_ACTIVE];

        if (strtolower($STATUS) === "add"){
            $generate_id = $_global_class->generateID($_table->NAME);
            $data_acc += ["id" => $generate_id];
            $data_acc += ["saldo" => "-"];
            $data_acc += ["id_activity" => $ID_ACTIVITY];

            $check_insert = DB::table($_table->BASETABLE)->insert($data_acc);

            if($check_insert){
//                return true;
                $status_balance_check = $this->balance_check($DATE, $PART, $NOMINAL);
                return $status_balance_check;
                /**
                 * CHECK BALANCE BY DATE
                 */

            } else {
                return false;
            }
        }

        if (strtolower($STATUS) === "update") {
            $check_update = DB::table($_table->BASETABLE)
                ->where('id_activity', '=', $ID_ACTIVITY)
                ->update($data_acc);

            if($check_update){
//                return true;
                $status_balance_check = $this->balance_check($DATE, $PART, $NOMINAL);
                return $status_balance_check;
//                $check_update_after_id = $this->accountancy_update_saldo_after_update_nominal($acc_id, $acc_date, $acc_saldo_before);
//                if ($check_update_after_id){
//                    return true;
//                } else {
//                    return false;
//                }
            } else {
                return false;
            }
        }
    }

    function balance_check($DATE, $STATUS, $NOMINAL){
        /**
         * CHECK BALANCE BY DATE
         *
         * $DATE => date
         * $STATUS => debit / credit
         * $NOMINAL => new nominal
         */

        $_global_class = new GlobalClass();
        $_table = new Balance();
        $saldo_after = 0;
        $balance = DB::table($_table->BASETABLE)
            ->where('date', '=', $DATE)
            ->where('is_active', '=', $_table->STATUS_ACTIVE)
            ->first();

        if (!empty($balance)){
            $saldo_before = $balance->nominal;

            if (strtolower($STATUS) === "debit"){
                $saldo_after = $_global_class->removeMoneySeparator($saldo_before) + $_global_class->removeMoneySeparator($NOMINAL);
            }

            if (strtolower($STATUS) === "credit"){
                $saldo_after = $_global_class->removeMoneySeparator($saldo_before) - $_global_class->removeMoneySeparator($NOMINAL);
            }

            $status_update = $this->balance_update($DATE, $saldo_after);
            return $status_update;
        } else {
            $saldo_before = $this->balance_get_saldo_date_before($DATE);
            if (!$saldo_before){
                $saldo_before = 0;
            }

            if (strtolower($STATUS) === "debit"){
                $saldo_after = $saldo_before + $_global_class->removeMoneySeparator($NOMINAL);
            }

            if (strtolower($STATUS) === "credit"){
                $saldo_after = $saldo_before - $_global_class->removeMoneySeparator($NOMINAL);
            }

            $status_add = $this->balance_add($DATE, $saldo_after);
            return $status_add;
        }
    }

    function balance_get_saldo_date_before($DATE){
        $_table = new Balance();
        $balance = DB::table($_table->BASETABLE)
            ->where('date', '<', $DATE)
            ->where('is_active', '=', $_table->STATUS_ACTIVE)
            ->orderBy('date', 'DESC')
            ->first();

        if (!empty($balance)){
            return $balance->nominal;
        } else {
            return false;
        }
    }

    function balance_add($DATE, $NOMINAL){
        $_global_class = new GlobalClass();
        $_table = new Balance();
        $data_bal = array();
        $data_bal += ["date" => $DATE];
        $data_bal += ["nominal" => $_global_class->addMoneySeparator($NOMINAL, 0)];
        $generate_id = $_global_class->generateID($_table->NAME);
        $data_bal += ["id" => $generate_id];
        $data_bal += ["is_active" => $_table->STATUS_ACTIVE];
        $check_insert = DB::table($_table->BASETABLE)->insert($data_bal);
        if($check_insert){
            return true;
        } else {
            return false;
        }
    }

    function balance_update($DATE, $NOMINAL){
        $_global_class = new GlobalClass();
        $_table = new Balance();
        $data_bal = array();
        $data_bal += ["nominal" => $_global_class->addMoneySeparator($NOMINAL, 0)];

        $check_update = DB::table($_table->BASETABLE)
            ->where('date', '=', $DATE)
            ->update($data_bal);

        if($check_update){
            return true;
        } else {
            return false;
        }
    }

    function accountancy_get_id_by_activity($ID){
        $_table = new Accountancy();

        $_data = DB::table($_table->BASETABLE)
            ->where('id_activity', '=', $ID)
            ->where('is_active', '=', $_table->STATUS_ACTIVE)
            ->first();

        if (!empty($_data)){
            return $_data->id;
        } else {
            return false;
        }
    }

    function accountancy_get_date_by_activity($ID){
        $_table = new Accountancy();

        $_data = DB::table($_table->BASETABLE)
            ->where('id_activity', '=', $ID)
            ->where('is_active', '=', $_table->STATUS_ACTIVE)
            ->first();

        if (!empty($_data)){
            return $_data->date;
        } else {
            return false;
        }
    }

    function accountancy_check_saldo_before($ID){
        $_table = new Accountancy();

        $_data = DB::table($_table->BASETABLE)
            ->where('id', '<', $ID)
            ->where('is_active', '=', $_table->STATUS_ACTIVE)
            ->orderBy('id', 'DESC')
            ->orderBy('date', 'ASC')
            ->first();

        if (!empty($_data)){
            return $_data->saldo;
        } else {
            return false;
        }
    }

    function accountancy_update_saldo_after_update_nominal($ID, $DATE, $SALDO){
        $_global_class = new GlobalClass();
        $_table = new Accountancy();
        $_datas = DB::table($_table->BASETABLE)
            ->where('id', '>', $ID)
            ->where('date', '>', $DATE)
            ->where('is_active', '=', $_table->STATUS_ACTIVE)
            ->get();

        if (count($_datas) > 0) {
            foreach ($_datas as $datas => $data) {
                $data_id = $data->id;
                $data_debit = $data->debit;
                $data_credit = $data->credit;

                if ($data_credit === "-"){
                    $new_saldo = $_global_class->removeMoneySeparator($SALDO) + $_global_class->removeMoneySeparator($data_debit);
                    $data_acc = array();
                    $data_acc += ["saldo" => $new_saldo];
                    $check_update = DB::table($_table->BASETABLE)
                        ->where('id', '=', $data_id)
                        ->update($data_acc);
                }

                if ($data_debit === "-"){
                    $new_saldo = $_global_class->removeMoneySeparator($SALDO) - $_global_class->removeMoneySeparator($data_debit);
                    $data_acc = array();
                    $data_acc += ["saldo" => $new_saldo];
                    $check_update = DB::table($_table->BASETABLE)
                        ->where('id', '=', $data_id)
                        ->update($data_acc);
                }
            }
        }

        return true;
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
            $potongan_bpjs = $request->potongan_bpjs;
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
                "potongan_bpjs" => $potongan_bpjs,
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
            "potongan_bpjs",
            "dob",
            "start_date",
            "gender",
            "status"
        ];

        $index = [1,2,3,4,5,6,7,8,9,10,11,12,13];

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

                if($i===12){
//                    $gender = null;
                    if(strtolower($request->$field) === "male"){
//                        $gender = $_employee->GENDER_MALE;
                        $data += ["gender" => $_employee->GENDER_MALE];
                    } else {
//                        $gender = $_employee->GENDER_FEMALE;
                        $data += ["gender" => $_employee->GENDER_FEMALE];
                    }
//                    $data += ["gender" => $gender];
                }

                if($i===13){
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
