<?php
/************************************************************************

	Main menu page

	**********************************************
	- File:			index.php
	- Date:			Sep 8th, 2010
	- Version:		1.0
	- Author:		AdNovea (Laurent)
	- Modified by:	Andy Chuo (QNAPAndy)
	- Description:  PMS Admin main page

*************************************************************************/

	require(".config/conf.php");
	add_language_list("index");
	include('.config/header.php');

	// Check applications status and Start/Stop servers
	include('.config/check.php');

	// Display sections
	load_language("menu");	// Load common language strings
	set_js_vars();
	
	// Top panel pulldown for PMS admin center
	echo "
	<div style='display: table; height: 800px; overflow: hidden; margin: auto;'>
		<div style='display: table-cell; vertical-align: middle;'>
			<div class='wrapper floatholder'>
				<div id='main'>";					
					echo "
					<div id='banner'><img border='0' src='images/banner.png'></div>
					<div id='iconimg'>$installed_pms</div>
					<div id='accordion'>
						<h3 class='toggler'>
							<table cellspacing='0' border='0'>
								<tr align='left'>
									<td align='left' style='padding-left: 15px;'><img border='0' src='images/arrow.gif' alt=''></td>
									<td width='80'>&nbsp;"._MENU_APP_STARTER."</td>
								</tr>
							</table>
						</h3>
						<div class='element'>
							<table cellspacing='0' border='0' style='margin-left: 10px; background-color: #333; width: 275px;' class='cssParsedBox'>
								<tr>
									<td valign='top' align='center'>						 
										<a href='?lid=$lid&amp;restart=1'><img border='0' src='images/start.png' alt='start pms' width='40'></a>
									</td>
									<td valign='top' align='center'>	
										<a href='?lid=$lid&amp;restart=2'><img border='0' src='images/stop.png' alt='stop pms' width='40'></a>
									</td>
								</tr>
								<tr>
									<td valign='top' align='center'>						 
										<span>"._MENU_START_PMS."</span>
									</td>
									<td valign='top' align='center'>	
										<span>"._MENU_STOP_PMS."</span>
									</td>
								</tr>
								<tr height='8'></tr>
							</table>
						</div>";											
						echo "
						<h3 class='toggler'>
							<table cellspacing='0' border='0'>
								<tr align='left'>
									<td align='left' style='padding-left: 15px;'><img border='0' src='images/arrow.gif' alt=''></td>
									<td width='190'>&nbsp;"._MENU_CONFIG_EDITOR."</td>
								</tr>
							</table>
						</h3>
						<div class='element'>
							<table cellspacing='0' border='0' style='margin-left: 10px; background-color: #333; width: 275px;' class='cssParsedBox'>
								<tr>
									<td align='center'>
										<a href='.config/edit.php?lid=$lid&amp;cfg=pms_conf&amp;file=PMS.conf&amp;action=save' rev='$lb_size' rel='lightbox[roundcube-conf1]'><img border='0' src='images/document_edit.png' alt='' width='40'></a><br>
									</td>
									<td></td>
									<td align='center'>
										<a href='.config/edit.php?lid=$lid&amp;cfg=pms_conf&amp;file=WEB.conf&amp;action=save' rev='$lb_size' rel='lightbox[roundcube-conf2]'><img border='0' src='images/document_edit.png' alt='' width='40'></a><br>
									</td>
									<td></td>
									<td align='center'>
										<a href='.config/edit.php?lid=$lid&amp;cfg=pms_conf&amp;file=plugins/MOVIEINFO.conf&amp;action=save' rev='$lb_size' rel='lightbox[roundcube-conf3]'><img border='0' src='images/document_edit.png' alt=''width='40'></a><br>
									</td>	
								</tr>
								<tr>
									<td align='center' width='89'>						 
										<span>"._MENU_PMS_CONF."</span>
									</td>
									<td></td>
									<td align='center' width='89'>	
										<span>"._MENU_WEB_CONF."</span>
									</td>
									<td></td>
									<td align='center' width='89'>	
										<span>"._MENU_MOVIEINFO_CONF."</span>
									</td>
									<td width='5'></td>
								</tr>
								<tr height='8'></tr>
							</table>
						</div>";
						echo "
						<h3 class='toggler'>
							<table cellspacing='0' border='0'>
								<tr align='left'>
									<td align='left' style='padding-left: 15px;'><img border='0' src='images/arrow.gif' alt=''></td>
									<td width='190'>&nbsp;"._MENU_LOG_VIEWER."</td>
								</tr>
							</table>
						</h3>
						<div class='element'>
							<table cellspacing='0' border='0' style='margin-left: 10px; background-color: #333; width: 275px;' class='cssParsedBox'>
								<tr>
									<td width='5'></td>	
									<td align='center' width='89'>
										<a style='cursor: pointer;' onclick=\"javascript: jQuery('#dialog_console').dialog('open'); TimerRun = true; TimerSet = false; loadFile(1);\">
											<img border='0' src='images/console.png' width='40'><br>
										</a>
									</td>										
									<td width='100'></td>		
								</tr>									
								<tr>
									<td width='5'></td>	
									<td align='center' width='89'>						 
										<span>"._MENU_LOG_CONSOLE."</span>
									</td>
									<td></td>

								</tr>
								<tr height='8'></tr>
							</table>
						</div>";
						echo "
						<h3 class='toggler'>
							<table cellspacing='0' border='0'>
								<tr align='left'>
									<td align='left' style='padding-left: 15px;'><img border='0' src='images/arrow.gif' alt=''></td>
									<td width='190'>&nbsp;"._MENU_APP_STATUS."</td>
								</tr>
							</table>
						</h3>
						<div class='element'>
							<table cellspacing='0' border='0' style='margin-left: 10px; background-color: #333; width: 275px;' class='cssParsedBox'>
									<tr>
										<td width='60%' align='left'><span style='color: #ccc'><b>"._MENU_INFO_PMS_VERSION."</b></span></td>
										<td width='40%' align='left'><span>".PMS_VERSION."</span></td>	
									</tr>
									<tr><td colspan=2><span style='font-size: 7pt; color: #ccc'><br /><b>&nbsp;Plugin installed</b></span></td></tr>
									<tr>
										<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_PMSENCODER_VERSION."</span></td>
										<td><span>".PMSENCODER_VERSION."</span></td>	
									</tr>
									<tr>
										<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_MOVIEINFO_VERSION."</span></td>
										<td><span>".MOVIEINFO_VERSION."</span></td>	
									</tr>
									<tr><td colspan=2><span style='font-size: 7pt; color: #ccc'><br /><b>&nbsp;AV Encoder/Decoder/Muxer</b></span></td></tr>
									<tr>
										<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_FFMPEG_VERSION."</span></td>
										<td><span>".FFMPEG_VERSION."</span></td>	
									</tr>
									<tr>
										<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_MPLAYER_VERSION."</span></td>
										<td><span>".MPLAYER_VERSION."</span></td>	
									</tr>
									<tr>
										<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_MENCODER_VERSION."</span></td>
										<td><span>".MENCODER_VERSION."</span></td>	
									</tr>
									<tr>
										<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_TSMUXER_VERSION."</span></td>
										<td><span>".TSMUXER_VERSION."</span></td>	
									</tr>									
									<tr><td colspan=2><span style='font-size: 7pt; color: #ccc'><br /><b>&nbsp;Acknowledment</b></span></td></tr>
									<tr>
										<td colspan=2><span style='font-size: 10px;'>&nbsp;&nbsp;&nbsp;Special thanks to PowerMAC, innot and spoon.uk</font></span></td>
									</tr>										
							</table>
						</div>
					</div> <!-- accordion -->						
				</div> <!-- main -->
				<div id='footer'><a href='docs/README' rev='$lb_size' rel='lightbox[xdove-gpl]'>"._COPYRIGHTS."</a></div>
			</div> <!-- wrapper floatholder -->
		</div>
	</div>
	<div id='dialog_console' title='Log Console'>
		<div class='contentdiv'>
			<div id='console' style='width: 765px; height: 380px;'></div>
			<form method='post' name='input' action='?lid=$lid&amp;truncate_log=1'>
				<small> 
					<input type='radio' name='logs' checked onClick=\"javascript: loadFile(1);\">".PMS_DEBUG_LOG_NAME."
					<input type='radio' name='logs' onClick=\"javascript: loadFile(2);\">".PMS_DAILY_LOG_NAME."
					<input class='button' type='submit' name='truncate_log' value='"._BT_OK."'>&nbsp;Truncate log
				</small>
			</form>
		</div>
	</div>";
	include('.config/footer.php');

?>

