<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RecipeIngredients extends SO_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('recipe_model');
    }

    public function createIngredient() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("recipeId", "stepNumber", "ingredient"));

    	$recipeId = $postdata["recipeId"];
    	$stepNumber = $postdata["stepNumber"];
    	$ingredient = $postdata["ingredient"];

    	$result = $this->recipe_model->createNewIngredient($recipeId, $stepNumber, $ingredient);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	} 
    	
    	$this->JSON(array("success" => FALSE, "error" => "Could not create ingredient"));
    }

    public function getIngredients() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("recipeId"));

    	$recipeId = $postdata["recipeId"];

    	$result = $this->recipe_model->getIngredientsForRecipe($recipeId);

    	if ($result) {
    		$this->JSON(array("success" => TRUE, "data" => $result));
    	}

    	$this->JSON(array("success" => FALSE, "error" => "could not get ingredients"));
    }

    public function removeIngredient() {
    	$postdata = $this->input->post();

    	$this->validateParams($postdata, array("id"));

    	$id = $postdata["id"];

    	$result = $this->recipe_model->deleteIngredient($id);

    	if ($result) {
    		$this->JSON(array("success" => TRUE));
    	}

    	$this->JSON(array("success" => FALSE));
    }
}
?>