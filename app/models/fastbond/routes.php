<?php

class Routes
{    
	public function getConfig($app) # AUN SIGO PROBANDO ESTE ARRAY
	{
		$app = '/' . trim($app, '/');
		$dir = Files::readDir($app, array('allow' => array('config') ) );
		if ($dir)
		{
			return parse_ini_file($app . '/config/routes.ini');
		}
		else
		{
			$_SESSION['voice'] = 'no_encuentro_app';
		}
	}
}

?>