<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends SO_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function getUser() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("email", "password"));

    	$email = $postdata["email"];
    	$password = $postdata["password"];

    	$result = $this->user_model->getAppUser(
    		$email, 
    		$password
    	);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	}

    	$this->JSON(array("success" => FALSE, "error" => "could not retrieve user"));
    }

	public function createUser() {
		$postdata = $this->input->post();

		$this->validateParams($postdata, array("firstName", "lastName", "email", "password"));

		$fName = $postdata["firstName"];
		$lName = $postdata["lastName"];
		$email = $postdata["email"];
		$password = $postdata["password"];
		$displayName = $fName." ".$lName;

		$result = $this->user_model->createAppUser(
			$fName, 
			$lName,
			$displayName,
			$email,
			$password
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
				"id" => $result,
				"firstName" => $fName,
				"lastName" => $lName,
				"displayName" => $displayName,
				"email" => $email,
				"password" => $password, 
				"salt" => Null
			);
			$this->JSON(array("success" => TRUE, "data" => $outBundle));
		}

		$this->JSON(array("success" => FALSE, "error" => $result));

		//need to add returns and options as to why it didn't work e.g. duplicate email. 
	}
}
?>