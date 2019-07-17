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
use App\Model\LiburSatpam;
use App\Model\Payment;
use App\Model\Product;
use App\Model\Purchase;
use App\Model\RevisionSalary;
use App\Model\Sales;
use App\Model\SalesDetail;
use App\Model\Standard;
use App\Model\Supplier;
use Codedge\Fpdf\Fpdf\Fpdf;
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
                        $temp = array(
                            "id" => $data_user->id,
                            "role" => $data_user->role
                        );
                        $feedback = [
                            "message" => $temp,
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

            if(strtolower($table) === "libur_satpam"){
                $_table = new LiburSatpam();
                $fields = [
                    "id_employee", "date"
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
                    "id_employee", "carton"
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
                } elseif(strtolower($status) === "sakit"){
                    $status = $_table->STATUS_SAKIT;
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
                    "id_distributor", "nota_number", "date", "total"
                ];

                foreach ($fields as $field) {
                    ${$field} = $request->$field;
                    $data += ["{$field}" => "${$field}"];
                }

                $generate_id = $_global_class->generateID($_table->NAME);
                $data += ["id" => $generate_id];
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

            if (strtolower($table) === "libur_satpam") {
                $_table = new LiburSatpam();

                if (strtolower($id) === "all") {
                    $_data = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                } elseif (strtolower($id) === "month") {
                    $id_employee = $request->id_employee;
                    $_month = date("m");
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id_employee', '=', $id_employee)
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_month)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->orderBy('date', 'asc')
                        ->get();
                } elseif (strtolower($id) === "id_date") {
                    $id_employee = $request->id_employee;
                    $date = $request->date;
                    $_data = DB::table($_table->BASETABLE)
                        ->where('id_employee', '=', $id_employee)
                        ->where('date', '=', $date)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();
                    if (!empty($_data)){
                        $feedback = [
                            "message" => "Date already exist for this employee.",
                            "status" => $_global_class->STATUS_ERROR,
                        ];
                        return response()->json($feedback);
                    } else {
                        $feedback = [
                            "message" => "Okay.",
                            "status" => $_global_class->STATUS_SUCCESS,
                        ];
                        return response()->json($feedback);
                    }
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

                } else if (strtolower($id) === "satpam") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('status', '=', $_table->STATUS_SATPAM)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_employee) > 0) {
                        foreach ($_employee as $emplo => $emp) {
                            $_emp_id = $emp->id;

                            $temp = array(
                                "id" => $emp->id,
                                "first_name" => $emp->first_name,
                                "last_name" => $emp->last_name,
                            );

                            array_push($_data, $temp);
                        }
                    }

                } else if (strtolower($id) === "bulanan") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('status', '=', $_table->STATUS_SATPAM)
                        ->orWhere('status', '=', $_table->STATUS_SUPIR)
                        ->orWhere('status', '=', $_table->STATUS_BULANAN)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    if (count($_employee) > 0) {
                        foreach ($_employee as $emplo => $emp) {
                            $_emp_id = $emp->id;

                            if ($emp->status === 4) {
                                $status = "Bulanan";
                            } elseif ($emp->status === 5) {
                                $status = "Satpam";
                            } elseif ($emp->status === 6) {
                                $status = "Supir";
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

                } else if (strtolower($id) === "harian") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('status', '=', $_table->STATUS_HARIAN_ATAS)
                        ->orWhere('status', '=', $_table->STATUS_HARIAN_BAWAH)
                        ->orWhere('status', '=', $_table->STATUS_BORONGAN)
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

                    $status = "";
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

                if (strtolower($id) === "harian") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('status', '=', $_table->STATUS_HARIAN_ATAS)
                        ->orWhere('status', '=', $_table->STATUS_HARIAN_BAWAH)
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
                } elseif (strtolower($id) === "bulanan") {
                    $_employee = DB::table($_table->BASETABLE)
                        ->where('status', '=', $_table->STATUS_BULANAN)
                        ->orWhere('status', '=', $_table->STATUS_SUPIR)
                        ->orWhere('status', '=', $_table->STATUS_SATPAM)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    $_table = new Attendance();
                    $_date = date("d");
                    $_month = date("m");
                    $_year = date("Y");

                    if (count($_employee) > 0) {
                        foreach ($_employee as $emplo => $emp) {
                            $_emp_id = $emp->id;

                            if ($emp->status === 4) {
                                $status = "Bulanan";
                            } elseif ($emp->status === 5) {
                                $status = "Satpam";
                            } elseif ($emp->status === 6) {
                                $status = "Supir";
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
                } elseif (strtolower($id) === "borongan") {
                    $_date = date("d-m-Y");
                    $group_carton_id = "";
                    $group_carton = "0";
                    $temp2 = array();
                    $_table = new GroupHeader();
                    $_group_headers = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                    if (count($_group_headers) > 0) {
                        foreach ($_group_headers as $_group_header => $group_header) {
                            $_table2 = new GroupDetail();
                            $_group_details = DB::table($_table2->BASETABLE)
                                ->join('employee', 'employee.id', '=', 'group_detail.id_employee')
                                ->where('group_detail.id_group', '=', $group_header->id)
                                ->select('employee.id as employee_id', 'employee.first_name', 'employee.last_name')
                                ->get();
                            if (count($_group_details) > 0) {
                                foreach ($_group_details as $_group_detail => $group_detail) {
                                    $_date = date("d");
                                    $_month = date("m");
                                    $_year = date("Y");

                                    $_is_att = DB::select(DB::raw("SELECT * FROM attendance
                                                            WHERE id_employee = '$group_detail->employee_id'
                                                            AND SUBSTR(`date`,1,2) = '$_date'
                                                            AND SUBSTR(`date`,4,2) = '$_month'
                                                            AND SUBSTR(`date`,7,4) = '$_year'
                                                            AND is_active = '1'"));

                                    $att_status = "";
                                    $att_id = "";
                                    $att_carton = "";

                                    if (count($_is_att) > 0) {
                                        foreach ($_is_att as $is_att => $att) {
                                            $att_status = $att->status;
                                            $att_id = $att->id;
                                            $att_carton = $att->carton;
                                        }
                                    }


                                    $temp3 = array(
                                        "employee_id" => $group_detail->employee_id,
                                        "employee_fname" => $group_detail->first_name,
                                        "employee_lname" => $group_detail->last_name,
                                        "attendance_id" => $att_id,
                                        "attendance_status" => $att_status,
                                        "attendance_carton" => $att_carton,
                                    );

                                    array_push($temp2, $temp3);
                                }
                                $temp3 = array();
                            }


                            $_table3 = new Carton();
                            $cartons = DB::table($_table3->BASETABLE)
                                ->where('id_group', '=', $group_header->id)
                                ->where('date', '=', date("d-m-Y"))
                                ->where('is_active', '=', $_table3->STATUS_ACTIVE)
                                ->first();

                            if (!empty($cartons)) {
                                $group_carton_id = $cartons->id;
                                $group_carton = $cartons->carton;
                            } else {
                                $group_carton_id = "";
                                $group_carton = "0";
                            }

                            $temp = array(
                                "group_id" => $group_header->id,
                                "group_name" => $group_header->name,
                                "group_carton_id" => $group_carton_id,
                                "group_carton" => $group_carton,
                                "employee_datas" => $temp2
                            );
                            array_push($_data, $temp);
                            $temp2 = array();
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
                } elseif (strtolower($id) === "update_attendance_borongan") {
                    $id_employee = $request->id_employee;
                    $date = $request->date;

                    $_table = new Carton();
                    $_cartons = DB::table($_table->BASETABLE)
                        ->where('id_group', '=', $id_employee)
                        ->where('date', '=', $date)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->first();

                    $temp2 = array();

                    $_table2 = new GroupDetail();
                    $_gds = DB::table($_table2->BASETABLE)
                        ->where('id_group', '=', $id_employee)
                        ->where('is_active', '=', $_table2->STATUS_ACTIVE)
                        ->get();
                    if (count($_gds) > 0) {
                        foreach ($_gds as $_gd => $gd) {
                            $_table3 = new Attendance();
                            $_emp_id = $gd->id_employee;

                            $_atts = DB::table($_table3->BASETABLE)
                                ->join('employee', 'employee.id', '=', 'attendance.id_employee')
                                ->where('attendance.id_employee', '=', $_emp_id)
                                ->where('attendance.date', '=', $date)
                                ->select('attendance.id as att_id', 'attendance.date', 'attendance.status', 'employee.id as employee_id', 'employee.first_name', 'employee.last_name', 'attendance.carton')
                                ->get();

                            if (count($_atts) > 0) {
                                foreach ($_atts as $_att => $att) {
                                    $temp3 = array(
                                        "employee_id" => $att->employee_id,
                                        "employee_fname" => $att->first_name,
                                        "employee_lname" => $att->last_name,
                                        "attendance_id" => $att->att_id,
                                        "attendance_status" => $att->status,
                                        "attendance_carton" => $att->carton
                                    );

                                    array_push($temp2, $temp3);
                                }
                            }
                        }
                    }

                    $_table4 = new GroupHeader();
                    $_ghs = DB::table($_table4->BASETABLE)
                        ->where('id', '=', $id_employee)
                        ->where('is_active', '=', $_table4->STATUS_ACTIVE)
                        ->first();

                    $temp = array(
                        "group_id" => $_ghs->id,
                        "group_name" => $_ghs->name,
                        "carton_id" => $_cartons->id,
                        "carton" => $_cartons->carton,
                        "employee_datas" => $temp2
                    );

                    array_push($_data, $temp);

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
                $_year = date("Y");
                $_month = date("m");
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
                $sakit = 0;
                $sakit_name = null;
                $sakit_name_arr = [];
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
                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                                ->where('id_group', $gh_id)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();
                            if ($_start_date[1] !== $_end_date[1]) {
                                $_cartons_temps = DB::table($_table->BASETABLE)
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                                    ->where('id_group', $gh_id)
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

//                            $_table = new Holiday();
//                            if ($_start_date[1] !== $_end_date[1]){
//                                $_holidays = DB::select(DB::raw("SELECT COUNT(`id`) as total
//                                                        FROM $_table->BASETABLE
//                                                        WHERE (`date` >= '$start_date' OR `date` <= '$end_date')
//                                                        AND is_active = $_table->STATUS_ACTIVE"));
//
//                                foreach ($_holidays as $holidays => $holiday) {
//                                    $_holiday = $holiday->total;
//                                }
//                            } else {
//                                $_holiday = DB::table($_table->BASETABLE)
//                                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '>=', $_start_date[0])
//                                    ->where(\DB::raw('SUBSTR(`date`,1,2)'), '<=', $_end_date[0])
//                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
//                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
//                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
//                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
//                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                    ->count();
//                            }

                            $_holiday = 0;
                            $_table = new Holiday();
                            $_holiday_temps = DB::table($_table->BASETABLE)
                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
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

//                            $_haids = DB::table('haid')
//                                ->where('date', '>=', $start_date)
//                                ->where('date', '<=', $end_date)
//                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
//                                ->get();

                            $_haids = [];
                            $_table = new Haid();
                            $_haids_temps = DB::table($_table->BASETABLE)
                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
                                ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                                ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                ->get();
                            if ($_start_date[1] !== $_end_date[1]) {
                                $_haids_temps = DB::table($_table->BASETABLE)
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                                    ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                    ->get();
                            }
                            if (count($_haids_temps) > 0) {
                                foreach ($_haids_temps as $_haids_temp => $_haidstemp) {
                                    $_haidst_date = $_haidstemp->date;
                                    $_haids_date = date('Y-m-d', strtotime($_haidst_date));

                                    $_haids_start = date('Y-m-d', strtotime($start_date));
                                    $_haids_end = date('Y-m-d', strtotime($end_date));

                                    if (($_haids_date >= $_haids_start) && ($_haids_date <= $_haids_end)) {
                                        $temp1 = array(
                                            "id" => $_haidstemp->id,
                                            "id_employee" => $_haidstemp->id_employee,
                                        );

                                        array_push($_haids, $temp1);
                                    }
                                }
                            }

                            if (count($_haids) > 0) {
                                foreach ($_haids as $_haid => $hid) {
                                    $_checkgroup = DB::table('group_detail')
                                        ->where('id_employee', '=', $hid['id_employee'])
                                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                        ->first();

                                    if (!empty($_checkgroup)) {
                                        if ($_checkgroup->id_group === $gh_id) {
                                            $haid = $haid + 1;

                                            $_employees = DB::table('employee')
                                                ->where('id', '=', $hid['id_employee'])
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

//                                    $_atts = DB::table('attendance')
//                                        ->join('employee', 'employee.id', '=', 'attendance.id_employee')
//                                        ->where('attendance.date', '>=', $start_date)
//                                        ->where('attendance.date', '<=', $end_date)
//                                        ->where('attendance.id_employee', '=', $gd_id_empl)
//                                        ->select('employee.id', 'employee.first_name', 'employee.last_name', 'attendance.id', 'attendance.status', 'employee.start_date')
//                                        ->get();

                                    $_atts = [];
                                    $_table = new Attendance();
                                    $_atts_temps = DB::table($_table->BASETABLE)
                                        ->join('employee', 'employee.id', '=', 'attendance.id_employee')
                                        ->where(\DB::raw('SUBSTR(attendance.`date`,4,2)'), '=', $_start_date[1])
                                        ->where(\DB::raw('SUBSTR(attendance.`date`,7,4)'), '=', $_year)
                                        ->where('attendance.id_employee', '=', $gd_id_empl)
                                        ->select('attendance.date', 'employee.id as employee_id', 'employee.first_name', 'employee.last_name', 'attendance.id as attendance_id', 'attendance.status', 'employee.start_date')
                                        ->get();
                                    if ($_start_date[1] !== $_end_date[1]) {
                                        $_atts_temps = DB::table($_table->BASETABLE)
                                            ->join('employee', 'employee.id', '=', 'attendance.id_employee')
                                            ->where(\DB::raw('SUBSTR(attendance.`date`,4,2)'), '>=', $_start_date[1])
                                            ->where(\DB::raw('SUBSTR(attendance.`date`,4,2)'), '<=', $_end_date[1])
                                            ->where(\DB::raw('SUBSTR(attendance.`date`,7,4)'), '=', $_start_date[2])
                                            ->where(\DB::raw('SUBSTR(attendance.`date`,7,4)'), '=', $_end_date[2])
                                            ->where('attendance.id_employee', '=', $gd_id_empl)
                                            ->select('attendance.date', 'employee.id as employee_id', 'employee.first_name', 'employee.last_name', 'attendance.id as attendance_id', 'attendance.status', 'employee.start_date')
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
                                                    "employee_id" => $_attstemp->employee_id,
                                                    "employee_first_name" => $_attstemp->first_name,
                                                    "employee_last_name" => $_attstemp->last_name,
                                                    "employee_start_date" => $_attstemp->start_date,
                                                    "attendance_id" => $_attstemp->attendance_id,
                                                    "attendance_status" => $_attstemp->status,
                                                );

                                                array_push($_atts, $temp1);
                                            }
                                        }
                                    }

                                    if (count($_atts) > 0) {
                                        foreach ($_atts as $atts => $att) {
                                            $att_stat = $att['attendance_status'];

                                            if ($att['employee_first_name'] === $att['employee_last_name']){
                                                $empl_name = $att['employee_first_name'];
                                            } else {
                                                $empl_name = $att['employee_first_name'] . ' ' . $att['employee_last_name'];
                                            }

                                            if ($att_stat === '3') {
                                                $ijin = $ijin + 1;

                                                if (!in_array($empl_name, $ijin_name_arr)) {
                                                    array_push($ijin_name_arr, $empl_name);
                                                    $ijin_name .= $empl_name . '@!#';
                                                }
                                            }
                                            if ($att_stat === '5') {
                                                $sakit = $sakit + 1;

                                                if (!in_array($empl_name, $sakit_name_arr)) {
                                                    array_push($sakit_name_arr, $empl_name);
                                                    $sakit_name .= $empl_name . '@!#';
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
                            $total_sakit = $sakit * $upah_harian;
                            $_total = ($total_carton + $total_haid + $total_ijin + $total_sakit + $_upah_libur) - $potongan_bpjs;

                            $temp = array(
                                "id_group" => $gh_id,
                                "group_name" => $gh->name,
                                "carton" => $_carton,
                                "haid" => $haid,
                                "haid_name" => $haid_name,
                                "potongan_bpjs" => $potongan_bpjs,
                                "ijin" => $ijin,
                                "ijin_name" => $ijin_name,
                                "sakit" => $sakit,
                                "sakit_name" => $sakit_name,
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
                            $sakit = 0;
                            $sakit_name = null;
                            $sakit_name_arr = [];
                            $potongan_bpjs = 0;
                            $_upah_libur = 0;
                            $_total = 0;
                        }
                    }
                } // else here
            }


            if (strtolower($table) === "gaji_harian"){
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $potongan_bpjs = $request->potongan_bpjs;
                $_year = date("Y");
                $_data = [];
                $_date = [];
                $_pokok = 0;
                $_premi = 0;
                $_haid = 0;
                $_pot_bpjs = 0;
                $_std_harian = 0;
                $_std_haid = 0;
                $_masuk = 0;
                $_setengah_hari = 0;
                $_ijin = 0;
                $_tidak_masuk = 0;
                $_tot = 0;
                $_holiday = 0;
                $_holiday_arr = [];
                $_chop = 0;
                $_chop_arr = [];
                $_start_date = explode('-', $start_date);
                $_end_date = explode('-', $end_date);
                $_conv_start_date = date('Y-m-d', strtotime($start_date));
                $_conv_end_date = date('Y-m-d', strtotime($end_date));
                if (strtolower($id) === "all") {

                    // => GET ALL DATA FROM STANDARD BY YEAR
                    $_table = new Standard();
                    $_standards = DB::table($_table->BASETABLE)
                        ->where('year', '=', $_year)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                    if (count($_standards) > 0) {
                        foreach ($_standards as $_standard => $standard) {
                            if ($standard->name === "upah_harian"){
                                $_std_harian = $_global_class->removeMoneySeparator($standard->nominal);
                            }
                            if ($standard->name === "cuti_haid"){
                                $_std_haid = $_global_class->removeMoneySeparator($standard->nominal);
                            }
                        }
                    }

                    // => GET ALL DATE FROM HOLIDAY BY YEAR
                    $_table = new Holiday();
                    $_holidays = DB::table($_table->BASETABLE)
                        ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();

                    // => CHECK IF THERE IS AN HOLIDAY BETWEEN START AND END DATE
                    if (count($_holidays) > 0) {
                        foreach ($_holidays as $_holiday => $holiday) {
                            $_conv_holiday_date = date('Y-m-d', strtotime($holiday->date));
                            if (($_conv_holiday_date >= $_conv_start_date) && ($_conv_holiday_date <= $_conv_end_date)) {
                                array_push($_date, $_conv_holiday_date);
                                array_push($_holiday_arr, $_conv_holiday_date);
                            }
                        }
                    }

                    // => GET ALL DATA FROM ATTENDANCE
                    $_table = new Attendance();
                    $_attendances = DB::table($_table->BASETABLE)
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
                        ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                    if ($_start_date[1] !== $_end_date[1]) {
                        $_attendances = DB::table($_table->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->get();
                    }

                    // => CHECK IF THERE IS AN ATTENDANCE DATE BETWEEN START AND END DATE
                    if (count($_attendances) > 0) {
                        foreach ($_attendances as $_attendance => $attendance) {
                            $_conv_attendance_date = date('Y-m-d', strtotime($attendance->date));
                            if (($_conv_attendance_date >= $_conv_start_date) && ($_conv_attendance_date <= $_conv_end_date)) {
                                if (!in_array($_conv_attendance_date, $_date)){
                                    array_push($_date, $_conv_attendance_date);
                                }
                            }
                        }
                    }

                    // => GET ALL DATA FROM CHOP
                    $_table = new Chop();
                    $_chops = DB::table($_table->BASETABLE)
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
                        ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_year)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->get();
                    if ($_start_date[1] !== $_end_date[1]) {
                        $_chops = DB::table($_table->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('is_active', '=', $_table->STATUS_ACTIVE)
                            ->get();
                    }

                    // => CHECK IF THERE IS AN CHOPS DATE BETWEEN START AND END DATE
                    if (count($_chops) > 0) {
                        foreach ($_chops as $_chop => $chop) {
                            $_conv_chop_date = date('Y-m-d', strtotime($chop->date));
                            if (($_conv_chop_date >= $_conv_start_date) && ($_conv_chop_date <= $_conv_end_date)) {
                                if (!in_array($_conv_chop_date, $_chop_arr)){
                                    if ($chop->number === (string)$_table->NUMBER_SINGAPORE){
                                        array_push($_chop_arr, $_conv_chop_date);
                                        array_push($_chop_arr, $_conv_chop_date);
                                        $_chop += 2;
                                    } else {
                                        array_push($_chop_arr, $_conv_chop_date);
                                        $_chop++;
                                    }
                                }
                            }
                        }
                    }

                    sort($_date);

//                    $temp_arr_attendance = [];
                    // => GET ALL EMPLOYEES BY STATUS
                    $_table = new Employee();
                    $_stat_harian_atas = $_table->STATUS_HARIAN_ATAS;
                    $_stat_harian_bawah = $_table->STATUS_HARIAN_BAWAH;
                    $_employees = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->where('status', '=', $_table->STATUS_HARIAN_ATAS)
                        ->orWhere('status', '=', $_table->STATUS_HARIAN_BAWAH)
                        ->get();
                    if (count($_employees) > 0) {
                        foreach ($_employees as $_employee => $employee) {
                            $temp_arr_attendance = [];
                            for ($i=0; $i<count($_date); $i++){
                                // => GET ALL ATTENDANCE BY DATE
                                $_temp_date = explode("-", $_date[$i])[2] . '-' . explode("-", $_date[$i])[1] . '-' . explode("-", $_date[$i])[0];
                                $_table = new Attendance();
                                $___attendances = DB::table($_table->BASETABLE)
                                    ->where('id_employee', '=', $employee->id)
                                    ->where('date', '=', $_temp_date)
                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                    ->first();
                                if (!empty($___attendances)){
                                    $arr_attendance = array(
                                        "att_id" => $___attendances->id,
                                        "date" => $___attendances->date,
                                        "status" => $___attendances->status,
                                    );
                                    if ($___attendances->status === (string)$_table->STATUS_MASUK){
                                        $_pokok += $_std_harian;
                                        $_masuk += 1;
//                                        CHECK IF TODAY IS CHOP
                                        if ($employee->status === $_stat_harian_atas){
                                            $_premi += $_global_class->removeMoneySeparator($employee->premi);
                                        }
                                        if ($employee->status === $_stat_harian_bawah){
                                            if (in_array($_date[$i], $_chop_arr)){
                                                $_premi += $_global_class->removeMoneySeparator($employee->premi);
                                            }
                                        }
                                    }
                                    if ($___attendances->status === (string)$_table->STATUS_IJIN){
                                        $_pokok += $_std_harian;
                                        if (in_array($_date[$i], $_chop_arr)){
                                            $_premi += 0;
                                        }
                                        $_ijin += 1;
                                    }
                                    if ($___attendances->status === (string)$_table->STATUS_SAKIT){
                                        $_pokok += $_std_harian;
                                        $_premi += 0;
                                        $_ijin += 1;
                                    }
                                    if ($___attendances->status === (string)$_table->STATUS_SETENGAH_HARI){
                                        $_pokok += ($_std_harian / 2);
                                        $ctr_temp = 0;
                                        
                                        if ($employee->status === $_stat_harian_atas){
                                            $_premi += $_global_class->removeMoneySeparator($employee->premi) / 2;
                                        }
                                        
                                        if ($employee->status === $_stat_harian_bawah){
                                            if (in_array($_date[$i], $_chop_arr)){
                                                $_premi += $_global_class->removeMoneySeparator($employee->premi) / 2;
                                            }
                                        }
                                        
                                       
//                                        for ($i = 0; $i < count($_chop_date_arr); $i++) {
//                                            if ($_chop_date_arr[$i] === $att_date){
//                                                $ctr_temp++;
//                                            }
//                                        }
//                                        $_premi += (($empl_premi * $ctr_temp) / 2);
                                        $_setengah_hari += 1;
                                    }

                                    if ($___attendances->status === (string)$_table->STATUS_TIDAK_MASUK){
                                        $_tidak_masuk += 1;
                                    }
                                } else {
                                    if ($_global_class->checkDifferenceBetweenTwoDate($employee->start_date, date("d-m-Y")) >= 12){
                                        $_pokok += $_std_harian;
                                    }
                                    $arr_attendance = array(
                                        "att_id" => "",
                                        "date" => $_temp_date,
                                        "status" => "",
                                    );
                                }
                                array_push($temp_arr_attendance, $arr_attendance);

                                $_table = new Haid();
                                $_haids = DB::table($_table->BASETABLE)
                                    ->where('id_employee', '=', $employee->id)
                                    ->where('date', '=', $_temp_date)
                                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                                    ->first();
                                if (!empty($_haids)) {
                                    $_haid += $_std_haid;
                                }
                            }

                            if ($potongan_bpjs){
                                $_pot_bpjs += $_global_class->removeMoneySeparator($employee->potongan_bpjs);
                            }

                            $_tot = ($_pokok + $_premi + $_haid) - $_pot_bpjs;

                            $nm = "";
                            if ($employee->first_name === $employee->last_name){
                                $nm = $employee->first_name;
                            } else {
                                $nm = $employee->first_name . ' ' . $employee->last_name;
                            }
                            $temp = array(
                                "id_employee" => $employee->id,
                                "employee_name" => $nm,
                                "gender" => $employee->gender,
                                "_attendance" => $temp_arr_attendance,
                                "msit" => $_masuk . ' | ' . $_setengah_hari . ' | ' . $_ijin . ' | ' . $_tidak_masuk,
                                "libur" => count($_holiday_arr),
                                "rajang" => count($_chop_arr),
                                "_rajamg" => $_chop_arr,
                                "pokok" => $_pokok,
                                "premi" => $_premi,
                                "haid" => $_haid,
                                "potongan_bpjs" => $_pot_bpjs,
                                "total" => $_tot,
                                "_date" => $_date
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


//                    $temp = array(
//                        "start_date" => $start_date,
//                        "end_date" => $end_date,
//                        "potongan_bpjs" => $potongan_bpjs,
//                        "_date" => $_date
//                    );
//
//                    array_push($_data, $temp);
                }
            }


            if (strtolower($table) === "old_gaji_harian") {
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
                $_temp_haid_date = "";

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
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
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
                        ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
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

                    $_table = new Employee();
                    $_empls = DB::table($_table->BASETABLE)
                        ->where('is_active', '=', $_table->STATUS_ACTIVE)
                        ->where('status', '=', $_table->STATUS_HARIAN_ATAS)
                        ->orWhere('status', '=', $_table->STATUS_HARIAN_BAWAH)
                        ->get();
                    if (count($_empls) > 0) {
                        foreach ($_empls as $empls => $empl) {
                            $empl_id = $empl->id;
                            if ($empl->first_name === $empl->last_name){
                                $empl_name = $empl->first_name;
                            } else {
                                $empl_name = $empl->first_name . ' ' . $empl->last_name;
                            }
                            $empl_gender = $empl->gender;
                            $empl_premi = $_global_class->removeMoneySeparator($empl->premi);
                            $empl_stat = $empl->status;
                            $empl_start_work_date = $empl->start_date;
                            $empl_pot_bpjs = $_global_class->removeMoneySeparator($empl->potongan_bpjs);

                            $_temp_atts = [];
                            $_table = new Attendance();
                            $_atts_temps = DB::table($_table->BASETABLE)
                                ->where(\DB::raw('SUBSTR(`date`,4,2)'), '=', $_start_date[1])
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

                                        array_push($_temp_atts, $temp1);
                                    }
                                }
                                $_atts = $_global_class->_array_sort($_temp_atts, "date");
                            }

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
                                    if ($att_stat === (string)$_table->STATUS_SAKIT){
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
                                ->orderBy('id', 'DESC')
                                ->first();
                            if (!empty($_haids)) {
                                $_h_date = $_haids->date;
                                $_temp_haid_date = $_h_date;
                                $_haid_date = date('Y-m-d', strtotime($_h_date));

                                $_haid_start = date('Y-m-d', strtotime($start_date));
                                $_haid_end = date('Y-m-d', strtotime($end_date));

                                if (($_haid_date >= $_haid_start) && ($_haid_date <= $_haid_end)) {
                                    $_haid += $_std_haid;
                                }
                            }
                            $ctr_haid = 0;

                            if ($_potongan_bpjs){
                                $_pot_bpjs += $empl_pot_bpjs;
                            }

                            $_tot = ($_pokok + $_premi + $_haid) - $_pot_bpjs;

                            $temp = array(
                                "hd" => $_temp_haid_date,
                                "id_employee" => $empl_id,
                                "employee_name" => $empl_name,
                                "gender" => $empl_gender,
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
                                "_attendance" => $_atts
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

            if (strtolower($table) === "administrator_push_token") {
                $_table = new Administrator();
                $fields = [
                    "push_token"
                ];
            }

            if (strtolower($table) === "employee") {
                $_table = new Employee();
                $fields = [
                    "id_company", "first_name", "last_name", "email", "phone_1", "phone_2", "domicile_address", "premi", "potongan_bpjs", "tunjangan", "dob", "start_date"
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
                } else if(strtolower($status) === "bulanan"){
                    $status = $_table->STATUS_BULANAN;
                } else if(strtolower($status) === "satpam"){
                    $status = $_table->STATUS_SATPAM;
                } else if(strtolower($status) === "supir"){
                    $status = $_table->STATUS_SUPIR;
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
                    "carton"
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
                } elseif(strtolower($status) === "sakit"){
                    $status = $_table->STATUS_SAKIT;
                }

                $data += ["status" => $status];

            }

            if (strtolower($table) === "attendance_borongan") {
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
                } elseif(strtolower($status) === "sakit"){
                    $status = $_table->STATUS_SAKIT;
                }

                $data += ["status" => $status];

            }

            if (strtolower($table) === "attendance_borongan_carton") {
                $_table = new Attendance();
                $fields = [
                    "carton"
                ];

            }

            if (strtolower($table) === "carton_by_carton_id") {
                $_table = new Carton();
                $fields = [
                    "carton"
                ];

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

    public function delete_data(){
        date_default_timezone_set("Asia/Jakarta");
        $_global_class = new GlobalClass();
        $_table = null;

        $postdata = file_get_contents("php://input");
        if (isset($postdata)) {
            $request = json_decode($postdata);
            $table = $request->table;
            $id = $request->id;

            if (strtolower($table) === "distributor") {
                $_table = new Distributor();
            }

            if (strtolower($table) === "contact") {
                $_table = new Contact();
            }

            if (strtolower($table) === "product") {
                $_table = new Product();
            }

            if (strtolower($table) === "supplier") {
                $_table = new Supplier();
            }

            if (strtolower($table) === "holiday") {
                $_table = new Holiday();
            }

            $data = ["is_active" => $_table->STATUS_INACTIVE];
            $_data = DB::table($_table->BASETABLE)
                ->where('id', '=', $id)
                ->update($data);

            $feedback = [
                "message" => $table . ' Delete Successfully.',
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

            if (strtolower($table) === "libur_satpam") {
                $_table = new LiburSatpam();
            }

            if (strtolower($table) === "carton") {
                $_table = new Carton();
            }

            if (strtolower($table) === "holiday") {
                $_table = new Holiday();
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

    public function print_data(){
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
                $_days = $request->days;

                $_total_pokok = 0;
                $_total_premi = 0;
                $_total_haid = 0;
                $_total_bpjs = 0;
                $_total_final = 0;

                $fpdf = new Fpdf('L','mm',array(210,330));
                $fpdf->AddPage();
                $fpdf->SetFont('Arial', 'B', 12);
                $fpdf->Cell(0, 0, 'Gaji Harian Rungkut');
                $fpdf->Ln(2);
                $fpdf->SetFont('Arial', '', 10);
                $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date);
                $fpdf->Ln(10);

                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(50,7,'Name',1);
                $days = explode("#", $_days);
                for($i=0; $i<count($days)-1;$i++) {
                    $fpdf->Cell(10,7, explode("-",$days[$i])[0],1, 0, 'C');
                }
//                $fpdf->Cell(25,7,'M | S | I | T',1, 0, 'C');
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
                    $tot_days = count($days)-1;
                    if ($tot_days === 6){
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[5],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[6],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[7],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[8],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[9],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[10],1, 0, 'C');
                    } else if ($tot_days === 5){
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[5],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[6],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[7],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[8],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[9],1, 0, 'C');
                    } else if ($tot_days === 4){
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[5],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[6],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[7],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[8],1, 0, 'C');
                    } else if ($tot_days === 3){
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[5],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[6],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[7],1, 0, 'C');
                    } else if ($tot_days === 2){
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[5],1, 0, 'C');
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[6],1, 0, 'C');
                    } else if ($tot_days === 1){
                        $fpdf->Cell(10,5, explode("#", $datas[$i])[5],1, 0, 'C');
                    }
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[$tot_days + 5],1, 0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[$tot_days + 6],1,0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[$tot_days + 7],1, 0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[$tot_days + 8],1, 0, 'R');
                    $fpdf->Cell(30,5, explode("#", $datas[$i])[$tot_days + 11],1,0, 'R');
                    $fpdf->Cell(50,5, explode("#", $datas[$i])[1],1);
                    $fpdf->Ln();

                    $_total_pokok += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 5]);
                    $_total_premi += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 6]);
                    $_total_haid += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 7]);
                    $_total_bpjs += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 8]);
                    $_total_final += $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 11]);
                }

                $fpdf->SetFont('Arial', 'B', 10);

                $tot_space = ($tot_days * 10) + 50;
                $fpdf->Cell($tot_space,5, "Total",1);
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_pokok, 0),1, 0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_premi, 0),1,0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_haid, 0),1, 0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_bpjs, 0),1, 0, 'R');
                $fpdf->Cell(30,5, $_global_class->addMoneySeparator($_total_final, 0),1,0, 'R');
                $fpdf->Cell(50,5, "",1);
                $fpdf->Ln();

                $_total_pokok_premi = $_total_pokok + $_total_premi;
                $fpdf->Cell($tot_space,5, "",1);
                $fpdf->Cell(60,5, $_global_class->addMoneySeparator($_total_pokok_premi, 0),1, 0, 'C');
                $fpdf->Cell(140,5, "",1);
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

            if (strtolower($table) === "gaji_harian_tt") {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $potongan_bpjs = $request->potongan_bpjs;
                $libur = null;
                $rajang = null;
                $_datas = $request->datas;
                $_days = $request->days;
                $final_pokok = 0;
                $final_premi = 0;
                $final_total = 0;

                $temp_arr_days = array();
                // set date for float right or left
                $date_limiter = 20;

                $_total_pokok = 0;
                $_total_premi = 0;
                $_total_haid = 0;
                $_total_bpjs = 0;
                $_total_final = 0;

                $fpdf = new Fpdf('L','mm',array(210,330));
                $fpdf->SetMargins(4,5,4);
                $fpdf->AddPage();
                $fpdf->SetFont('Arial', 'B', 12);
                $fpdf->Cell(0, 0, 'Gaji Harian Rungkut');
                $fpdf->Ln(2);
                $fpdf->SetFont('Arial', '', 10);
                $datetime1 = new \DateTime(explode("-", $start_date)[2] . '-' . explode("-", $start_date)[1] . '-' . explode("-", $start_date)[0]);
                $datetime2 = new \DateTime(explode("-", $end_date)[2] . '-' . explode("-", $end_date)[1] . '-' . explode("-", $end_date)[0]);
//                $__days = strtotime($end_date) - strtotime($start_date);
//                $interval = $datetime1->diff($datetime2);
                $diff = $datetime1->diff($datetime2);
//                $__days = round($__days / (60 * 60 * 24)) + 1;
                $__days= intval($diff->format("%d")) + 1;
                $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date);
