<?php

namespace App\Classes;

use App\Model\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Mail;

class GlobalClass
{

    public $STATUS_ERROR = "404";
    public $STATUS_SUCCESS = "200";
    public $STATUS_EXIST = "201";

    public function generateID($NAME)
    {
        date_default_timezone_set("Asia/Jakarta");
        return $NAME . date("YmdHis");
    }

    public function generatePassword($PASSWORD)
    {
        return Hash::make($PASSWORD);
    }

    public function checkPassword($PASSWORD_INPUT, $PASSWORD_DB)
    {
        return Hash::check($PASSWORD_INPUT, $PASSWORD_DB);
    }

    public function generateVerificationCode($LIMIT)
    {
        $res = "";
        for ($i = 0; $i < $LIMIT; $i++) {
            if($i==0 || $i==($LIMIT-1)){
                $res .= rand(1,9);
            } else {
                $res .= rand(0,9);
            }
        }
        return $res;
    }

    public function getMonth($MONTH)
    {
        switch ($MONTH) {
            case 'Jan':
            case 'January':
                return '01';
            case 'Feb':
            case 'February':
                return '02';
            case 'Mar':
            case 'March':
                return '03';
            case 'Apr':
            case 'April':
                return '04';
            case 'May':
                return '05';
            case 'Jun':
            case 'June':
                return '06';
            case 'Jul':
            case 'July':
                return '07';
            case 'Aug':
            case 'August':
                return '08';
            case 'Sep':
            case 'September':
                return '09';
            case 'Oct':
            case 'October':
                return '10';
            case 'Nov':
            case 'November':
                return '11';
            case 'Dec':
            case 'December':
                return '12';
            default:
                return '01';
        }
    }

    public function setGender($GENDER){
        $users = new Users();
        if(strtolower($GENDER) === "male"){
            return $users->GENDER_MALE;
        } else if(strtolower($GENDER) === "female"){
            return $users->GENDER_FEMALE;
        }
    }

    public function mailConfig($MAIL, $DATA, $TO, $STATUS){
        $config = array(
            'driver' => $MAIL->driver,
            'host' => $MAIL->host,
            'port' => $MAIL->port,
            'from' => array('address' => $MAIL->address, 'name' => $MAIL->name),
            'to' => array('address' => $TO['address'], 'name' => $TO['name']),
            'encryption' => $MAIL->encryption,
            'username'   => $MAIL->username,
            'password'   => $MAIL->password,
            'sendmail'   => $MAIL->sendmail,
            'pretend'    => $MAIL->pretend,
        );
        Config::set('mail', $config);

        $send_mail = "";

        if($STATUS === "VERIFICATION CODE | SIGNUP"){
            $send_mail = Mail::send('Backend.template.email_code', $DATA, function ($message) {
                $message->subject('VERIFICATION CODE | SIGNUP');
            });
        }

        if($STATUS === "VERIFICATION CODE | FORGOT PASSWORD"){
            $send_mail = Mail::send('Backend.template.email_code', $DATA, function ($message) {
                $message->subject('VERIFICATION CODE | FORGOT PASSWORD');
            });
        }

        return $send_mail;
    }

    public function removeMoneySeparator($MONEY){
        return (int)str_replace('.', '', $MONEY);
    }

    public function checkDifferenceBetweenTwoDate($DATE1, $DATE2){
        $_date1 = explode("-", $DATE1)[2] . '-' . explode("-", $DATE1)[1] . '-' . explode("-", $DATE1)[0];
        $_date2 = explode("-", $DATE2)[2] . '-' . explode("-", $DATE2)[1] . '-' . explode("-", $DATE2)[0];
//        return $_date1->diff($_date2)->m + ($_date1->diff($_date2)->y*12);
        return (int)abs((strtotime($_date1) - strtotime($_date2))/(60*60*24*30));
    }
}

?>
