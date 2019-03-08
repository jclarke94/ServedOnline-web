<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RecipeSteps extends SO_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('recipe_model');
        $this->load->model('user_model');
    }

    public function createStep() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array(
    		"recipeId", 
    		"stepDescription", 
    		"stepNumber", 
    		"finalStep"
    	));

    	$recipeId = $postdata["recipeId"];
    	$stepDescription = $postdata["stepDescription"];
    	$stepNumber = $postdata["stepNumber"];
    	$finalStep = $postdata["finalStep"];
    	$timer = NULL;
    	if (isset($postdata["timer"])) {
    		$timer = $postdata["timer"];
    	}

    	$result = $this->recipe_model->createNewRecipeStep($recipeId, $stepDescription, $stepNumber, $finalStep, $timer);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	} 

    	$this->JSON(array("success" => FALSE, "error" => "Could not create step"));
    }

    public function getRecipeSteps() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("recipeId"));

    	$recipeId = $postdata["recipeId"];

    	$results = $this->recipe_model->getSteps($recipeId);

    	if ($results) {
    		$this->JSON(array("success" => TRUE, "data" => $results));
    	}

    	$this->JSON(array("success" => FALSE, "error" => "could not retrieve steps"));
    }

    public function removeStep() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("id"));

    	$id = $postdata["id"];

    	$result = $this->recipe_model->deleteStep($id);

    	if ($result) {
    		$this->JSON(array("success" => TRUE));
    	}

    	$this->JSON(array("success" => FALSE));
    }
}

?>