//                if ($__days === 4) {
//                } else if ($__days === "4") {
//                    $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date . ' || ' . $__days . ' string');
//                } else {
//                    $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date . ' || ' . $__days . ' else');
//                }
                $fpdf->Ln(10);

                $days = explode("#", $_days);
                $tot_days = count($days)-1;

                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(8,16,'NO.',1,0,'C');
                $fpdf->Cell(40,16,'NAMA',1,0,'C');
                $fpdf->Cell(8,16,'L',1,0,'C');
                $fpdf->Cell(8,16,'P',1,0,'C');
                $fpdf->SetFont('Arial', 'B', 7);
//                if ($tot_days >= 5){
//                    $fpdf->Cell($tot_days*8,8,'Tgl. Pendapatan hari masuk kerja',1,0,'C');
//                } else {
                    $fpdf->Cell(6*8,8,'Tgl. Pendapatan hari masuk kerja',1,0,'C');
//                }
                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(20,16,'Jumlah',1,0,'C');
                $fpdf->Cell(75,8,'PENERIMAAN',1,0,'C');
                $fpdf->Cell(30,16,'JUMLAH','LTR',0,'C');
                $fpdf->SetFont('Arial', 'B', 7);
                $fpdf->Cell(15,16,'POTONGAN',1,0,'C');
                $fpdf->SetFont('Arial', 'B', 8);
                $fpdf->Cell(20,8,'JUMLAH ','LTR',0,'C');
                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(10,16,'NO.',1,0,'C');
                $fpdf->Cell(40,16,'TANDA TANGAN',1,0,'C');
                $fpdf->Ln();

                $fpdf->Cell(8+40+8+8,5,' ',0,0,'C');
