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

	define("RUNNING","<img border='0' src='images/black_play.png' alt=''>");
	define("STOPPED","<img border='0' src='images/black_stop.png' alt=''>");

// Stop or Restart application (doesn't always work !!!)
	if ($_GET['restart'] == 1) exec(WRITE_HTTP."pms=start".WRITE_LOG);
	if ($_GET['restart'] == 2) exec(WRITE_HTTP."pms=stop".WRITE_LOG);
	if ($_GET['truncate_log'] == 1) exec(WRITE_HTTP."truncate=1".WRITE_LOG);
	if (isset($_GET['restart'])) sleep(7);

// Check if server.php has been created and if XMail is running
	if (file_exists(PMS_PID_FILE)) $installed_pms = RUNNING; else $installed_pms = STOPPED;
?>
 
