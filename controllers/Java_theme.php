<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Java_theme extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
        session_start();
        $this->load->model('user_model');
		$grup	= $this->user_model->sesi_grup($_SESSION['sesi']);
		if ($grup != 1 AND $grup != 2 AND $grup != 3)
		{
			if (empty($grup))
				$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
			else
				unset($_SESSION['request_uri']);
			redirect('siteman');
		}
		$this->load->model('header_model');
    }

    public function index()
    {
        echo '<h1>Theme Options</h1>';
    }
}