//                $days = explode("#", $_days);

                /**
                 * SET DATE ON HEADER
                 */
                if ($__days === 6){
                    if (explode("-",$days[0])[0] > $date_limiter){
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 1);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 2);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 3);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 4);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 5);
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+3,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+4,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+5,1, 0, 'C');
                    } else {
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');
                        array_push($temp_arr_days, explode("-",$start_date)[0]);

                        $res = explode("-",$start_date)[0] +1;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +2;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +3;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +4;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +5;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }
                    }
                }

                if ($__days === 5){
                    if (explode("-",$days[0])[0] > $date_limiter){
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 1);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 2);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 3);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 4);
                        array_push($temp_arr_days, 'a');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+3,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+4,1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                    } else {
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                        $res = explode("-",$start_date)[0] +1;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +2;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +3;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +4;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }
                    }
                }

                if ($__days === 4){
                    if (explode("-",$days[0])[0] > $date_limiter){
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 1);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 2);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 3);
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+3,1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                    } else {
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                        $res = explode("-",$start_date)[0] +1;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +2;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +3;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }
                    }
                }

                if ($__days === 3){
                    if (explode("-",$days[0])[0] > $date_limiter){
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 1);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 2);
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, 'c');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                    } else {
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, 'c');
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                        $res = explode("-",$start_date)[0] +1;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }

                        $res = explode("-",$start_date)[0] +2;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }
                    }
                }

                if ($__days === 2){
                    if (explode("-",$days[0])[0] > $date_limiter){
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        array_push($temp_arr_days, explode("-",$start_date)[0] + 1);
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, 'c');
                        array_push($temp_arr_days, 'd');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                    } else {
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, 'c');
                        array_push($temp_arr_days, 'd');
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                        $res = explode("-",$start_date)[0] +1;
                        if ($res < 10) {
                            $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                            array_push($temp_arr_days, "0" . $res);
                        } else {
                            $fpdf->Cell(8,-8, $res,1, 0, 'C');
                            array_push($temp_arr_days, $res);
                        }
                    }
                }

                if ($__days === 1){
                    if (explode("-",$days[0])[0] > $date_limiter){
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, 'c');
                        array_push($temp_arr_days, 'd');
                        array_push($temp_arr_days, 'e');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                    } else {
                        array_push($temp_arr_days, 'a');
                        array_push($temp_arr_days, 'b');
                        array_push($temp_arr_days, 'c');
                        array_push($temp_arr_days, 'd');
                        array_push($temp_arr_days, 'e');
                        array_push($temp_arr_days, explode("-",$start_date)[0]);
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                        $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');
                    }
                }

