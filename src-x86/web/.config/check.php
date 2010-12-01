<?php
/************************************************************************

	Status Checking

	**********************************************
	- File:			check.php
	- Date:			Sep 8th, 2010
	- Version:		1.0
	- Author:		AdNovea (Laurent)
	- Modified by:	Andy Chuo (QNAPAndy)
	- Description:  Start/Stop/Check the PMS 

*************************************************************************/
	include('conf.php');

	$restart = $_GET['restart'];
	$apply_settings = $_GET['apply_settings'];
	$reset_default = $_GET['reset_default'];
	$truncate_log = $_GET['truncate'];
	$logging = $_GET['logging'];
	
	// Stop or Restart application (doesn't always work !!!)
	if ($restart == 1) exec(WRITE_HTTP."pms=start".WRITE_LOG);
	if ($restart == 2) exec(WRITE_HTTP."pms=stop".WRITE_LOG);
	if ($restart == 3) exec(WRITE_HTTP."pms=restart".WRITE_LOG);
	if ($apply_settings == 1) exec(WRITE_HTTP."pms=restart".WRITE_LOG); 
	if ($reset_default == 1) exec(WRITE_HTTP."reset_default=1".WRITE_LOG); 
	if ($truncate_log == 1) exec(WRITE_HTTP."truncate=1".WRITE_LOG);
	if ($logging == 1) exec(WRITE_HTTP."logging=1".WRITE_LOG);
	if ($logging == 2) exec(WRITE_HTTP."logging=2".WRITE_LOG);
	if (isset($_GET['restart'])) sleep(7);

// Check if server.php has been created and if XMail is running
	if($restart != 0) {
		if (file_exists(PMS_PID_FILE)) 
			$pms_status = RUNNING; 
		else 
			$pms_status = STOPPED;
		
		echo $pms_status;
	}
	
	define("LOGGING2", exec(CMD_GREP." \"<level>OFF</level>\" ".PMS_ROOT."logback.xml | /bin/cut -d \">\" -f 2 | /bin/cut -d \"<\" -f 1"));
	
	if (LOGGING2 == "ALL") 
		define("PMS_LOGGING", "On");
	else 
		define("PMS_LOGGING", "Off");
?>
 
