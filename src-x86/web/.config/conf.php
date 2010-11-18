<?php
/************************************************************************

	Config file

	**********************************************
	- File:			conf.php
	- Date:			Sep 8th, 2010
	- Version:		1.0
	- Author:		AdNovea (Laurent)
	- Modified by:	Andy Chuo (QNAPAndy)
	- Description:  PMS Admin main config & defines


*************************************************************************/

	$debugging = true;

// Common system variables
	define("_APPS_NAME",		"PMS Admin Center - QNAP PS3 Media Server");
	define("_COPYRIGHTS",		"PMS Admin Center v0.1 by QNAPAndy.<br>All rights reserved for its respective owners.");
	define("PWD_MIN_LENGHT",	5);			// Minimum number of password characters
	define("DOC_TYPE",			"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1-transitional.dtd\">");
	$admin_min_pwd = 5;						// Password minimum of characters
	$lb_size = "width=800, height=450";		// Slimbox dimensions
	define("ARCHIVE_SUFFIX",	"tgz");		// XMail archive files suffix
	define("ARCHIVE_MAX_NB",	5);			// Maximum number of XMail archive files (0= illimited)
	define("DEFAULT_PORT_XMAIL",6017);		// Default XMAIL port
	define("DEFAULT_PORT_SMTP",	50025);		// Default SMTP port
	define("DEFAULT_PORT_POP",	50110);		// Default POP port
	define("DEFAULT_PORT_IMAP",	143);		// Default IMAP port
	define("DEFAULT_PORT_FINGER",50079);	// Default FINGER port
	define("DEFAULT_PORT_MYSQL",3306);		// Default MYSQL port

	define("CMD_AWK",			"/bin/awk");
	define("CMD_CUT",			"/bin/cut");
	define("CMD_GETCFG",		"/sbin/getcfg");
	define("CMD_GREP",			"/bin/grep");
	define("CMD_HOSTNAME",		"/bin/hostname");
	define("CMD_IFCONFIG",		"/sbin/ifconfig");
	define("CMD_PIDOF",			"/bin/pidof");
	define("CMD_PS",			"/bin/ps");
	define("CMD_TAR",			"/bin/tar");
	define("CMD_UNAME",			"/bin/uname");
	define("CMD_WGET",			"/usr/bin/wget");
	define("HTPASSWD",			"/usr/local/apache/bin/htpasswd");
	define("CRONTAB_FILE",		"/etc/config/crontab");
	define("CMD_DATE",				"/bin/date");
	
