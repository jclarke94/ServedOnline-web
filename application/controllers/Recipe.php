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

    public function getRecipes() {
    	$postdata = $this->input->post();

    	$userId = $postdata["userId"];

    	//todo get list of following userId's 

    	$result = $this->recipe_model->getRecipesList();

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	} 

    	$this->JSON(array("success" => FALSE, "error" => "error collecting recipes"));
    }

    public function getRecipe() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("recipeId"));

    	$recipeId = $postdata["recipeId"];

    	$result = $this->recipe_model->getSingleRecipe($recipeId);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	} 

    	$this->JSON(array("success" => FALSE, "error" => $result));
    }

    public function getUsersRecipes() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("userId"));

    	$userId = $postdata["userId"];

    	$result = $this->recipe_model->getUsersRecipes($userId);

    	if($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	}

    	$this->JSON(array("success" => FALSE, "error" => "unknown error while gathering users recipes"));



    }

    public function deleteRecipe() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("recipeId"));

    	$recipeId = $postdata["recipeId"];

    	$result = $this->recipe_model->deleteSingleRecipe($recipeId);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	}

    	$this->JSON(array("success" => FALSE, "error" => "unknown error"));
    }
}

?>