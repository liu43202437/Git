<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		/*if(!$this->input->is_cli_request())
		{
			echo "This script can only be accessed via the command line" . PHP_EOL;
			exit();
		}*/
	}

	/**
	 * Cron controller.
	 */
	public function auto_reminder()
	{
		
	}
}
