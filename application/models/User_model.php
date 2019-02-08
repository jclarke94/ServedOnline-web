<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends SO_Model {

	/**
     * Generates a secure Hash given arbitrary data and a salt
     */
    private function generateSecureHash($data, $salt = "") {
        $hash1 = hash("sha1", $data);

        if ($salt != "") {
            $saltHash1 = hash("sha1", $salt);
            $saltHash2 = hash("sha1", $salt);

            return hash("sha1", $saltHash2 . $hash1 . $saltHash1);
        }

        return $hash1;
    }

    /**
     * Generate's a salt for hashing purposes
     */
    private function generateSalt() {
        return hash("sha1", uniqid(time().""));
    }

    /**
     * Creates an Alphanumeric string of a given length.
     *
     * Output will include lowercase and uppercase characters and numbers.
     */
    public function generateAlphanumericCode($length) {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMOPQRSTUVWXYZ123456789";

        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }



    public function validateUser($email, $password)
    {
        $this->db->select("id, password, salt");

        $this->db->where("email", $email);

        $testResult = $this->db->get("user");

        if ($testResult->num_rows() > 0) {
            $user = $testResult->first_row();

            $createComparePassword = $this->generateSecureHash($password, $user->salt);

            if ($createComparePassword == $user->password) {
                return $user->id;
            }
        }

        return FALSE;
    }


    public function generateUserAuthToken($userId) {
        $auth = $this->generateAlphanumericCode(64);

        $updateData = array(
            "userId" => $userId,
            "token" => $auth,
            "expiry" => date("Y-m-d H:i:s", strtotime("+7 Days"))
        );

        $this->db->insert("userAuthToken", $updateData);

        if ($this->db->insert_id() > 0) {
            return $auth;
        }
        return FALSE;
    }

    public function validateAuthToken($userID, $token) {

        $this->db->select("id");
        $this->db->where("userID", $userID);
        $this->db->where("token", $token);
        //$this->db->where("expiry <=", date("Y-m-d H:i:s", time()));
        $this->db->order_by("id", "desc");
        $result = $this->db->get("userAuthToken");

        if ($result->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function getAppUser($email, $password) {
    	$this->db->select("id, firstName, lastName, displayName, email, password");
    	$this->db->where("email", $email);
    	$result = $this->db->get("user");

    	if ($result->num_rows() > 0) {
    		$user = $result->first_row();
    		if ($user->password = $password) {
    			return $user;
    		} else {
    			return FALSE;
    		}
    	} else {
    		return FALSE;
    	}
    }

    public function createAppUser($fName, $lName, $displayName, $email, $password) {
    	// $this->db->select("id, email");
    	// $this->db->where("email", $email);
    	// $result = $this->db->get("user");

    	// if ($result > 0 ) {
    	// 	return "email already exists";
    	// } else { 

    		$updateData = array(
    			"firstName" => $fName, 
    			"lastName" => $lName, 
    			"displayName" => $displayName, 
    			"email" => $email, 
    			"password" => $password
    		);

    		//need to encrypt password with hash and SALT. 
    		//for now just do as is as time is pressing 


    		$this->db->insert("user", $updateData);
    		$id = $this->db->insert_id();
    		if ($id > 0) {
    			return $id;
    		} else {
    			return FALSE;
    		}
    	// }
    }


    public function getUsersForDropdown() {
        $this->db->select("id, firstName, lastName");
        $this->db->where("active", 1);

        $result = $this->db->get("user");

        $arrayForCodeigniter = array();
        $arrayForCodeigniter[-1] = "Show all users";

        foreach ($result->result_array() as $row)
        {
            //$arrayForCodeigniter[] = array($row["id"]=>$row["name"]);
            $arrayForCodeigniter[$row["id"]] = $row["firstName"] . " " . $row["lastName"];
        }

        return $arrayForCodeigniter;
    }
}
?>