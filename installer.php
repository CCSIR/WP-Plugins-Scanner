<?php
/** 

Wordpress Plugins Scanner
	by Andrei Avadanei (andrei.avadanei@ccsir.ro) 
			

Copyright (C) 2013 Cyber Security Research Center from Romania

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.		

**/
if(IN_WP_PLUGIN_SCANNER)
{
	/* lets hack rips */
	if(!file_exists('rips/pwppstr.php') || filesize('rips/pwppstr.php') != filesize('pwppstr.php'))
	{
		$fh = fopen('pwppstr.php','r'); /* proxy wp plugins scanner to rips */
		$content = fread($fh, filesize('pwppstr.php'));
		fclose($fh);
		$fh2 = fopen('rips/pwppstr.php','w');
		fwrite($fh2, $content);
		fclose($fh2);
	}
}
?>