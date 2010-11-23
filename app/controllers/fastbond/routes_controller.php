<?php

Load::lib('fastbond/files');
Load::lib('fastbond/ku');

class RoutesController extends ApplicationController 
{
	public $template = 'fastbond/crud';
	
	public function index()
	{
		$this->routes = Load::model('fastbond/routes')->getConfig($_GET['app']);
	}
}