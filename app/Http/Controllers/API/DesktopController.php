<?php

namespace App\Http\Controllers\API;

use App\Classes\GlobalClass;
use App\Model\Administrator;
use App\Model\Employee;
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

            if(strtolower($status) === "harian"){
                $status = $_employee->STATUS_HARIAN;
            } else {
                $status = $_employee->STATUS_BORONGAN;
            }

            $data = [
                "id" => $generate_id,
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

        $index = [1,2,3,4,5,6,7,8,9];

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

                if($i===10){
                    if(strtolower($request->$field) === "male"){
                        $gender = $_employee->GENDER_MALE;
                    } else {
                        $gender = $_employee->GENDER_FEMALE;
                    }
                    $data += ["gender" => $gender];
                }

                if($i===11){
                    if(strtolower($request->$field) === "harian"){
                        $status = $_employee->STATUS_HARIAN;
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
