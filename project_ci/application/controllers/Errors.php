<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends CI_Controller {

	public function page_missing()
	{
		$this->load->view('error_404');
	}
}
