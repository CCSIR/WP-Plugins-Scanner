<?php 
/** 

Wordpress Plugins Scanner
	by Andrei Avadanei (andrei.avadanei@ccsir.ro) 
			

Copyright (C) 2013 Cyber Security Research Center from Romania

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.		

**/
define('IN_WP_PLUGIN_SCANNER', TRUE);
include_once('installer.php'); ?>
<html>
<head>
<script type="text/javascript" src="static/jquery.js"></script>
<script type="text/javascript" src="static/scanner.js"></script>
<title>Wordpress Plugins Scanner</title>
<link type="text/css" rel="stylesheet" href="static/style.css" media="all" />
</head>
<body>
	<div id="body">
		<div id="header">
			<a href="https://github.com/CCSIR/WP-Plugins-Scanner" target="_blank"><h3 alt="Wordpress Plugins Scanner"><div>Plugins Scanner</div></h3></a>
		</div>
		<div id="content">
			<div id="form_scan">
				<span><input type="button" id="load" value="Load plugins" /></span>
				<span><input type="button" id="clear" value="Clear" /></span>
				<span><input type="button" id="subscribe" value="Subscribe" /></span>
				<span class="loading" id="load_loading" style="display:none;" ></span>
				<div>
					URL: <input type="text" id="path" value="http://wordpress.org/extend/plugins/search.php?q=test&sort=popular" /> 
				    <span><input type="button" id="download" value="Download plugins" /> </span>
					<span class="loading" id="download_loading" style="display:none;" ></span>						
				</div>
			</div>
			<div id="plugins">
			</div>
			<div>
				<input type="button" id="scan" value="Scan plugins" style="display:none" />
				<span class="loading" id="scan_loading" style="display:none;" ></span>
			</div>
		</div>
		<div id="footer">
			<span id="footer-text">Copyright &copy; 2013 <a href="http://ccsir.ro" target="_blank">Cyber Security Research Center from Romania</a>. All rights reserved. <br />Powered by OWASP RIPS.</span>
		</div>
	</div>
</body>
</html>
