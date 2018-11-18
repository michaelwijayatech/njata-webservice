<?php

namespace App\Http\Controllers\API;

use App\Classes\GlobalClass;
use App\Model\Administrator;
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

    public function uploadFile(){
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
}