//                for($i=0; $i<count($days)-1;$i++) {
//                    if ($tot_days >= 5) {
//                        $fpdf->Cell(8,-8, explode("-",$days[$i])[0],1, 0, 'C');
//                    } else {
//                        $fpdf->Cell(((5*8) / $tot_days),-8, explode("-",$days[$i])[0],1, 0, 'C');
//                    }
//                    if ($tot_days === 3) {
//                        if (explode("-", $days[$i])[0] > 4){
//                            $fpdf->Cell(8,-8, ' ',1, 0, 'C');
//                            $fpdf->Cell(8,-8, ' ',1, 0, 'C');
//                            $fpdf->Cell(8,-8, ' ',1, 0, 'C');
//                            $fpdf->Cell(8,-8, explode("-",$days[$i])[0],1, 0, 'C');
//                            $fpdf->Cell(8,-8, explode("-",$days[$i])[0],1, 0, 'C');
//                            $fpdf->Cell(8,-8, explode("-",$days[$i])[0],1, 0, 'C');
//                        }
//                    }

//                }
                $fpdf->SetFont('Arial', 'B', 8);
                $fpdf->Cell(20,-8,' ',0,0,'C');
                $fpdf->Cell(30,-8,'Upah Pokok',1,0,'C');
                $fpdf->Cell(15,-8,'Tunjangan',1,0,'C');
                $fpdf->Cell(30,-8,'Premi',1,0,'C');
                $fpdf->Cell(30,-8,'PENERIMAAN','T',0,'C');
                $fpdf->Cell(15,-8,' ',0,0,'C');
                $fpdf->Cell(20,-10,'BERSIH','T',0,'C');
                $fpdf->Ln();
