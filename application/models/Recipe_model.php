<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recipe_model extends SO_Model {

	public function createNewRecipe($userId, $recipeTitle, $recipeDescription, $timerLength = NULL) {
		$updateData = array(
			"userId" => $userId, 
			"recipeTitle" => $recipeTitle,
			"recipeDescription" => $recipeDescription, 
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

}
?>