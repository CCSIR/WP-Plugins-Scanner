<?php
/** 

Wordpress Plugins Scanner
	by Andrei Avadanei (andrei.avadanei@ccsir.ro) 
			

Copyright (C) 2013 Cyber Security Research Center from Romania

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.		

**/
set_time_limit(0);
ini_set('memory_limit', '128M');
$version = '1.0'; 
if(!is_dir('plugins')) mkdir(plugins);
header("Content-Type: text/html");

/* dispatcher */
if(isset($_GET['type']))
{
	$content = '';
	switch($_GET['type'])
	{
		case 'load':
			$content = load_plugins();
		break;
		case 'download':
			$content = download_plugins(@$_GET['link']);
		break;
		case 'scan':
			$content = scan_plugin(@$_GET['plugin']);
		break;
		default:
			$content = '<div id="error">Invalid request.</div>';
		break;
	}
	echo $content;
}

/* download plugins from wp repository from a specific link */
function download_plugins($link)
{
	$outputf = '<div id="error!">Submitted link was invalid.</div>';
	$link = trim(urldecode($link));
	 
	if(!empty($link) && stristr($link,'http://wordpress.org/',true) !== FALSE) //if !empty & is wordpress.org link
	{
		$content = file_get_contents($link);
		
		preg_match_all('/<h3><a href="http:\/\/wordpress\.org\/extend\/plugins\/(.*?)">(.*?)<\/a><\/h3>/i',$content, $plugins); 
		/* 
			0 - links 
			1 - plugin folder (names)
			2 - plugin names 
		*/
		/* having fun */
		$outputf = '<div id="error">Submitted link returned an invalid content.</div>';
		if(isset($plugins[0],$plugins[1],$plugins[2]) && 
		   sizeof($plugins[0]) && sizeof($plugins[1]) && sizeof($plugins[2]) && 
		   sizeof($plugins[0])/sizeof($plugins[1])*sizeof($plugins[2]) == sizeof($plugins[0])
		   )
		{
			$outputf = '<div id="success">Found '.sizeof($plugins[1]).' plugins.</div>';
			/* the "magic" rofl */
			foreach($plugins[1] as $id => $plugin)
			{
				$plugin_clean = htmlentities(str_replace('/','',$plugin), ENT_QUOTES);
				$plugin_name  = htmlentities($plugins[2][$id], ENT_QUOTES);
				$file = 'plugins/'.$plugin_clean.'.zip';
				file_put_contents($file,file_get_contents('http://downloads.wordpress.org/plugin/'.$plugin_clean.'.zip'));
				
				$zip = new ZipArchive;
				$res = $zip->open($file);
				if ($res === TRUE) 
				{
				  $zip->extractTo('plugins/');
				  $zip->close(); 
				}
				$outputf .= '<li class="plugin" id="'.$plugin_clean.'">'.$plugin_name.' <span><input class="man_scan" type="button" id="'.$plugin_clean.'" value="Scan plugin" /></span></li>';
				unlink($file);
			}
			$outputf = '<ul>'.$outputf.'</ul>';
		}
	}
	return $outputf;
}

/* load plugins from local repository */
function load_plugins()
{
	$outputf = '<div id="error">No plugins found.</div>';
	if ($handle = opendir('plugins')) {
		 
		$tmp = ''; $count = 0;
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && is_dir('plugins/'.$entry)) {
				$tmp .= '<li class="plugin" id="'.$entry.'">'.$entry.'<span><input class="man_scan" type="button" id="'.$entry.'" value="Scan plugin" /></span></li>';
				$count++;
				//todo get clean name of the plugin
			}
		}
		closedir($handle);
		 
		if($count)
			$outputf = '<div id="success">Successfully loaded '.$count.' plugins.</div><ul>'.$tmp.'</ul>';
	}
	return $outputf;
}

/* rips, do your best */
function scan_plugin($plugin)
{
	$outputf = '<div id="error">Plugin not found.</div>';
	/* some sort of protection for lfi shits (WTF!?) */
	$plugin = trim($plugin);
	$plugin = str_replace(array('.','/','&','%','\\','*','#'),'',$plugin);
	 
	if(!empty($plugin) && is_dir('plugins/'.$plugin))
	{
		$params = 'loc=../plugins/'.$plugin.'&subdirs=1&verbosity=4&vector=server&treestyle=1&stylesheet=ayti&ignore_warning=1';
		/* hack the rips output identation. >:) */		
		$response = str_replace(array("\t","\r","\n","  "),"",curl_request('rips/main.php', $params));
		 
		preg_match_all('/<div id="stats" class="stats">(.*?)<\/table><\/div>/is', $response, $stats);
		$outputf = $stats[0][0]; /* honey */
		
		/* lets clear this shit */
		$outputf = preg_replace(array('/onmouseover="(.*?)"/is', //remove junk
					    		      '/onmouseout="(.*?)"/is',//remove junk
							          '/onclick="(.*?)"/is',//remove junk
									  '/style="cursor:pointer;"/is',//remove junk and update style
									  '/<td rowspan="4" ><div class="diagram"><canvas id="diagram" width="80" height="70"><\/canvas><\/div><\/td>/is',//remove junk
									  '/<table class="textcolor" width="100%"><tr><td nowrap/is',//change 3 tables to width 50%
									  '/<table class="textcolor" width="50%"><tr><td nowrap width="160">Scan time/is', //change last table to width 100%
									  '/title="show only vulnerabilities of this category"/is', //remove junk
									  '/<hr \/><table class="textcolor" width="50%"/is', //remove hr between 2 tables with width 50%
									  '/<table class="textcolor" width="50%">/is', //float left for both tables
									  '/<hr \/>/is',//append clear fix-ul,
									  '/Sensitive sinks:/is',
									  '/Considered sinks:/is',
									  '/Include success:/is',
									  '/<td width="160">Info:<\/td>/is',
									  '/<td nowrap width="160">Scan time:<\/td>/is',
									  '/style="font-size:22px;padding-left:10px"/is'
									  ),
									  array('',
											'',
											'',
											'style="font-weight:bold; width:200px;"',
											'',
											'<table class="textcolor" width="50%"><tr><td nowrap',
											'<table class="textcolor" width="100%"><tr><td nowrap width="160">Scan time',
											'',
											'<table class="textcolor" width="50%"',
											'<table class="textcolor" width="50%" style="float:left;">',
											'<hr class="clear"/>',
											'<strong>Sensitive sinks:</strong>',
											'<strong>Considered sinks:</strong>',
											'<strong>Include success:</strong>',
											'<td style="width:200px;"><strong>Info:</strong></td>',
											'<td style="width:200px;"><strong>Scan time:</strong></td>',
											'style="font-size:19px;color:#4581B9;"'
									  ),$outputf);
 	} 
	return $outputf;
}

/* bla bla, google it */
function curl_request($url, $params)
{ 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/'.$url);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
	$result = curl_exec ($ch);
	curl_close ($ch);  
	return $result;
}
?>