//                if ($tot_days >= 5) {
//                    $fpdf->Cell(8+40+8+8+((count($days)-1) * 8)+20+30+15+30+30+15,8,' ',0,0,'C');
//                } else {
//                }
                $fpdf->Cell(8+40+8+8+(6 * 8)+20+30+15+30+30+15,8,' ',0,0,'C');
                $fpdf->Cell(20,4,'PENDAPATAN',0,0,'C');
                $fpdf->Ln(10);

                $fpdf->SetFont('Arial', '', 10);

                $counter = 1;
                $datas = explode("@", $_datas);
                for($i=0; $i<count($datas)-1;$i++) {

                    if ($counter === 16) {
                        $fpdf->SetFont('Arial', 'B', 10);
                        $fpdf->Cell(8, 16, 'NO.', 1, 0, 'C');
                        $fpdf->Cell(40, 16, 'NAMA', 1, 0, 'C');
                        $fpdf->Cell(8, 16, 'L', 1, 0, 'C');
                        $fpdf->Cell(8, 16, 'P', 1, 0, 'C');
                        $fpdf->SetFont('Arial', 'B', 7);
//                        if ($tot_days >= 5) {
//                            $fpdf->Cell($tot_days * 8, 8, 'Tgl. Pendapatan hari masuk kerja', 1, 0, 'C');
//                        } else {
                            $fpdf->Cell(6 * 8, 8, 'Tgl. Pendapatan hari masuk kerja', 1, 0, 'C');
//                        }
                        $fpdf->SetFont('Arial', 'B', 10);
                        $fpdf->Cell(20, 16, 'Jumlah', 1, 0, 'C');
                        $fpdf->Cell(75, 8, 'PENERIMAAN', 1, 0, 'C');
                        $fpdf->Cell(30, 16, 'JUMLAH', 'LTR', 0, 'C');
                        $fpdf->SetFont('Arial', 'B', 7);
                        $fpdf->Cell(15, 16, 'POTONGAN', 1, 0, 'C');
                        $fpdf->SetFont('Arial', 'B', 8);
                        $fpdf->Cell(20, 8, 'JUMLAH ', 'LTR', 0, 'C');
                        $fpdf->SetFont('Arial', 'B', 10);
                        $fpdf->Cell(10, 16, 'NO.', 1, 0, 'C');
                        $fpdf->Cell(40, 16, 'TANDA TANGAN', 1, 0, 'C');
                        $fpdf->Ln();

                        $fpdf->Cell(8 + 40 + 8 + 8, 5, ' ', 0, 0, 'C');
                        $days = explode("#", $_days);
//                        for ($j = 0; $j < count($days) - 1; $j++) {
//                            if ($tot_days >= 5) {
//                                $fpdf->Cell(8, -8, explode("-", $days[$j])[0], 1, 0, 'C');
//                            } else {
////                                $fpdf->Cell(((5 * 8) / $tot_days), -8, explode("-", $days[$j])[0], 1, 0, 'C');
//                                $fpdf->Cell(((5 * 8) / $tot_days), -8, 'T', 1, 0, 'C');
//                            }
//                        }

                        /**
                         * SET DATE ON HEADER
                         */
                        if ($__days === 6){
                            if (explode("-",$days[0])[0] > 20){
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+3,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+4,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+5,1, 0, 'C');
                            } else {
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                                if (explode("-",$start_date)[0] +1 < 10) {
                                    $res = explode("-",$start_date)[0] +1;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +1,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +2 < 10) {
                                    $res = explode("-",$start_date)[0] +2;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +2,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +3 < 10) {
                                    $res = explode("-",$start_date)[0] +3;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +3,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +4 < 10) {
                                    $res = explode("-",$start_date)[0] +4;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +4,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +5 < 10) {
                                    $res = explode("-",$start_date)[0] +5;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +5,1, 0, 'C');
                                }
                            }
                        }

                        if ($__days === 5){
                            if (explode("-",$days[0])[0] > 20){
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+3,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+4,1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                            } else {
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                                if (explode("-",$start_date)[0] +1 < 10) {
                                    $res = explode("-",$start_date)[0] +1;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +1,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +2 < 10) {
                                    $res = explode("-",$start_date)[0] +2;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +2,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +3 < 10) {
                                    $res = explode("-",$start_date)[0] +3;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +3,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +4 < 10) {
                                    $res = explode("-",$start_date)[0] +4;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +4,1, 0, 'C');
                                }
                            }
                        }

                        if ($__days === 4){
                            if (explode("-",$days[0])[0] > 20){
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+3,1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                            } else {
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                                if (explode("-",$start_date)[0] +1 < 10) {
                                    $res = explode("-",$start_date)[0] +1;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +1,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +2 < 10) {
                                    $res = explode("-",$start_date)[0] +2;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +2,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +3 < 10) {
                                    $res = explode("-",$start_date)[0] +3;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +3,1, 0, 'C');
                                }
                            }
                        }

                        if ($__days === 3){
                            if (explode("-",$days[0])[0] > 20){
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+2,1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                            } else {
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                                if (explode("-",$start_date)[0] +1 < 10) {
                                    $res = explode("-",$start_date)[0] +1;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +1,1, 0, 'C');
                                }

                                if (explode("-",$start_date)[0] +2 < 10) {
                                    $res = explode("-",$start_date)[0] +2;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +2,1, 0, 'C');
                                }
                            }
                        }

                        if ($__days === 2){
                            if (explode("-",$days[0])[0] > 20){
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0]+1,1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                            } else {
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');

                                if (explode("-",$start_date)[0] +1 < 10) {
                                    $res = explode("-",$start_date)[0] +1;
                                    $fpdf->Cell(8,-8, "0" . $res ,1, 0, 'C');
                                } else {
                                    $fpdf->Cell(8,-8, explode("-",$start_date)[0] +1,1, 0, 'C');
                                }
                            }
                        }

                        if ($__days === 1){
                            if (explode("-",$days[0])[0] > 20){
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0],1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                            } else {
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, ' ',1, 0, 'C');
                                $fpdf->Cell(8,-8, explode("-",$start_date)[0], 1, 0, 'C');
                            }
                        }

                        $fpdf->SetFont('Arial', 'B', 8);
                        $fpdf->Cell(20, -8, ' ', 0, 0, 'C');
                        $fpdf->Cell(30, -8, 'Upah Pokok', 1, 0, 'C');
                        $fpdf->Cell(15, -8, 'Tunjangan', 1, 0, 'C');
                        $fpdf->Cell(30, -8, 'Premi', 1, 0, 'C');
                        $fpdf->Cell(30, -8, 'PENERIMAAN', 'T', 0, 'C');
                        $fpdf->Cell(15, -8, ' ', 0, 0, 'C');
                        $fpdf->Cell(20, -10, 'BERSIH', 'T', 0, 'C');
                        $fpdf->Ln();
