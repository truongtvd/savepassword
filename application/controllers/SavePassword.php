<?php
/**
 * Created by PhpStorm.
 * User: truongtvd
 * Date: 10/12/17
 * Time: 9:39 AM
 */
defined('BASEPATH') OR exit('No direct script access allowed');
define("SAVE_PASS","savepass");
include "application/models/ServerResponse.php";
include "application/models/User.php";
include "application/models/Save.php";

class SavePassword extends CI_Controller
{

    public function save(){
        $obj =  new ServerResponse();
        if (isset($_POST['token']) && isset($_POST['user_name']) && isset($_POST['pass_word']) && isset($_POST['host'])){
            $token =  $_POST['token'];
            $username = $_POST['user_name'];
            $password =$_POST['pass_word'];
            $host =$_POST['host'];
            $note = "";
            if (isset($_POST['note'])){
                $note = $_POST['note'];
            }
            $this->load->database();
            $database = $this->db;
            $user_id = $this->getUserId($database,$token);
            $data = array(
                'user_id' => $user_id,
                'username' => $username,
                'password'=>$password,
                'host'=>$host,
                'note'=>$note);
            $database->insert(SAVE_PASS,$data);
            $obj->code = 100;
            $obj->message = "Success";
            echo json_encode($obj);

        }else{
            $obj->code = 66;
            $obj->message = "Params invalidate";
            echo json_encode($obj);
        }

    }

    public function edit(){
        $obj =  new ServerResponse();
        if (isset($_POST['token']) && isset($_POST['id'])){
            $token =  $_POST['token'];
            $id = $_POST['id'];
            $this->load->database();
            $database = $this->db;
            $user_id = $this->getUserId($database,$token);
            $select = $database->get_where(SAVE_PASS,array('user_id'=>$user_id,'id'=>$id));
            if($select->num_rows() > 0){
                $data = array();
                if (isset($_POST['user_name'])){
                    $data['username'] = $_POST['user_name'];
                }
                if (isset($_POST['pass_word'])){
                    $data['password'] = $_POST['pass_word'];
                }
                if (isset($_POST['host'])){
                    $data['host'] = $_POST['host'];
                }
                if (isset($_POST['note'])){
                    $data['note'] = $_POST['note'];
                }
                $database->where('id',$id);
                $database->update(SAVE_PASS,$data);
                $obj->code = 100;
                $obj->message = "Success";
                echo json_encode($obj);

            }else{
                $obj->code = 200;
                $obj->message = "Edit error";
                echo json_encode($obj);
            }

        }else{
            $obj->code = 66;
            $obj->message = "Params invalidate";
            echo json_encode($obj);
        }
    }
    public function delete(){
        $obj =  new ServerResponse();
        if (isset($_POST['token']) && isset($_POST['id'])){
            $token =  $_POST['token'];
            $id = $_POST['id'];
            $this->load->database();
            $database = $this->db;
            $user_id = $this->getUserId($database,$token);
            $select = $database->get_where(SAVE_PASS,array('user_id'=>$user_id,'id'=>$id));
            if($select->num_rows() > 0){
                $database->where('id',$id);
                $database->delete(SAVE_PASS);
                $obj->code = 100;
                $obj->message = "Success";
                echo json_encode($obj);

            }else{
                $obj->code = 200;
                $obj->message = "Delete error";
                echo json_encode($obj);
            }

        }else{
            $obj->code = 66;
            $obj->message = "Params invalidate";
            echo json_encode($obj);
        }
    }

    public function getall(){
        $obj =  new ServerResponse();
        $this->load->database();
        $database = $this->db;

        if (isset($_POST['token'])){
            $token =  $_POST['token'];
            $user_id = $this->getUserId($database,$token);
            $database = $database->order_by('id','desc');
            $select = $database->get_where(SAVE_PASS,array('user_id'=>$user_id));
            foreach ($select->result() as $row)
            {
                $save =  new Save();
                $save->username = $row->username;
                $save->password = $row->password;
                $save->host = $row->host;
                $save->note = $row->note;
                $obj->data[] = $save;
            }
            echo json_encode($obj);
        }else{
            $obj->code = 66;
            $obj->message = "Params invalidate";
            echo json_encode($obj);
        }
    }
    function getUserId($database,$token){
        $user_id = "";
        $query = $database->get_where('api', array('token' => $token));
        if ($query->num_rows() == 0){

            return $user_id;
        }
        foreach ($query->result() as $row)
        {
            $user_id = $row->user_id;
        }
        return $user_id;
    }
}