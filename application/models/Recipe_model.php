<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recipe_model extends SO_Model {

	public function createNewRecipe($userId, $recipeTitle, $recipeDescription, $userName, $timerLength = NULL) {

		$updateData = array(
			"userId" => $userId, 
			"recipeTitle" => $recipeTitle,
			"recipeDescription" => $recipeDescription, 
			"userName" => $userName,
			"timerLength" => $timerLength
		);

		$this->db->insert("recipe", $updateData);
		$id = $this->db->insert_id();
    	if ($id > 0) {
    		return $id;
   		} else {
   			return FALSE;
   		}
	}

	public function getRecipesList() {
		$this->db->select("id, userId, recipeTitle, recipeDescription, userName, timerLength, likes, dateOfCreation");
		$this->db->where("active", 1);
		$this->db->order_by("id", "desc");
		$this->db->limit('15');
		$results = $this->db->get("recipe");

		return $results->result_array();
	}

	public function getSingleRecipe($id) {
		$this->db->select("id, userId, recipeTitle, recipeDescription, userName, timerLength, likes, dateOfCreation");
		$this->db->where("id", $id);
		$result = $this->db->get("recipe");

		return $result->first_row();
	}

	public function deleteSingleRecipe($id) {
		$this->db->where("id", $id);
		$this->db->delete("recipe");

		return $this->db->affected_rows() > 0;
	}

	public function getUsersRecipes($userId) {
		$this->db->select("id, userId, recipeTitle, recipeDescription, userName, timerLength, likes, dateOfCreation");
		$this->db->where("userId", $userId);
		$this->db->order_by("id", "desc");
		$this->db->limit('15');
		$results = $this->db->get("recipe");

		return $results->result_array();
	}

	public function createNewRecipeStep($recipeId, $stepDescription, $stepNumber, $finalStep, $timer = NULL) {

		$updateData = array(
			"recipeId" => $recipeId, 
			"stepDescription" => $stepDescription, 
			"stepNumber" => $stepNumber, 
			"finalStep" => $finalStep, 
			"timer" => $timer
		);

		$this->db->insert("recipeSteps", $updateData);
		$id = $this->db->insert_id();
		if ($id) {
			return $id;
		} else {
			return FALSE;
		}
	}

	public function getSteps($recipeId) {
		$this->db->select("id, recipeId, stepDescription, stepNumber, finalStep, timer");
		$this->db->where("recipeId", $recipeId);
		$this->db->order_by("stepNumber", "asc");
		$results = $this->db->get("recipeSteps");

		return $results->result_array();
	}



}
?>