//                        if ($tot_days >= 5) {
//                            $fpdf->Cell(8 + 40 + 8 + 8 + ((count($days) - 1) * 8) + 20 + 30 + 15 + 30 + 30 + 15, 8, ' ', 0, 0, 'C');
//                        } else {
//                        }
                        $fpdf->Cell(8 + 40 + 8 + 8 + (6 * 8) + 20 + 30 + 15 + 30 + 30 + 15, 8, ' ', 0, 0, 'C');
                        $fpdf->Cell(20, 4, 'PENDAPATAN', 0, 0, 'C');
                        $fpdf->Ln(10);

                        $counter = 16;

                    }
                    $fpdf->SetFont('Arial', '', 10);
                    $fpdf->Cell(8, 10, $counter, 1);
                    $fpdf->Cell(40, 10, explode("#", $datas[$i])[1], 1);
                    if (explode("#", $datas[$i])[2] === 'L'){
                        $fpdf->Cell(8, 10, explode("#", $datas[$i])[2], 1,0,'C');
                        $fpdf->Cell(8, 10, ' ', 1);
                    } else {
                        $fpdf->Cell(8, 10, ' ', 1);
                        $fpdf->Cell(8, 10, explode("#", $datas[$i])[2], 1,0,'C');
                    }

                    $total_days = 0;
                    $tot_days = count($days)-1;
                    /**
                     * START LIST ABSENSi
                     */
                    if ($tot_days === 6){
//                        if($temp_arr_days[0] === )
                        $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                        $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                        $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                        $fpdf->Cell(8,10, explode("#", $datas[$i])[8],1, 0, 'C');
                        $fpdf->Cell(8,10, explode("#", $datas[$i])[9],1, 0, 'C');
                        $fpdf->Cell(8,10, explode("#", $datas[$i])[10],1, 0, 'C');
                        if (explode("#", $datas[$i])[5] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[5] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[6] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[6] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[7] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[7] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[8] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[8] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[8] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[8] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[9] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[9] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[9] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[9] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[10] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[10] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[10] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[10] === 's'){
                            $total_days += 1;
                        }
                    }

                    if ($tot_days === 5){
                        if (explode("-",$days[0])[0] > 20) {
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[8],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[9],1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                        } else {
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[8],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[9],1, 0, 'C');
                        }

                        if (explode("#", $datas[$i])[5] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[5] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[6] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[6] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[7] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[7] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[8] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[8] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[8] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[8] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[9] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[9] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[9] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[9] === 's'){
                            $total_days += 1;
                        }
                    }

                    if ($tot_days === 4){
                        if (explode("-",$days[0])[0] > 20) {
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[8],1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                        } else {
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[8],1, 0, 'C');
                        }

                        if (explode("#", $datas[$i])[5] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[5] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[6] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[6] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[7] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[7] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[8] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[8] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[8] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[8] === 's'){
                            $total_days += 1;
                        }
                    }

                    if ($tot_days === 3){
                        if (explode("-",$days[0])[0] > 20) {
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                        } else {
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[7],1, 0, 'C');
                        }

                        if (explode("#", $datas[$i])[5] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[5] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[6] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[6] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[7] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[7] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[7] === 's'){
                            $total_days += 1;
                        }
                    }

                    if ($tot_days === 2){
                        if (explode("-",$days[0])[0] > 20) {
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                        } else {
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[6],1, 0, 'C');
                        }

                        if (explode("#", $datas[$i])[5] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[5] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === 's'){
                            $total_days += 1;
                        }

                        if (explode("#", $datas[$i])[6] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[6] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[6] === 's'){
                            $total_days += 1;
                        }
                    }

                    if ($tot_days === 1){
                        if (explode("-",$days[0])[0] > 20) {
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                        } else {
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, ' ',1, 0, 'C');
                            $fpdf->Cell(8,10, explode("#", $datas[$i])[5],1, 0, 'C');
                        }

                        if (explode("#", $datas[$i])[5] === '1') {
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === '0.5'){
                            $total_days += 0.5;
                        } elseif (explode("#", $datas[$i])[5] === 'i'){
                            $total_days += 1;
                        } elseif (explode("#", $datas[$i])[5] === 's'){
                            $total_days += 1;
                        }
                    }

                    $fpdf->Cell(20, 10, $total_days, 1, 0, 'C');
                    $fpdf->Cell(30,10, explode("#", $datas[$i])[$tot_days + 5],1, 0, 'R');
                    $fpdf->Cell(15, 10, ' ', 1, 0, 'C');
                    $fpdf->Cell(30, 10, explode("#", $datas[$i])[$tot_days + 6], 1, 0, 'R');

                    $_total_pokok = $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 5]);
                    $_total_premi = $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 6]);
                    $_total_revisi = $_global_class->removeMoneySeparator(explode("#", $datas[$i])[$tot_days + 10]);
                    $_total_terima =  $_total_pokok + $_total_premi;
                    $final_pokok += $_total_pokok;
                    $final_premi += $_total_premi;
                    $final_total += $_total_terima;

                    $fpdf->Cell(30,10, $_global_class->addMoneySeparator($_total_terima, 0),1, 0, 'R');
                    $fpdf->Cell(15, 10, $_global_class->addMoneySeparator($_total_revisi, 0), 1, 0, 'C');
                    $fpdf->Cell(20, 10, ' ', '1', 0, 'C');
                    $fpdf->Cell(10, 10, $counter, 1, 0, 'C');
                    $fpdf->Cell(40, 10, ' ', 1, 0, 'C');

                    $fpdf->Ln();
                    $counter++;
                }
                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(8, 10, "", 1);
                $fpdf->Cell(40, 10, "TOTAL", 1, 0, 'R');
                $fpdf->Cell(8, 10, "", 1,0,'C');
                $fpdf->Cell(8, 10, ' ', 1);
                $fpdf->Cell(8,10, "",1, 0, 'C');
                $fpdf->Cell(8,10, "",1, 0, 'C');
                $fpdf->Cell(8,10, "",1, 0, 'C');
                $fpdf->Cell(8,10, "",1, 0, 'C');
                $fpdf->Cell(8,10, "",1, 0, 'C');
                $fpdf->Cell(8,10, "",1, 0, 'C');
                $fpdf->Cell(20, 10, '', 1, 0, 'C');
                $fpdf->Cell(30,10, $_global_class->addMoneySeparator($final_pokok, 0),1, 0, 'R');
                $fpdf->Cell(15, 10, ' ', 1, 0, 'C');
                $fpdf->Cell(30, 10, $_global_class->addMoneySeparator($final_premi, 0), 1, 0, 'R');
                $fpdf->Cell(30,10, $_global_class->addMoneySeparator($final_total, 0),1, 0, 'R');
                $fpdf->Cell(15, 10, ' ', 1, 0, 'C');
                $fpdf->Cell(20, 10, ' ', '1', 0, 'C');
                $fpdf->Cell(10, 10, "", 1, 0, 'C');
                $fpdf->Cell(40, 10, ' ', 1, 0, 'C');

                $target_path = base_path('public/pdf/');
