<?php

Load::lib('files');
Load::lib('ku');

class Migrate
{      
	public function getPathes() # AUN SIGO PROBANDO ESTE ARRAY
	{
		return array
		(
			# 0.5 => 1.0 beta1
			'/apps/default' => '/app',
			'/apps' => '',
			'/app/controllers/application.php' => '/app/application.php',
			'/app/views/layouts' => '/app/views/templates',
			'/app/views/index.phtml' => '/app/views/templates/default.phtml',
			'/app/views/not_found.phtml' => '/app/views/errors/404.phtml',
			'/app/views/bienvenida.phtml' => '/app/views/pages/index.phtml',
			'/app/helpers' => '/app/extensions/helpers',
			'/app/models/base/model_base.php' => '/app/model_base.php',
			'/app/models/base/' => '',
			'/cache' => '/app/cache',
			'/config' => '/app/config',
			'/docs' => '/app/docs',
			'/logs' => '/app/logs',
			'/scripts' => '/app/scripts',
			'/test' => '/app/test',
			
			# 1.0 beta1 => 1.0 beta2
		);
	}
		
    public function getStrings()
    {
		return array
		(
			# application
			'application' => array
			(
				'ControllerBase' => 'ApplicationController',
				'public function init(' => 'protected function initialize(',
				"render_view('bienvenida');" => '',
			),
			
			# models
			'models' => array
			(
				'public $mode' => 'public $database',
			),
			
			# Callbacks
			array
			(
				'public function initialize' => 'protected function initialize',
				'public function finalize' => 'protected function finalize',
				'public function before_filter' => 'protected function before_filter',
				'public function after_filter' => 'protected function after_filter',
			),
		
			# config.ini
			'boot.ini' => array
			(
				'kumbia.' => '',
				'mail.' => '',
				'libchart.' => '',
				'extensions' => 'libs',
			),
			
			# routes.ini
			'routes.ini' => array
			(
				'/ = index/index' . PHP_EOL => '',
				'[default]' => '[default]' . PHP_EOL . '/ = index/index',
			),
		
			# Input::
			array
			(
				'$this->has_post(' => 'Input::hasPost(',
				'$this->has_get(' => 'Input::hasGet(',
				'$this->has_request(' => 'Input::hasRequest(',
				'$this->post(' => 'Input::post(',
				'$this->get(' => 'Input::get(',
				'$this->request(' => 'Input::request(',
			),
			
			# View::
			array
			(
				'$this->cache' => 'View::cache(',
				'$this->render' => 'View::select(',
				'$this->set_response' => 'View::response(',
				' content(' => ' View::content(', # ARREGLO CON ESPACIO DELANTE PARA QUE NO SEA RECURRENTE
				'render_partial(' => 'View::partial(',
			),
			
			# Router::
			array
			(
				'$this->route_to(' => 'Router::route_to(', # Se recomienda $this->route_to => Router::redirect
				'$this->redirect(' => 'Router::redirect(',
			),
			
			# Html::
			array
			(
				'img_tag(' => 'Html::img(',
				'link_to(' => 'Html::link(',
				'stylesheet_link_tag(' => 'Tag::css(',
				'stylesheet_link_tags(' => 'Html::includeCss(',
				'javascript_include_tag(' => 'Tag::js(',
			),
			
			# Form::
			array
			(
				'end_form_tag(' => 'Form::close(', # end_form_tag DEBE IR PRIMERO QUE form_tag O end_form_tag SERA end_Form::open
				'form_tag(' => 'Form::open(',
				'input_field_tag(' => 'Form::input(',
				'text_field_tag(' => 'Form::text(',
				'password_field_tag(' => 'Form::pass(',
				'textarea_tag(' => 'Form::textarea(',
				'hidden_field_tag(' => 'Form::hidden(',
				'select_tag(' => 'Form::select(',
				'file_field_tag(' => 'Form::file(',
				'button_tag(' => 'Form::button(',
				'submit_image_tag(' => 'Form::submitImage(',
				'submit_tag(' => 'Form::submit(',
				'checkbox_field_tag(' => 'Form::check(',
				'radio_field_tag(' => 'Form::radio(',
			),
		);
	}
	
