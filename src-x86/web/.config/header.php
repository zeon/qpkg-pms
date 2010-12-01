<?php
/************************************************************************

	- File:			header.php
	- Date:			Sep 8th, 2010
	- Version:		1.0
	- Author:		AdNovea (Laurent)
	- Modified by:	Andy Chuo (QNAPAndy)
	- Description:  PMS Admin main page header

*************************************************************************/
	include('js.php');
	
	echo DOC_TYPE."
	<html>
	<head>
		<meta charset='UTF-8' />
		<title>"._ADMIN_TITLE."</title>
		<meta http-equiv='Content-Type' content='text/html; charset="._CHARSET."'>
		
		<link href='".$URL_site."css/style.css' rel='stylesheet' type='text/css' />
		<link href='".$URL_site."css/jquery-ui.custom.css' rel='stylesheet' type='text/css' />
		
		<script type='text/javascript' src='".$URL_site."js/mootools.js'></script>
		<script type='text/javascript' src='".$URL_site."js/slimbox_ex.js'></script>
		<script type='text/javascript' src='".$URL_site."js/jquery.min.js'></script>
		<script type='text/javascript' src='".$URL_site."js/jquery-ui-1.8.6.custom.min.js'></script>	
		<script type='text/javascript' src='".$URL_site."js/jquery.curvycorners.packed.js'></script>	
		<script type='text/javascript' src='".$URL_site."js/pms-admin.js'></script>
		<script type='text/javascript' src='".$URL_site."js/jquery.sexy-combo.js'></script>
		<script type='text/javascript' src='".$URL_site."js/fileuploader.js'></script>";
		
	echo $jssource."
	</head>

	<body>";

?>
