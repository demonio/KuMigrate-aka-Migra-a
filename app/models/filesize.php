<?php

Load::lib('files');
Load::lib('ku');

class Filesize
{      
	public function go($dir)
	{
		echo '<pre>';
		passthru('ls -lRS /', $lol);
		echo '</pre>';

		die;
	}
}

?>