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
	$jssource =  "
		<script type='text/javascript'>";

// jQuery UI configuration
	$jssource .=  "
			// jQuery UI configuration			
			
			// Debug console files
			docFile0 = '".PMS_DEBUG_LOG."';
			docFile1 = '".PMS_DAILY_LOG."';
			stNoSelection = '\"._ST_NOSELECTION.\"';

			// jQuery functions			
			jQuery.noConflict();
			jQuery(function() {
				// Application Dialog box				
				jQuery('#dialog_console').dialog({
					autoOpen: false,
					stack: false,
					resizable: false,
					draggable: true,
					show: 'blind',
					width: 800,
					height: 470,
					modal: false,
					close: function(event, ui) { window.location = 'index.php?lid=$lid'; }
				});
				jQuery('img.menu_class').click(function () {
					jQuery('ul.the_menu').slideToggle('medium');
				});

			});	
			
			window.addEvent('domready', function() {

			//create our Accordion instance
			var myAccordion = new Accordion($('accordion'), 'h3.toggler', 'div.element', {
				opacity: true,
				openAll: false,
				show: [], //close everything
				alwaysHide: true,

				onActive: function(toggler, element){
					toggler.setStyle('color', '#41464D');
				},
				onBackground: function(toggler, element){
					toggler.setStyle('color', '#528CE0');
				}
				
			});
		}); 
		</script>";
?>