//                $file_name = $_date_now . '_gaji_harian_tt.pdf';
//                $fname = date("YmdHis") . '_'. rand(1, 9999);
                $fname = date("YmdHis");
                $file_name = $fname . '_gaji_harian_tt.pdf';
                $file_path = $target_path . $file_name;
                $fpdf->Output($file_path, 'F');

                $feedback = [
                    "message" => $file_name,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            }

            if (strtolower($table) === "libur_satpam") {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $_start_date = explode("-", $start_date);
                $_end_date = explode("-", $end_date);
                $res = array();
                $standard = 0;
                $final = 0;
                $days = 0;

                $_table3 = new Standard();
                $_standards = DB::table($_table3->BASETABLE)
                    ->where('name', '=', 'libur_jaga_satpam')
                    ->where('year', '=', date('Y'))
                    ->where('is_active', '=', $_table3->STATUS_ACTIVE)
                    ->first();
                if (!empty($_standards)){
                    $standard = $_global_class->removeMoneySeparator($_standards->nominal);
                }

                $_table = new Employee();
                $_employees = DB::table($_table->BASETABLE)
                    ->where('status', '=', $_table->STATUS_SATPAM)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->get();
                if (count($_employees) > 0) {
                    foreach ($_employees as $_employee => $employee) {
                        $employee_id = $employee->id;

                        $_table2 = new LiburSatpam();
                        $_libur_satpams = DB::table($_table2->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('id_employee', '=', $employee_id)
                            ->where('is_active', '=', $_table2->STATUS_ACTIVE)
                            ->orderBy('date', 'asc')
                            ->get();

                        $temp2 = array();
                        if (count($_libur_satpams) > 0) {
                            foreach ($_libur_satpams as $_libur_satpam => $libur_satpam) {
                                $_libur_satpam_date = $libur_satpam->date;
                                if (!in_array($_libur_satpam_date,$temp2)){
                                    array_push($temp2, $_libur_satpam_date);
                                }
                            }
                        }

                        $temp = array(
                            "employee_id" => $employee_id,
                            "employee_fname" => $employee->first_name,
                            "employee_lname" => $employee->last_name,
                            "dates" => $temp2
                        );

                        array_push($res,$temp);
                    }
                }

                $fpdf = new Fpdf('P','mm',array(210,330));
                $fpdf->AddPage();
                $fpdf->SetFont('Arial', 'B', 12);
                $fpdf->Cell(0, 0, 'Upah Libur Satpam Rungkut');
                $fpdf->Ln(2);
                $fpdf->SetFont('Arial', '', 10);
                $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date);
                $fpdf->Ln(10);

                for($i=0; $i<count($res);$i++) {
                    if (count($res[$i]['dates']) > 0){
                        $fpdf->SetFont('Arial', 'B', 10);
                        $employee_name = "";
                        if ($res[$i]['employee_fname'] === $res[$i]['employee_lname']){
                            $employee_name = $res[$i]['employee_fname'];
                        } else {
                            $employee_name = $res[$i]['employee_fname'] . ' ' . $res[$i]['employee_lname'];
                        }
                        $fpdf->Cell(80,7, $employee_name,1, 0, 'L');
                        $fpdf->Cell(30,7, count($res[$i]['dates']) . ' X ' . $_global_class->addMoneySeparator($standard, 0),1, 0, 'R');
                        $total = count($res[$i]['dates']) * $standard;
                        $fpdf->Cell(30,7, $_global_class->addMoneySeparator($total, 0),1, 0, 'R');
                        $fpdf->Cell(50,14, '',1, 0, 'R');
                        $fpdf->Ln(7);
                        $fpdf->SetFont('Arial', '', 10);
                        $_date = "";
                        for($j=0; $j<count($res[$i]['dates']);$j++) {
                            $_date .= $res[$i]['dates'][$j] . ', ';
                        }
                        $fpdf->Cell(140,7, $_date,1, 0, 'L');
                        $fpdf->Ln(10);
                        $days += count($res[$i]['dates']);
                        $final += $total;
                    }
                }

                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(80,7, 'Total ',1, 0, 'R');
                $fpdf->Cell(30,7, $days . ' X ' . $_global_class->addMoneySeparator($standard, 0),1, 0, 'R');
                $fpdf->Cell(30,7, $_global_class->addMoneySeparator($final, 0),1, 0, 'R');

                $target_path = base_path('public/pdf/');
                $fname = date("YmdHis");
                $file_name = $fname . '_upah_libur_satpam_tt.pdf';
                $file_path = $target_path . $file_name;
                $fpdf->Output($file_path, 'F');

                $feedback = [
                    "message" => $file_name,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            }

            if (strtolower($table) === "borongan_mingguan") {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $_start_date = explode("-", $start_date);
                $_end_date = explode("-", $end_date);
                $_conv_start_date = date('Y-m-d', strtotime($start_date));
                $_conv_end_date = date('Y-m-d', strtotime($end_date));
                $res = array();
                $standard = 0;
                $final = 0;
                $days = 0;
                $carton_column = 0;

                $_table3 = new Standard();
                $_standards = DB::table($_table3->BASETABLE)
                    ->where('name', '=', 'upah_borongan')
                    ->where('year', '=', date('Y'))
                    ->where('is_active', '=', $_table3->STATUS_ACTIVE)
                    ->first();
                if (!empty($_standards)) {
                    $standard = $_global_class->removeMoneySeparator($_standards->nominal);
                }

                $_table = new GroupHeader();
                $_groups = DB::table($_table->BASETABLE)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->get();
                if (count($_groups) > 0) {
                    foreach ($_groups as $_group => $group) {
                        $group_id = $group->id;
                        $temp2 = array();

                        $_table2 = new Carton();
                        $_cartons = DB::table($_table2->BASETABLE)
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '>=', $_start_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,4,2)'), '<=', $_end_date[1])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_start_date[2])
                            ->where(\DB::raw('SUBSTR(`date`,7,4)'), '=', $_end_date[2])
                            ->where('id_group', '=', $group_id)
                            ->where('is_active', '=', $_table2->STATUS_ACTIVE)
                            ->orderBy('id', 'asc')
                            ->get();
                        if (count($_cartons) > 0) {
                            foreach ($_cartons as $_carton => $carton) {
                                $_conv_date = date('Y-m-d', strtotime($carton->date));
                                if (($_conv_date >= $_conv_start_date) && ($_conv_date <= $_conv_end_date)) {
                                    $temp = array(
                                        "carton_id" => $carton->id,
                                        "carton_date" => $carton->date,
                                        "carton_carton" => $carton->carton
                                    );
                                    array_push($temp2, $temp);
                                }
                            }
                        }

                        $temp3 = array(
                            "group_id" => $group_id,
                            "group_name" => $group->name,
                            "carton_datas" => $temp2
                        );
                        array_push($res, $temp3);
                    }
                }

                $fpdf = new Fpdf('P','mm',array(210,330));
                $fpdf->AddPage();
                $fpdf->SetFont('Arial', 'B', 12);
                $fpdf->Cell(0, 0, 'Upah Borongan Rungkut');
                $fpdf->Ln(2);
                $fpdf->SetFont('Arial', '', 10);
                $fpdf->Cell(0, 10, 'Periode : ' . $start_date . ' s/d ' . $end_date);
                $fpdf->Ln(10);

                $total_date_column_length = 48;
                $date_column_length = $total_date_column_length / count($res[0]['carton_datas']);

                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(40,7, 'Group',1, 0, 'L');

                for ($j=0; $j<count($res[0]['carton_datas']); $j++){
                    $fpdf->Cell($date_column_length,7, explode('-', $res[0]['carton_datas'][$j]['carton_date'])[0],1, 0, 'C');
                }
                $fpdf->Cell(20,7, 'Carton',1, 0, 'C');
                $fpdf->Cell(30,7, 'Upah',1, 0, 'C');
                $fpdf->Cell(40,7, 'Total',1, 0, 'C');
                $fpdf->Ln(7);

                $fpdf->SetFont('Arial', '', 10);
                if (count($res) > 0) {
                    for($i=0; $i<count($res);$i++) {
                        $carton_row = 0;
                        $fpdf->Cell(40,7, $res[$i]['group_name'],1, 0, 'L');
                        for ($j=0; $j<count($res[$i]['carton_datas']); $j++){
                            $carton = $res[$i]['carton_datas'][$j]['carton_carton'];
                            $fpdf->Cell($date_column_length,7, $carton,1, 0, 'C');
                            $carton_row += $carton;
                        }
                        $fpdf->Cell(20,7, $carton_row,1, 0, 'C');
                        $fpdf->Cell(30,7, $_global_class->addMoneySeparator($standard, 0),1, 0, 'R');
                        $total_row = $carton_row * $standard;
                        $fpdf->Cell(40,7, $_global_class->addMoneySeparator($total_row, 0),1, 0, 'R');
                        $fpdf->Ln(7);
                        $carton_column += $carton_row;
                    }
                }

                $fpdf->SetFont('Arial', 'B', 10);
                $fpdf->Cell(40 + $total_date_column_length,7, 'Total',1, 0, 'R');
                $fpdf->Cell(20,7, $carton_column,1, 0, 'C');
                $fpdf->Cell(30,7, $_global_class->addMoneySeparator($standard, 0),1, 0, 'R');
                $total_column = $carton_column * $standard;
                $fpdf->Cell(40,7, $_global_class->addMoneySeparator($total_column, 0),1, 0, 'R');
                $fpdf->Ln(7);

                $target_path = base_path('public/pdf/');
                $fname = date("YmdHis");
                $file_name = $fname . '_upah_borongan_mingguan.pdf';
                $file_path = $target_path . $file_name;
                $fpdf->Output($file_path, 'F');

                $feedback = [
                    "message" => $file_name,
                    "status" => $_global_class->STATUS_SUCCESS,
                ];

                return response()->json($feedback);
            }

            if (strtolower($table) === "gaji_bulanan_tt") {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $_start_date = explode("-", $start_date);
                $_end_date = explode("-", $end_date);

                $fpdf = new Fpdf('P','mm',array(210,330));
                $fpdf->AddPage();

                $_table = new Employee();
                $_employees = DB::table($_table->BASETABLE)
                    ->where('status', '=', $_table->STATUS_BULANAN)
                    ->orWhere('status', '=', $_table->STATUS_SUPIR)
                    ->orWhere('status', '=', $_table->STATUS_SATPAM)
                    ->where('is_active', '=', $_table->STATUS_ACTIVE)
                    ->get();
                if (count($_employees) > 0) {
                    foreach ($_employees as $_employee => $employee) {
                        $fpdf->SetFont('Arial', 'B', 11);
                        $fpdf->Cell(210,7, 'TANDA TERIMA',0, 0, 'C');
                        $fpdf->Ln(7);
                        $fpdf->Cell(210,7, 'GAJI BULAN ' . $_global_class->getMonthText($_start_date[1]) . ' ' . $_start_date[2],0, 0, 'C');
                        $fpdf->Ln(2);
                        $fpdf->SetFont('Arial', '', 10);
                        $fpdf->Ln(10);
                        $fpdf->Cell(40,7, 'Nama',0, 0, 'L');
                        $fpdf->Cell(5,7, ':',0, 0, 'C');
                        $employee_name = "";
                        if ($employee->first_name === $employee->last_name){
                            $employee_name = $employee->first_name;
                        } else {
                            $employee_name = $employee->first_name . ' ' . $employee->last_name;
                        }
                        $fpdf->Cell(150,7, $employee_name,0, 0, 'L');
                        $fpdf->Ln(10);

                        $fpdf->Cell(40,7, 'Gaji Pokok',0, 0, 'L');
                        $fpdf->Cell(5,7, ':',0, 0, 'C');
                        $fpdf->Cell(8,7, 'Rp.',0, 0, 'L');
                        $fpdf->Cell(20,7, $employee->premi,0, 0, 'R');
                        $fpdf->Ln(7);

                        $fpdf->Cell(40,7, 'Tunjangan','B', 0, 'L');
                        $fpdf->Cell(5,7, ':','B', 0, 'C');
                        $fpdf->Cell(8,7, 'Rp.','B', 0, 'L');
                        $fpdf->Cell(20,7, $employee->tunjangan,'B', 0, 'R');
                        $fpdf->Ln(7);

                        $total = $_global_class->removeMoneySeparator($employee->premi) + $_global_class->removeMoneySeparator($employee->tunjangan);
                        $fpdf->SetFont('Arial', 'B', 10);
                        $fpdf->Cell(40,7, 'Total',0, 0, 'L');
                        $fpdf->Cell(5,7, ':',0, 0, 'C');
                        $fpdf->Cell(8,7, 'Rp.',0, 0, 'L');
                        $fpdf->Cell(20,7, $_global_class->addMoneySeparator($total, 0),0, 0, 'R');
                        $fpdf->Ln(7);

                        $fpdf->SetFont('Arial', '', 9);
                        $fpdf->Cell(40,7, '',0, 0, 'L');
                        $fpdf->Cell(5,7, '',0, 0, 'C');
                        $fpdf->Cell(40,7, '( ' . $_global_class->terbilang($total) . ' )',0, 0, 'L');
                        $fpdf->Ln(7);

                        $fpdf->SetFont('Arial', '', 10);
                        $fpdf->Cell(115,7, '',0, 0, 'L');
                        $fpdf->Cell(40,7, 'Tgl.             ' . $_global_class->getMonthText($_start_date[1]) . ' ' . $_start_date[2],0, 0, 'L');
                        $fpdf->Ln(22);

                        $fpdf->Cell(110,7, '',0, 0, 'L');
                        $fpdf->Cell(5,7, '(',0, 0, 'L');
                        $fpdf->Cell(40,7, $employee_name,0, 0, 'C');
                        $fpdf->Cell(5,7, ' )',0, 0, 'L');
                        $fpdf->Ln(10);

                        $fpdf->Cell(90,7, '_',0, 0, 'L');
                        $fpdf->Cell(90,7, '_',0, 0, 'R');

                        $fpdf->Ln(10);
                    }
                }

                $target_path = base_path('public/pdf/');
                $fname = date("YmdHis");
                $file_name = $fname . '_upah_bulanan_tt.pdf';
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

    public function sendPushNotification(){
//        require_once './../../../../vendor/autoload.php';
//        require_once base_path('vendor/autoload.php');
        $interestDetails = ['unique identifier', 'ExponentPushToken[WlrQxYJavPHrNsW4sznFZn]'];

        // You can quickly bootup an expo instance
        $expo = \ExponentPhpSDK\Expo::normalSetup();

        // Subscribe the recipient to the server
        $expo->subscribe($interestDetails[0], $interestDetails[1]);

        // Build the notification data
        $notification = ['title' => 'Title from Server!', 'body' => 'Hello World!'];

        // Notify an interest with a notification
        $expo->notify($interestDetails[0], $notification);
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
            $tunjangan = $request->tunjangan;
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
            } else if(strtolower($status) === "bulanan"){
                $status = $_employee->STATUS_BULANAN;
            } else if(strtolower($status) === "satpam"){
                $status = $_employee->STATUS_SATPAM;
            } else if(strtolower($status) === "supir"){
                $status = $_employee->STATUS_SUPIR;
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
                "tunjangan" => $tunjangan,
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
//        $_global_class = new GlobalClass();
//        $postdata = file_get_contents("php://input");
//        if (isset($postdata)) {
//            $request = json_decode($postdata);
//            $images = $request->image;
//            $new_name = rand() . '.jpg';
//            $images->move(base_path('public/images/employee/') . $new_name);
//            return response()->json([
//                "message" => $new_name,
//                "status" => $_global_class->STATUS_SUCCESS
//            ]);
//        }

        $_global_class = new GlobalClass();
//
        $name = $_global_class->generateID('IMG');
        $img = $name . '.jpg';
        $target_path 	= base_path('public/images/employee/') . $img;
//
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

//        if (isset($_FILES['files'])) {
//            if (move_uploaded_file($_FILES['files']['tmp_name'], $target_path)) {
//                return response()->json([
//                    "message" => $img,
//                    "status" => $_global_class->STATUS_SUCCESS
//                ]);
//            } else {
//                return response()->json([
//                    "message" => 'Something when wrong. Please try again later.',
//                    "status" => $_global_class->STATUS_ERROR
//                ]);
//            }
//        }
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
