<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends SO_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

	public function createUser() {
		$postdata = $this->input->post();

		$this->validateParams($postdata, array("firstName", "lastName", "email", "password"));

		$result = $this->user_model->createAppUser(
			$postdata["firstName"], 
			$postdata["lastName"], 
			$postdata["email"], 
			$postdata["password"]
		);

		if ($result > 0) {
			// $validate = $this->user_model->validateUser($postdata["email"], $postdata["password"]);

			// if($validate) {
			// 	$authToken = $this->User_model->generateUserAuthToken($validate);
			// 	$customerResult = $this->User_model->getUser($validate);

			// 	$outBundle = array(
			// 		"token" => $authToken, 
			// 		"user" => $customerResult
			// 	); 

			// 	$this->JSON(array("success" => TRUE, "data" => $outBundle), 200);
			// }

			$outBundle = array(
				"id" => $result
			);
			$this->JSON(array("success" => TRUE, "data" => $outBundle));
		}

		$this->JSON(array("success" => FALSE, "error" => $result));

		//need to add returns and options as to why it didn't work e.g. duplicate email. 
	}
}
?>