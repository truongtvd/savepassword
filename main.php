<?php
//namespace Model;
include 'Model/Person.php';
// include 'ServerResponse.php';
    /*
    lay parameter
    */

//    $name = $_GET['name'];
//    echo "Hello, $name";


    /*
      tao Object
    */

    //$per = new Person();
    // $user = new Person();
    //
    // echo $user->name;

    /*
      Ket noi co so du lieu va xuat du lieu
    */
    $db = mysql_connect("localhost","root","") or
          die("Khong the ket noi csdl!");

    mysql_select_db("TestPython",$db) or die("Khong the select database!");
    $sql = "select * from Dog";
    $result = mysql_query($sql);
    $array = array();
    while ($data = mysql_fetch_assoc($result)) {
      $array[] = $data;
    }
    $obj = new ServerResponse();
    $obj->code = 100;
    $obj->data = $array;
    echo json_encode($obj);
//    echo "asas";
    function hello(){
      echo "hello";
    }

/**
 *
 */
class ServerResponse
{
  public $code = 0;
  public $data = array();
}

?>
