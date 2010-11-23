<?php

class FilesizeController extends ApplicationController 
{
	public function index()
	{
		Load::model('filesize')->go($_GET['dir']);
	}
}