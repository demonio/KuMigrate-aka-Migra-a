<?php

class Ku
{
	static public function _js($fn, $value, $delay=0)
	{
		# ALGUNAS FUNCIONES JS TIENEN OTRO FORMATO
		$a = array('location');
	
		# FORMATO FN='VALUE';
		if (in_array($fn, $a) )
		{
			$s = "$fn='$value';";
		}
		# FORMATO FN('VALUE');
		else
		{
			$s = "$fn('$value');";
		}
	
		# RETARDO PARA LANZAR LA FUNCION EN SEGUNDOS    
		if ($delay)
		{
			$delay = $delay*1000;
			$s = "setTimeout(\"$s\", $delay);";
		}
		
		return $s;
	}
	
	static public function _wrap($s, $tag, $opt='', $charset='UTF-8')
	{
		if ($tag == 'script') $opt .= ' type="text/javascript" charset="' . $charset . '"';
		
		return "<$tag$opt>$s</$tag>";
	}
	
		static public function _location($url, $delay=0)
		{
			# SE COMPRUEBA SI SE ENVIARON CABECERAS. POR EJEMPLO COMO PASA CON AJAX
			if ( headers_sent() )
			{
				# SI EL USUARIO TIENE JAVASCRIPT ACTIVADO SE USA LOCATION
				echo self::_wrap(self::_js('location', $url, $delay), 'script');
				
				# SI NO LO TIENE SE USA META
				echo self::_wrap('<meta http-equiv="refresh" content="' . $delay . ';url=' . $url . '" />', 'noscript');
			}
			else
			{
				# SI NO SE ENVIARON CABECERAS, AQUI VA UNA
				sleep($delay);
				header('Location: ' . $url);
			}
			exit();
		}

		static public function _pre($mix)
		{
			 return self::_wrap(htmlentities(print_r($mix, 1), ENT_QUOTES), 'pre');
		}
		
			static public function _die($mix)
			{
				die( self::_pre($mix) );
			}
		
			static public function _flush($mix)
			{
				echo '<hr />' . self::_pre($mix);
				ob_flush();
				flush();
			}
			
		static public function _red($s)
		{
			 return self::_wrap($s, 'span', ' style="color:red"');
		}
}