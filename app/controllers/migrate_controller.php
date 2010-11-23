<?php

class MigrateController extends ApplicationController 
{
	public $template = 'ku';
	
	public function index()
	{
		$this->view = 'list';
	}
	
	public function setPathes()
	{
		$_SESSION['result'] = array();
		$_SESSION['dad'] = rtrim($_GET['dir'], '/');
		Load::model('migrate')->setPathes($_GET['dir']);
		$this->items = $_SESSION['result'];
		$this->view = 'list';
	}
	
	public function setStrings()
	{
		$_SESSION['result'] = array();
		Load::model('migrate')->setStrings($_GET['dir']);
		$this->items = $_SESSION['result'];
		$this->view = 'list';
	}
}