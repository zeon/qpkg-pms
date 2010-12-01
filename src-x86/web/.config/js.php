<?php
/************************************************************************

	PMS Admin Web GUI

	**********************************************
	- File:			js.php
	- Date:			Sep 8th, 2010
	- Version:		1.0
	- Author:		AdNovea (Laurent)
	- Modified by:	Andy Chuo (QNAPAndy)
	- Description:  PMS Admin main page javascript

*************************************************************************/
	
// Open JS section		
	$jssource = "
		<script type='text/javascript'>
			// Debug console files
			docFile0 = '".PMS_DEBUG_LOG."';
			docFile1 = '".PMS_DAILY_LOG."';
			stNoSelection = '"._ST_NOSELECTION."';
			var horizontalPadding = 30;
			var verticalPadding = 30;
			var fontFileName; 
			
			// jQuery functions			
			jQuery.noConflict();
			jQuery(function() {

				jQuery('#dialog_console').dialog({
					title: 'Log Console',
					autoOpen: false,
					width: 800,
					height: 500,
					modal: true,
					resizable: true,
					autoResize: true,
					overlay: {
						opacity: 0.5,
						background: 'black'
					}
				}).width(800 - horizontalPadding).height(500 - verticalPadding);   
						
				jQuery('img.menu_class').click(function () {
					jQuery('ul.the_menu').slideToggle('medium');
				});

				jQuery('#basic-combo').sexyCombo();
				jQuery('#basic-combo').val('".PMS_SUB_CODEPAGE."');	
				
				//Ajax related
				jQuery.ajaxSetup ({
					cache: false
				}); 
				//Global events
				jQuery('.loading').bind('ajaxSend', function(){
					$(this).show();
				}).bind('ajaxComplete', function(){
					$(this).hide();
				});

				var ajax_load = \"<div class='loading' alt='loading...' />\"; 
				var loadUrl = '.config/check.php'; 
				jQuery('#start_pms').click(function(){
					jQuery('.result').html(ajax_load).load(loadUrl, 'lid=English&restart=1');
				}); 	
				jQuery('#stop_pms').click(function(){
					jQuery('.result').html(ajax_load).load(loadUrl, 'lid=English&restart=2');
				}); 
				jQuery('#restart_pms').click(function(){
					jQuery('.result').html(ajax_load).load(loadUrl, 'lid=English&restart=3');
				}); 
   				jQuery('#apply_settings').click(function(){
					jQuery('.result2').html(ajax_load).load(loadUrl, 'lid=English&apply_settings=1', function(){
						jQuery('.result2').html('Done!');													jQuery('#current_settings1').html(jQuery('#basic-combo option:selected').val());
						jQuery('#current_settings2').html(fontFileName);
					});
				});
				jQuery('#reset_default2').click(function(){
					jQuery('.result2').html(ajax_load).load(loadUrl, 'lid=English&reset_default=1', function(){
						jQuery('.result').html('Done');
						jQuery('#current_settings1').html('cp1252');
						jQuery('#current_settings2').html('Vera.ttf');
					});
				}); 
				jQuery('#truncate_log').click(function(){
					jQuery('.result3').html(ajax_load).load(loadUrl, 'lid=English&truncate=1', function(){
						jQuery('.result3').html('Done');
					});
				}); 
				jQuery('#start_log').click(function(){
					jQuery('.result3').html(ajax_load).load(loadUrl, 'lid=English&logging=1', function(){
						jQuery('.result3').html('Applied');
						jQuery('#current_settings3').html('Logging is On.');
					});
				}); 
				jQuery('#stop_log').click(function(){
					jQuery('.result3').html(ajax_load).load(loadUrl, 'lid=English&logging=2', function(){
						jQuery('.result3').html('Applied');
						jQuery('#current_settings3').html('Logging is Off.');
					});
				}); 
				jQuery(function() {
					var icons = {
						header: 'ui-icon-circle-arrow-e',
						headerSelected: 'ui-icon-circle-arrow-s'
					};

					jQuery( '#accordion' ).accordion({
						icons: icons,
						//event: 'mouseover',
						animated: 'easeInOutBack',
						active: false,
						autoHeight: false,
						collapsible: true
					});
				});		
				
				jQuery('.debuglog').click(function(e) {
					e.preventDefault();
					jQuery('#console').html(ajax_load).load('".PMS_DEBUG_LOG."', function(data){							
						jQuery('#console').html(data.replace(/\\n/g, '<br/>'));
					});
					jQuery('#dialog_console').dialog('open');
				});
				
				jQuery('.dailylog').click(function(e) {
					e.preventDefault();
					jQuery('#console').html(ajax_load).load('".PMS_DAILY_LOG."', function(data){							
						jQuery('#console').html(data.replace(/\\n/g, '<br/>'));
					});
					jQuery('#dialog_console').dialog('open');
				});
				
				function createUploader(){         
					var uploader = new qq.FileUploader({
						element: document.getElementById('file-uploader-demo1'),
						
						// url of the server-side upload script, should be on the same domain
						action: '/pms/.config/upload.php',
						
						// additional data to send, name-value pairs
						params: {
							 param1: jQuery('#basic-combo option:selected').val()
						},

						// validation    
						// ex. ['jpg', 'jpeg', 'png', 'gif'] or []
						allowedExtensions: ['ttf'],

						// each file size limit in bytes
						// this option isn't supported in all browsers
						sizeLimit: 100 * 1024 * 1024, // max size   
						//minSizeLimit: 0, // min size

						// set to true to output server response to console
						debug: true,

						// events         
						// you can return false to abort submit
						onSubmit: function(id, fileName){
							uploader.setParams({
							   param1: jQuery('#basic-combo option:selected').val()
							});
							
						},
						//onProgress: function(id, fileName, loaded, total){},
						onComplete: function(id, fileName, responseJSON){
							fontFileName = fileName;
						},
						//onCancel: function(id, fileName){},

						messages: {
							typeError: '{file} has invalid extension. Only [.{extensions}] are allowed.',
							sizeError: '{file} is too large, maximum file size is {sizeLimit}.',
							minSizeError: '{file} is too small, minimum file size is {minSizeLimit}.',
							emptyError: '{file} is empty, please select files again without it.',
							onLeave: 'The files are being uploaded, if you leave now the upload will be cancelled.'       
						},
						showMessage: function(message){ alert(message); }        
					});  
				}

				// in your app create uploader as soon as the DOM is ready
				// don't wait for the window to load  
				window.onload = createUploader; 

			}); 
		</script>";
?>
