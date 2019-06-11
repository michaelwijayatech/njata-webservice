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

    public function getMonthText($MONTH)
    {
        switch ($MONTH) {
            case '1':
            case '01':
                return 'January';
            case '2':
            case '02':
                return 'February';
            case '3':
            case '03':
                return 'March';
            case '4':
            case '04':
                return 'April';
            case '5':
            case '05':
                return 'May';
            case '6':
            case '06':
                return 'June';
            case '7':
            case '07':
                return 'July';
            case '8':
            case '08':
                return 'August';
            case '9':
            case '09':
                return 'September';
            case '10':
                return 'October';
            case '11':
                return 'November';
            case '12':
                return 'December';
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

    public function addMoneySeparator($MONEY, $DECIMAL){
        return number_format($MONEY, $DECIMAL,",",".");
    }

    public function checkDifferenceBetweenTwoDate($DATE1, $DATE2){
        $_date1 = explode("-", $DATE1)[2] . '-' . explode("-", $DATE1)[1] . '-' . explode("-", $DATE1)[0];
        $_date2 = explode("-", $DATE2)[2] . '-' . explode("-", $DATE2)[1] . '-' . explode("-", $DATE2)[0];
//        return $_date1->diff($_date2)->m + ($_date1->diff($_date2)->y*12);
        return (int)abs((strtotime($_date1) - strtotime($_date2))/(60*60*24*30));
    }

    public function _array_sort($ARRAY, $KEY){
        foreach ($ARRAY as $k=>$v){
            $b[] = strtolower($v[$KEY]);
        }

        asort($b);

        foreach ($b as $k=>$v) {
            $c[] = $ARRAY[$k];
        }

        return $c;
    }

    public function numberTowords($num)
    {
        $ones = array(
            0 => "",
            1 => "one",
            2 => "two",
            3 => "three",
            4 => "four",
            5 => "five",
            6 => "six",
            7 => "seven",
            8 => "eight",
            9 => "nine",
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen",
            18 => "eighteen",
            19 => "nineteen"
        );
        $tens = array(
            1 => "ten",
            2 => "twenty",
            3 => "thirty",
            4 => "forty",
            5 => "fifty",
            6 => "sixty",
            7 => "seventy",
            8 => "eighty",
            9 => "ninety"
        );
        $hundreds = array(
            "hundred",
            "thousand",
            "million",
            "billion",
            "trillion",
            "quadrillion"
        ); //limit t quadrillion
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr);
        $rettxt = "";
        foreach($whole_arr as $key => $i){
            if($i < 20){
                $rettxt .= $ones[$i];
            }elseif($i < 100){
                $rettxt .= $tens[substr($i,0,1)];
                $rettxt .= " ".$ones[substr($i,1,1)];
            }else{
                $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
                $rettxt .= " ".$tens[substr($i,1,1)];
                $rettxt .= " ".$ones[substr($i,2,1)];
            }
            if($key > 0){
                $rettxt .= " ".$hundreds[$key]." ";
            }
        }
        if($decnum > 0){
            $rettxt .= " and ";
            if($decnum < 20){
                $rettxt .= $ones[$decnum];
            }elseif($decnum < 100){
                $rettxt .= $tens[substr($decnum,0,1)];
                $rettxt .= " ".$ones[substr($decnum,1,1)];
            }
        }
        return $rettxt;
    }

    public function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = $this->penyebut($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." Puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " Ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " Ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " Juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " Milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " Trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }
        return $temp;
    }

    public function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "Minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil . ' Rupiah';
    }

}

?>
