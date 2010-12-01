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
	//include('.config/check.php');

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
					<div id='banner'><img src='images/banner.png' alt='Banner'></div>				
					<div id='accordion'>
						<h3>
							<a href='#'><b>&nbsp;"._MENU_APP_STARTER."</b></a>
						</h3>
						<div>
							<div class='cssParsedBox' style='height: 120px;'>
								<div class='result'>$pms_status</div>
								<div>
									<div style='float:left; width: 33%; text-align: center;'>
										<a id='start_pms'><img src='images/start.png' alt='start pms' width='40'></a>
									</div>
									<div style='float:left; width: 33%; text-align: center;'>
										<a id='stop_pms'><img border='0' src='images/stop.png' alt='stop pms' width='40'></a>
									</div>
									<div style='float:right; width: 33%; text-align: center;'>
										<a id='restart_pms'><img border='0' src='images/restart.png' alt='restart pms' width='40'></a>
									</div>
								</div>
								<div>
									<div style='float:left; width: 33%; text-align: center;'>
										<span>"._MENU_START_PMS."</span>
									</div>
									<div style='float:left; width: 33%; text-align: center;'>
										<span>"._MENU_STOP_PMS."</span>
									</div>
									<div style='float:right; width: 33%; text-align: center;'>
										<span>"._MENU_RESTART_PMS."</span>
									</div>
								</div>
							</div>							
						</div>";											
						echo "
						<h3>
							<a href='#'><b>&nbsp;"._MENU_CONFIG_EDITOR."</b></a>
						</h3>
						<div>
							<div class='cssParsedBox' style='height: 60px;'>
								<div>
									<div style='float:left; width: 33%; text-align: center;'>
										<a href='.config/edit.php?lid=English&amp;cfg=pms_conf&amp;file=PMS.conf&amp;action=save' rev='width=800, height=450' rel='lightbox[roundcube-conf1]'><img border='0' src='images/document_edit.png' alt='' width='40'></a><br>
									</div>
									<div style='float:left; width: 33%; text-align: center;'>
										<a href='.config/edit.php?lid=English&amp;cfg=pms_conf&amp;file=WEB.conf&amp;action=save' rev='width=800, height=450' rel='lightbox[roundcube-conf2]'><img border='0' src='images/document_edit.png' alt='' width='40'></a><br>
									</div>
									<div style='float:right; width: 33%; text-align: center;'>
										<a href='.config/edit.php?lid=English&amp;cfg=pms_conf&amp;file=plugins/MOVIEINFO.conf&amp;action=save' rev='width=800, height=450' rel='lightbox[roundcube-conf3]'><img border='0' src='images/document_edit.png' alt=''width='40'></a><br>
									</div>
								</div>
								<div>
									<div style='float:left; width: 33%; text-align: center;'>
										<span>"._MENU_PMS_CONF."</span>
									</div>
									<div style='float:left; width: 33%; text-align: center;'>
										<span>"._MENU_WEB_CONF."</span>
									</div>
									<div style='float:right; width: 33%; text-align: center;'>
										<span>"._MENU_MOVIEINFO_CONF."</span>
									</div>
								</div>
							</div>							
						</div>";
						echo "
						
						<h3>
							<a href='#'><b>&nbsp;"._MENU_SUBTITLE_FONT_CHANGER."</b></a>
						</h3>
						<div>
							<div class='cssParsedBox' style='height: 300px;'>
								<div style='padding: 5px 0; height: 40px;'>
									<label for='basic-combo'>1. Choose a subtitle codepage:</label><br>
									<select id='basic-combo' name='basic-combo'  size='1'>
										<option value='cp1250'>cp1250 (Windows - Eastern Europe)</option>
										<option value='cp1251'>cp1251 (Windows - Cyrillic)</option>
										<option value='cp1252'>cp1252 (Windows - Western Europe)</option>
										<option value='cp1253'>cp1253 (Windows - Greek)</option>
										<option value='cp1254'>cp1254 (Windows - Turkish)</option>
										<option value='cp1255'>cp1255 (Windows - Hebrew)</option>
										<option value='cp1256'>cp1256 (Windows - Arabic)</option>
										<option value='cp1257'>cp1257 (Windows - Baltic)</option>
										<option value='cp1258'>cp1258 (Windows - Vietnamese)</option>
										<option value='ISO-8859-1'>ISO-8859-1 (Western Europe)</option>
										<option value='ISO-8859-2'>ISO-8859-2 (Western and Central Europe)</option>
										<option value='ISO-8859-3'>ISO-8859-3 (Western Europe and South European)</option>
										<option value='ISO-8859-4'>ISO-8859-4 (Western Europe and Baltic countries)</option>
										<option value='ISO-8859-5'>ISO-8859-5 (Cyrillic alphabet)</option>
										<option value='ISO-8859-6'>ISO-8859-6 (Arabic)</option>
										<option value='ISO-8859-7'>ISO-8859-7 (Greek)</option>
										<option value='ISO-8859-8'>ISO-8859-8 (Hebrew)</option>
										<option value='ISO-8859-9'>ISO-8859-9 (Western Europe with amended Turkish)</option>
										<option value='ISO-8859-10'>ISO-8859-10 (Western Europe with Nordic languages)</option>
										<option value='ISO-8859-11'>ISO-8859-11 (Thai)</option>
										<option value='ISO-8859-13'>ISO-8859-13 (Baltic languages plus Polish)</option>
										<option value='ISO-8859-14'>ISO-8859-14 (Celtic languages)</option>
										<option value='ISO-8859-15'>ISO-8859-15 (Added the Euro sign)</option>
										<option value='ISO-8859-16'>ISO-8859-16 (Central Europe languages)</option>
										<option value='cp932'>cp932 (Japanese)</option>
										<option value='cp936'>cp936 (Chinese)</option>
										<option value='cp949'>cp949 (Korean)</option>
										<option value='cp950'>cp950 (Big5, Taiwanese, Cantonese)</option>									
									</select>
								</div>
								<div style='padding: 10px 0 0 0;'>
									<label for='file-uploader-demo1'>2. Upload a font file (.ttf):
									</label><br>
									<div id='file-uploader-demo1'></div>	
									<span style='padding-left:15px;'>(or drag and drop the file here.)</span>
								</div>	
								<br>
								<div>
									3. &nbsp;<a id='apply_settings' class='ui-state-default ui-corner-all ui-state-hover'>
										<span class='ui-icon ui-icon-check'></span>Apply
									</a>
									&nbsp;&nbsp;or&nbsp;
									<a href='#' id='reset_default' class='ui-state-default ui-corner-all ui-state-hover'>
										<span class='ui-icon ui-icon-arrowreturnthick-1-w'></span>Reset to default
									</a>
									<div class='result2' style='float: right; padding: 0 30px 0 0; margin: 0;'></div><br>
								</div>	
								<div style='padding-top:30px; line-height:2em;'>
									<span style='color: #fff; font-weight: bold;'>Current Setting</span><br>
									<span class='ui-icon ui-icon-triangle-1-e' style='float:left;'></span>Codepage: 
									<span id='current_settings1' style='color: #fff;'>".PMS_SUB_CODEPAGE."</span><br>
									<span class='ui-icon ui-icon-triangle-1-e' style='float:left;'></span>Font: 
									<span id='current_settings2' style='color: #fff;'>".PMS_FONT_NAME."</span>
								</div>	
							</div>	
						</div>
						
						";
						echo "
						<h3>
							<a href='#'><b>&nbsp;"._MENU_LOG_VIEWER."</b></a>
						</h3>
						<div>
							<div class='cssParsedBox' style='height: 150px;'>
								<span id='current_settings3' style='color: #fff;'>Logging is ".PMS_LOGGING.".</span>
								<div style='padding: 10px 0 0 0; height: 40px; text-align: center;' class='result3'></div>
								<div>
									<div style='float:left; width: 20%; text-align: center;'>
										<a style='cursor: pointer;' class='debuglog'>
										<img border='0' src='images/console.png' width='40' alt='Console'><br>
										</a>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<a style='cursor: pointer;' class='dailylog'>
										<img border='0' src='images/console.png' width='40' alt='Console'><br>
										</a>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<a id='truncate_log'><img src='images/log.png' alt='truncate log' width='40'></a>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<a id='start_log'><img src='images/start.png' alt='start pms' width='40'></a>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<a id='stop_log'><img border='0' src='images/stop.png' alt='stop pms' width='40'></a>
									</div>
								</div>
								<div>
									<div style='float:left; width: 20%; text-align: center;'>
										<span>Debug Log</span>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<span>Daily Log</span>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<span>"._MENU_TRUNCATE_LOG."</span>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<span>Turn on Logging</span>
									</div>
									<div style='float:left; width: 20%; text-align: center;'>
										<span>Turn off Logging</span>
									</div>
								</div>
							</div>	
						</div>";
						echo "
						<h3>
							<a href='#'><b>&nbsp;"._MENU_APP_STATUS."</b></a>
						</h3>
						<div>
							<div class='cssParsedBox' style='height: 230px;'>
								<div style='padding: 5px 0;'>
									<table cellspacing='0' border='0' style='margin-left: 10px; width: 300px;'>
										<tr>
											<td width='60%' align='left'><span style='color: #ccc'><b>"._MENU_INFO_PMS_VERSION."</b></span></td>
											<td width='40%' align='left'><span>".PMS_VERSION."</span></td>	
										</tr>
										<tr><td colspan=2><span style='font-size: 7pt; color: #ccc'><br><b>&nbsp;Plugin installed</b></span></td></tr>
										<tr>
											<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_PMSENCODER_VERSION."</span></td>
											<td><span>".PMSENCODER_VERSION."</span></td>	
										</tr>
										<tr>
											<td><span>&nbsp;&nbsp;&nbsp;"._MENU_INFO_MOVIEINFO_VERSION."</span></td>
											<td><span>".MOVIEINFO_VERSION."</span></td>	
										</tr>
										<tr><td colspan=2><span style='font-size: 7pt; color: #ccc'><br><b>&nbsp;AV Encoder/Decoder/Muxer</b></span></td></tr>
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
										<tr><td colspan=2><span style='font-size: 7pt; color: #ccc'><br><b>&nbsp;Acknowledment</b></span></td></tr>
										<tr>
											<td colspan=2><span style='font-size: 10px;'>&nbsp;&nbsp;&nbsp;Special thanks to PowerMAC, innot and spoon.uk</span></td>
										</tr>										
									</table>
								</div>
							</div>								
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
		</div>
	</div>";
	include('.config/footer.php');

?>

