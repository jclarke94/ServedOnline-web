<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends SO_Controller {
	public function login() {
		$postdata = $this->input->post();
		$requiredParams = array("email", "password");
		$this->validateParams($postdata, $requiredParams);

		$result = $this->user_model->validateUser($postdata["email"], $postdata["password"]);

		if($result) {
			$authToken = $this->User_model->generateUserAuthToken($result);
			$customerResult = $this->User_model->getUser($result);

			$outBundle = array(
				"token" => $authToken, 
				"user" => $customerResult
			); 

			$this->JSON(array("success" => TRUE, "data" => $outBundle), 200);
		}

		$this->JSON(array("success" => FALSE, "error" => "Email and Password combination was incorrect."), 400);
	}
	
}