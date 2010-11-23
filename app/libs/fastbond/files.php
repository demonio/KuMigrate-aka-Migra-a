<?php

class Files
{    
    static public function createDir($path)
    {
        $path = trim($path, '/');
        $dirs = explode('/', $path);
        
        $path = '';    
        foreach ($dirs as $dir)
        {
            $path .= '/' . $dir;
                        
            if ( file_exists('/' . $path) ) continue;

            mkdir($path);
        }
    }
	
    static public function readDir($dir, $opt=array() )
    {
		if ( ! file_exists($dir) ) return 0; # DIR NO EXISTE
		
        foreach (new DirectoryIterator($dir) as $item)
        {
            if ( $item->isDot() ) continue; # NADA DE . Y ..
            $name = $item->getFilename();
            if ( ! empty($opt['deny']) and in_array($name, $opt['deny']) ) continue; # NADA DE LO NEGADO
            if ( ! empty($opt['allow']) and ! in_array($name, $opt['allow']) ) continue; # NADA DE LO NO PERMITIDO
			
            if ( $item->isDir() )
            {
				$k = rtrim($dir, '/') . '/' . $name; # DIR NUEVO
				
                if ( ! empty($opt['recursive']) ) $a[$k] = self::readDir($k, array('recursive'=>1) );
				else $a[$k] = array();
            }
            else
            {
                $a[] = $name;
            }
        }
        if ( isset($a) ) return $a;
		else return 0; # DIR VACIO
	}
	
    static public function readFile($file)
    {
        if ( ! file_exists($file) ) return $file . ' no exists!';

        if ( is_dir($file) ) return self::readFile($file);

        return file_get_contents($file);
    }
	
    static public function updateFile($file, $data)
    {
        if ( ! file_exists($file) ) return $file . ' no exists!';
		
        return file_put_contents($file, $data);
    }

    static public function deleteDir($dir)
    {
		if ( ! file_exists($dir) ) return 0; # SI NO EXISTE NO HAY MAS QUE HACER
		
		if ( ! self::readDir($dir) ) rmdir($dir); # SI ESTA VACIO SE BORRA
    }
	
    static public function moveFile($from, $to)
    {
        if ( file_exists($to) ) return 0;

        $path = dirname($to);
		self::createDir($path);
        $copy = copy($from, $to);
		$unlink = unlink($from);
		if ($copy and $unlink) return '<em>' . $from . ' => ' . $to . '<em> <strong>movido!</strong><br />';
		else return 0;
    }
	
    static public function moveDir($from, $to='')
    {
		$items = self::readDir($from);
		
		if ( ! $items )
		{
			return self::deleteDir($from);
		}
		
		foreach ($items as $k => $v)
		{		
			if ( is_string($v) ) # ARCHIVO
			{
				$_SESSION['result'][] = self::moveFile($from . '/' .  $v, $to . '/' . $v);
			}
			else # DIRECTORIO
			{
				$dir = str_replace($from, '', $k);
				self::moveDir($k, $to . $dir);
			}
		}
		self::deleteDir($from);
    }	
}

?>