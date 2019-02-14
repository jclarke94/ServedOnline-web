<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Recipe extends SO_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('recipe_model');
    }

    public function createRecipe() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array(
    		"userId", 
    		"recipeTitle", 
    		"recipeDescription"
    	));

    	$userId = $postdata["userId"];
    	$recipeTitle = $postdata["recipeTitle"];
    	$recipeDescription = $postdata["recipeDescription"];
    	$timerLength = null;
    	if (isset($postdata["timerLength"])) {
    		$timerLength = $postdata["timerLength"];
    	} 
    	

    	$result = $this->recipe_model->createNewRecipe($userId, $recipeTitle, $recipeDescription, $timerLength);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	}

    	$this->JSON(array("success" => FALSE, "error" => "could not create new recipe"));
    }
}

?>