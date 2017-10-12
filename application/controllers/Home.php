<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// include 'ServerResponse.php';
class Home extends CI_Controller {
	public function getAllDog() {
		$obj = new ServerResponse();
		$this->load->database();
		$query = $this->db->query('SELECT * FROM Dog');

		foreach ($query->result() as $row) {
			$dog         = new Dog();
			$dog->name   = $row->name;
			$dog->age    = (int) $row->age;
			$dog->color  = $row->color;
			$obj->data[] = $dog;

			/*
		hoac co the add thang row
		$obj->data[] = $row;
		 */

		}
		$obj->code = 100;
		echo json_encode($obj);

	}

}
class ServerResponse {
	public $code = 0;
	public $data = array();
}
class Dog {
	public $name  = "";
	public $age   = 0;
	public $color = "";
}