// Get QNAP server type, name and base location (ATTENTION: Do not change the cmds order below !!!)
	define("BASE",				exec(CMD_GETCFG." Public path -f /etc/config/smb.conf | ".CMD_CUT." -c 1-16"));
	if (BASE == "") die("SYSTEM ERROR: Cannot find QNAP server base location!<br>Returns:".BASE);
	define("SYS_MODEL",			exec(CMD_GETCFG." system model", $output));
	define("KERNEL",			exec(CMD_UNAME." -mr | ".CMD_CUT." -d '-'  -f 1 | ".CMD_CUT." -d ' '  -f 1", $output));
	if (KERNEL == "2.6.12.6")	define("CHROOTED",true); else define("CHROOTED",false);
	define("QNAP_NAME",			exec(CMD_HOSTNAME, $output));
	
	$output = null;	exec(CMD_IFCONFIG." eth0 | ".CMD_AWK." '/addr:/{print $2}' | ".CMD_CUT." -f2 -d:", $output);
	if (trim($output[0])!="") define("QNAP_IP", $output[0]);
	else {
		$output = null;	exec(CMD_IFCONFIG." bond0 | ".CMD_AWK." '/addr:/{print $2}' | ".CMD_CUT." -f2 -d:", $output);
		if (trim($output[0])!="") define("QNAP_IP", $output[0]);
		else {
			$output = null; exec(CMD_IFCONFIG." eth1 | ".CMD_AWK." '/addr:/{print $2}' | ".CMD_CUT." -f2 -d:", $output);
			if (trim($output[0])!="") define("QNAP_IP", $output[0]);
			else {
				$output = null; exec(CMD_IFCONFIG." bond1 | ".CMD_AWK." '/addr:/{print $2}' | ".CMD_CUT." -f2 -d:", $output);
				if (trim($output[0])!="") define("QNAP_IP", $output[0]);
				else define("QNAP_IP", "127.0.0.1");
			}
		}
	}
	
	define("QNAP_HTTPD_PORT",		exec(CMD_GETCFG." SYSTEM \"Web Access Port\" -f /etc/config/uLinux.conf"));	// Default is 8080
	define("QNAP_WEB_PORT",			exec(CMD_GETCFG." QWEB Port -u -f /etc/config/uLinux.conf"));				// Default is 80
	define("QNAP_WEB_FOLDER", 		exec(CMD_GETCFG." SHARE_DEF defWeb -d Qweb -f /etc/config/def_share.info"));

	// App version, etc
	define("PMS_VERSION",			"v1.20.409");
	define("PMSENCODER_VERSION",	"v1.1.0");
	define("MOVIEINFO_VERSION",		"build 14122009");
	define("FFMPEG_VERSION",		"git-9e981c8");
	define("MPLAYER_VERSION",		"SVN-r32037-4.1.3");
	define("MENCODER_VERSION",		"SVN-r32037-4.1.3");
	define("TSMUXER_VERSION",		"v1.10.6");
	
	// Paths, Mail server files and scripts paths
	define("QPKG_ROOTFS",			"/mnt/HDA_ROOT/rootfs_2_3_6/");
	define("PMS_ROOT",				BASE.".qpkg/PMS/");
	define("QWEB_DIR",				"/share/".QNAP_WEB_FOLDER."/");
	define("PMS_DIR",				QWEB_DIR."pms/");
	define("LOGS_DIR",				"logs/");					// Used for log files and cron temp files
	
	define("HTPASSWD_FILE",			"/root/.htpasswd");
	define("PMS_PID_FILE",			PMS_ROOT."pms.pid");

	define("WRITE_HTTP",			CMD_WGET." \"http://127.0.0.1:".QNAP_HTTPD_PORT."/pms.cgi?");
	
	if ($debugging)
		define("WRITE_LOG",		"\" -O ".PMS_DIR.LOGS_DIR."debug.log");
	else 
		define("WRITE_LOG",		"\"");		
	
	define("PMS_DEBUG_LOG",			"logs/pms_debug.log");	
	define("PMS_DEBUG_LOG_NAME",	"Debug log");	
	define("PMS_DAILY_LOG",			"logs/pms-".exec(CMD_DATE." \+\%F").".log");
	define("PMS_DAILY_LOG_NAME",	"Daily log");
	
	if (! file_exists(PMS_DIR.PMS_DAILY_LOG)) exec(WRITE_HTTP."pms=restart".WRITE_LOG);	
	if (! file_exists(PMS_DIR.PMS_DEBUG_LOG)) exec(WRITE_HTTP."pms=restart".WRITE_LOG);	
	
	// Execute a bash cmd
	function bash($stt, $debug=false) {
		$output = null; $result = exec($stt, $output);
		if ($debug) foreach ($output as $line) echo "==>$line<br>";
		return $result;
	}

// Prepare for Internationalization
	$lid = (isset($_GET['lid']) ? $_GET['lid'] : "English");
	$lid = ($lid!=""?$lid:"en_US");
	$rootfolder = dirname(__FILE__)."/../";
	include($rootfolder."langs/languages.php");

// Add language droplist 
	function add_language_list($urlfile) {
		global $languages, $innerBanner, $lid;
		if (isset($_GET['first'])) $first = "&first=".$_GET['first'];
		$innerBanner = "
		<select onchange=\"javascript:location='$urlfile.php?lid='+this.value+'$first';\">";	
		foreach($languages as $key => $lang) {
			$selected = ($lid == $key)?" selected":"";
			$innerBanner .= "<option value=\"$key\"$selected>".$lang["name"]."</option>\r";
		}
		$innerBanner .= "</select>";
	}

// Load language strings
	function load_language($section) { 
		global $lid, $rootfolder, $languages;
		
		$lg = file($rootfolder."langs/".$languages[$lid]['path'].".txt");
		$getstring = false;
		while (list($line,$value) = each($lg)) {
			if($value[0] == "[") {
				if (trim($value) == "[$section]") {
					$getstring = true; 
				} else {
					if ($getstring) break;	// End of section reached
					$getstring = false;
				}
			}
			if ($getstring && strpos(";#",$value[0]) === false && ($pos = strpos($value,"=")) != 0 && trim($value) != "") {
				$varname  = trim(substr($value,0,$pos));
				$varvalue = trim(substr($value,$pos+1));
				define("_".strtoupper($varname),$varvalue);
			}
		}
	}
	load_language("common");	// Load common language strings

// Set javascript variables
	function set_js_vars() { 
		echo "
		<script type='text/javascript'>
			<!--
			var bt_close='"._BT_CLOSE."';	// Internationalization of the Slimbox close button
			//-->
		</script>";
	}

// Get XDove base URL
	$root = $_SERVER['PHP_SELF']; 	
	$root = str_replace(".config/","",$root);
	$root = str_replace("docs/","",$root);
	$root = str_replace("auto_install/","",$root);
	$root = str_replace(basename($root),"",$root);
	$URL_site = "$root";

?>
