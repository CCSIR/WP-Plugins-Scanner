/** 

Wordpress Plugins Scanner
	by Andrei Avadanei (andrei.avadanei@ccsir.ro) 
			

Copyright (C) 2013 Cyber Security Research Center from Romania

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.		

**/
var SCANNED = 0;
var PLUGIN_TO_SCAN = null;
$(document).ready(function() {

	$("#download").click(function() {
		$('#download_loading').show();
		var link = escape($("#path").val());
		$.ajax({
            type: "GET",
            url: "scanner.php",
            data: "type=download&link=" + link,
            success: function(data){
			   $('#download_loading').hide(); 
               $('#plugins').html(data); 
			   $('#scan').show();
			   $('.plugin').hover(function() { $(this).find('.man_scan').show(); }, 
								  function() { $(this).find('.man_scan').hide(); } )
					       .find('.man_scan').click(function() {PLUGIN_TO_SCAN = $(this).attr('id'); $('#scan').click(); } );
			}
		});
		
		SCANNED = 0;
		return false;
	});
	$("#load").click(function() {
		$('#load_loading').show();
		$.ajax({
            type: "GET",
            url: "scanner.php",
            data: "type=load",
            success: function(data){ 
			   $('#load_loading').hide();
               $('#plugins').html(data); 
			   $('#scan').show();
			   $('.plugin').hover(function() { $(this).find('.man_scan').show(); }, 
								  function() { $(this).find('.man_scan').hide(); } )
					       .find('.man_scan').click(function() {PLUGIN_TO_SCAN = $(this).attr('id'); $('#scan').click(); } );
			}
		});
		
		SCANNED = 0;
			
		return false;
	}); 
	$("#scan").click(function() {
		if(SCANNED == 0)
		{
			$('#scan_loading').show();
			var count=0;
			$(".plugin").each(function() { 
				if(PLUGIN_TO_SCAN == null || PLUGIN_TO_SCAN == $(this).attr('id'))
			    {
					count++;
					var plugin = $(this);
					$.ajax({
						type: "GET",
						url: "scanner.php",
						data: "type=scan&plugin=" + $(plugin).attr('id'),
						success: function(data){ 
							count--;
							if(count == 0) 
							{
								$('#scan_loading').hide();
								if(PLUGIN_TO_SCAN == null) SCANNED = 1;
							}
							$(plugin).html($(plugin).html() + '<div class="plugin_scan_result">' + data + '</div>');
							$(plugin).find('.man_scan')
									.attr('value','Details')
									.click(function() { 
										var proxy = document.location.toString().replace('index.php','');  
										window.open(proxy + '/rips/pwppstr.php?plugin=' + $(plugin).attr('id'), '_blank'); 
									} );
									
							PLUGIN_TO_SCAN = null;
						}
					});
				}
			});			
		}
		return false;
	});
	$("#clear").click(function() {
		$("#plugins").html('');
		$('#scan').hide();
	});
});
