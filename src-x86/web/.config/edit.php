<?php
	/************************************************************************

	PMS config editor
	
	**********************************************
	- File:			edit.php
	- Date:			Sep 8th, 2010
	- Version:		1.0
	- Author:		AdNovea (Laurent)
	- Modified by:	Andy Chuo (QNAPAndy)
	- Description:  PMS config editor

*************************************************************************/

	require("conf.php");

	$viewconfig = $_POST['viewconfig'];
	$save_it = $_POST['save_it'];
	$cfg = $_GET['cfg'];
	$file = $_GET['file'];
	$action = (isset($_GET['action'])?$_GET['action']:"");

	echo "
	<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset="._CHARSET."'>
		<link href='".$URL_site."css/jquery-ui.css' rel='stylesheet' type='text/css' >
	</head>
	<body>";
		
	load_language("edit_file");	// Load language strings for configuration file edition
	set_js_vars();
	echo "<script type=\"text/javascript\" language=\"javascript\">editor=true;</script>";
	
	if (CHROOTED) $chrooted = QPKG_ROOTFS; else $chrooted = ""; 

	switch ($cfg) {
		case "pms_conf": $loadcontent = PMS_ROOT.$file; break;
		case "web_conf": $loadcontent = PMS_ROOT.$file; break;
		case "codecs_conf": $loadcontent= ROUNDCUBE_DIR.$file; break;
		//default: die('System error');
	}

	if ($save_it == "1") {
		$savecontent = stripslashes($viewconfig);
		$fp = @fopen($loadcontent, "w");
		if ($fp) {
			fwrite($fp, $savecontent);
			fclose($fp);
		} else $msg = _EDIT_CANNOT_SAVE." &nbsp; ";
	}
	if (!file_exists($loadcontent)) {
		$action = "";
		$loadcontent = _EDIT_NOFILE;
	} else {
		$fp = @fopen($loadcontent, "r");
		$loadcontent = fread($fp, filesize($loadcontent));
		$loadcontent = htmlspecialchars($loadcontent);
		fclose($fp);
	}
	
	$msg = "$file";
	echo "
		<style type='text/css'>
			textarea {
				width: 100%;
				height: 400px;
				font: 12px Tahoma, Courier New;
				color: #dddddd;
				background-color: #525252;			
			}
		
			#viewconfig {
				width: 99%; 
				height: 380px;	
				font-variant: normal; 
				font: normal 12px Courier New; 
				line-height: normal; 
				font-size-adjust: none; 
				font-stretch: normal; 
			}
			
			.button {
				padding: 2px 5px 1px 5px;
				font-size: 11px;
				color: #2F3A55;
				border: 1px solid #DCDCC6;
				background-color: #aaa;
				
			}
		</style>
		<form style='position: absolute; top: 0px; left: 0px;' method=post action=\"".$_SERVER['PHP_SELF']."?&amp;lid=$lid&amp;cfg=$cfg&amp;file=$file&amp;action=$action\">
			<div id='dialog_console' class='ui-dialog ui-widget ui-widget-content ui-corner-all  ui-draggable' style='left: 0px; right: 0pt; width: 791px;'>
				<div class='ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix' style='height: 25px; width: 787px; margin: 0 0 10px; padding: 4px 0 0;'>
					<span class='ui-dialog-title' style='padding: 0 0 0 10px; font-family: Trebuchet MS,Tahoma,Verdana,Arial,sans-serif; font-size: 13px;'>$msg</span>";
				if ($action == "save")
					echo "<input type='submit' value='"._BT_SAVE."' name='save_file' class='button' style='float: right; height:15px; font-size:11px; font-weight:normal; height:21px; width: 50px; margin-right:60px;'>
				</div>
				<center>
					<textarea name=\"viewconfig\" id=\"viewconfig\">$loadcontent</textarea>
					<input type=\"hidden\" name=\"save_it\" value=\"1\">
				</center>
			</div>
		</form>";
	include('footer.php');
	
?>
