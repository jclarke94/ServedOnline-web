<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends SO_Controller {
	public function testing() {
		$out = array("success" => true);
		$this->JSON($out);
	}
}