	public function setPathes($dir)
	{	
		$items = Files::readDir($dir, array('deny' => array('library', 'public') ) ); # VEMOS QUE HAY EN CADA DIRECTORIO

		if ( ! $items ) return; # NO SE PUEDE PROCESAR DIRECTORIOS VACIOS
			
		foreach ($this->getPathes() as $old => $new) # PARA CADA ITEM HAY QUE VER LOS CAMBIOS A REALIZAR
		{
			$to = $_SESSION['dad'] . $new; # RURA NUEVA COMPLETA
			
			foreach ($items as $k => $v) # VEMOS EL PUS DEL DIRECTORIO
			{
				if ( is_array($v) ) # SI ES DIRECTORIO
				{
					if ($new == '') # SI EL DESTINO ESTA VACIO SE BORRA EL ORIGEN
					{
						Files::deleteDir($k);
					}
					else if ($k == $_SESSION['dad'] . $old and $k <> $to) # SI HAY CAMBIOS 
					{
						Ku::_flush("$k, $to");
						Files::moveDir($k, $to); # MOVEMOS EL DIRECTARIO A LA RUTA NUEVA
					}
					$this->setPathes($k); # RECURSIVIDAD
				}
				else # SI ES ARCHIVO
				{
					$from = $dir . '/' . $v;
					if ($from <> $_SESSION['dad'] . $old) continue;

					$_SESSION['result'][] = Files::moveFile($from, $to);
				}
			}
		}
	}
	
	public function setStrings($dir)
	{
		$items = Files::readDir($dir, array('deny' => array('test', 'scripts', 'public', 'logs', 'library', 'docs', 'cache') ) ); # VEMOS QUE HAY EN CADA DIRECTORIO
	
		if ( ! $items ) return; # NO SE PUEDE PROCESAR DIRECTORIOS VACIOS
			
		$found = 0;
		$migration = 0;
		$s = '';
		foreach ($items as $k => $v) # VEMOS EL PUS DEL DIRECTORIO
		{
			if ( is_array($v) ) $this->setStrings($k); # RECURSIVIDAD SI ES UN DIRECTORIO
			
			$file = $dir . '/' . $v;
			$content = Files::readFile($file); # CONTENIDO DEL ARCHIVO
	
			foreach ($this->getStrings() as $path => $changes) # PARA CADA ITEM HAY QUE VER LOS CAMBIOS A REALIZAR
			{
				foreach ($changes as $old => $new) # PARA CADA ITEM HAY QUE VER LOS CAMBIOS A REALIZAR
				{
					
					if ( ! preg_match('/\d+/', $path) and ! strstr($file, $path) ) break; # CONTROL DE RUTAS
					
					if ( ! strstr($content, $old) ) continue; # SI NO HAY PALABRA PARA REEMPLAZAR
					$found = 1; # SE HA ENCONTRADO ALGO QUE CAMBIAR
					$s .= substr_count($content, $old) . ' <em>' . $old . ' => ' . $new . '</em><br />'; # SE CUENTA EL NUMERO DE SUSTITUCIONES
					
					$content = str_replace(array("\r\n", "\r", "\n"), '[[:eol:]]', $content); # HUMM
					$content = str_replace('[[:eol:]]', PHP_EOL, $content); # HUMM 2
					
					$content = str_replace($old, $new, $content);
				}
			}
	
			if ($found) # SI SE ENCONTRARON CAMBIOS SE ACTUALIZA EL CONTENIDO
			{
				Files::updateFile($file, $content);
				$found = 0;
				$migration = 1;
				$_SESSION['result'][] = $s . $file . ' <strong>actualizado!</strong>';
				$s = '';
			}
		}
		if ( ! $migration ) $_SESSION['result'][] = '<strong>No hubo cambios en</strong> ' . $dir;
	}
}

?>