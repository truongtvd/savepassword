<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include "application/models/ServerResponse.php";
include "application/models/User.php";
/**
 * Created by PhpStorm.
 * User: truongtvd
 * Date: 10/9/17
 * Time: 9:06 AM
 */
class UserController extends CI_Controller{
    public function login(){
        if (isset($_POST['user_name']) && isset($_POST['pass_word'])){
            $user_name = $_POST['user_name'];
            $pass_word = $_POST['pass_word'];

            $this->load->database();
            $database = $this->db;
            $obj = new ServerResponse();

            $query = $database->get_where('user', array('username' => $user_name));
            if (!$query){
                $obj->code = 200;
                $obj->message = "Username or password incorrect";
                echo json_encode($obj);
                return;
            }
            foreach ($query->result() as $row) {
                $user        = new User();
                $user->user_name   = $row->username;
                $user->pass_word    = (int) $row->password;
                $user->user_id = (int) $row->user_id;
            }
            if ($query->num_rows() == 0){
                $obj->code = 200;
                $obj->message = "Username or password incorrect";
                echo json_encode($obj);
                return;
            }
            if ($user_name == $user->user_name && $pass_word == $user->pass_word){
                $user_id = $user->user_id;
                $queryToken = $database->get_where('api', array('user_id' => $user_id));
                if ($queryToken->num_rows() == 0) {
                    //tao token cho user va insert vao bang api
                    $token =  $this->getToken(10);
                    $data = array('user_id'=>$user_id,'token'=>$token);
                    $database->insert("api",$data);
                }else{
                    //lay token cho user tu bang api
                    foreach ($queryToken->result() as $rowtoken) {
                        $token = $rowtoken->token;
                    }
                }

                $record  = array();
                $record['username'] = $user->user_name;
                $record['token'] = $token;
                echo $array = json_encode(array('code'=>100,'data'=>$record));
                return;
            }else{
                $obj->code = 200;
                $obj->message = "Username or password incorrect";
                echo json_encode($obj);
                return;
            }
        }else{
            $obj = new ServerResponse();
            $obj->code = 66;
            $obj->message = "Params invalided";
            echo json_encode($obj);

        }
    }

    public function signup(){
        if (isset($_POST['user_name']) && isset($_POST['pass_word'])) {
            $user_name = $_POST['user_name'];
            $pass_word = $_POST['pass_word'];
            $this->load->database();
            $database = $this->db;
            $obj = new ServerResponse();
            //insert user
            $countusername = $database->get_where('user', array('username' => $user_name));
            if ($countusername->num_rows()>0){
                $obj->code = 200;
                $obj->message = "Username exist";
                echo json_encode($obj);
            }else{
                $data = array(
                    'username' => $user_name,
                    'password' => $pass_word);

                $database->insert("user"  ,$data);

                $query = $database->get_where('user', array('username' => $user_name));
                $user = new User();
                foreach ($query->result() as $row) {
                    $user->user_name   = $row->username;
                    $user->pass_word    = (int) $row->password;
                    $user->user_id = (int) $row->user_id;
                }
                $user_id = $user->user_id;
                $token =  $this->getToken(10);
                $dataToken = array(
                    'user_id' => $user_id,
                    'token' => $token);
                $database->insert("api"  ,$dataToken);
                $record  = array();
                $record['username'] = $user->user_name;
                $record['token'] = $token;
                echo $array = json_encode(array('code'=>100,'data'=>$record));

            }

        }else{
            $obj = new ServerResponse();
            $obj->code = 66;
            $obj->message = "Params invalided";
            echo json_encode($obj);
        }
    }

    function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    function getToken($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }
}