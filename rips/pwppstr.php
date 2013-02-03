<?php	
/** 

Wordpress Plugins Scanner
	by Andrei Avadanei (andrei.avadanei@ccsir.ro) 
			

Copyright (C) 2013 Cyber Security Research Center from Romania

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.		

**/
$plugin = '';
/* proxy wp plugins scanner to rips */
if(isset($_GET['plugin'])) 
{
	$plugin = trim($_GET['plugin']);
	$plugin = str_replace(array('.','/','&','%','\\','*','#'),'',$plugin);
	if($plugin != '')
	{
		ob_start('callback');
		include_once('index.php');
		ob_end_flush();
	}
}

function callback($content) 
{
	global $plugin;
	$injection = '
			<script type="text/javascript" src="../static/jquery.js"></script>
			<script type="text/javascript">
			function inject_wpscanner() {
				$("#location").val("../plugins/'.$plugin.'");
				$("#subdirs").attr("checked",true); 
				$("#verbosity option[value=\'4\']").prop("selected",true);
				$("input[value=\'scan\']").click();
			}
			
			setTimeout(inject_wpscanner, 100);
			</script>
		';
		
	$found   = stripos($content, '</head>');
	$content = substr($content, 0, $found).$injection.substr($content, $found);
	return $content;
}
/*var path = document.getElementById("location"); path.value = "../plugins/'.$plugin.'";
				alert(path); */
?>