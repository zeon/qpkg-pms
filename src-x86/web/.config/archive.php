<?php
/************************************************************************
	XDove is a GPL'ed software developed by

	 - Andy Chuo (QNAP) and Laurent (AdNovea)

	**********************************************
	- File:			archive.php
	- Date:			January 14th, 2009
	- Version:		1.2d
	- Description:  Archiving of the Mail data

*************************************************************************/

	require("./conf.php");
	add_language_list("index");
	require_once('./header.php');
	require_once(PHPXMAIL_DIR."class.xmail.php");
	set_js_vars();
	load_language("archive");

	$action = (isset($_GET['action'])?$_GET['action']:"");
	define("_BT_RETURN","<input type='button' class='button' onclick=\"javascript:location ='archive.php?lid=$lid&amp;action=$action';\" value='"._BT_BACK."'>");
	$delete = (isset($_GET['delete'])?$_GET['delete']:"");


// Check if MySQL is running
	$output = null; exec(CMD_PIDOF." mysqld", $output);
	if ($output[0] !="") $installed_mysql = RUNNING; else $installed_mysql = STOPPED;

// Delete the archives
	if ($delete != "") {
		if ( ($delete == "delall") && ($handle = opendir(ARCHIVE_DIR)) ) {
			while ( false !== ($file = readdir($handle)) ) {
				$fileExt = explode(".", $file);
				if ( $fileExt[1] == ARCHIVE_SUFFIX ) unlink(ARCHIVE_DIR.$file);
			}
			closedir($handle);
		} else unlink(ARCHIVE_DIR.$delete);
	}


// Save an archive
	if (isset($_POST['save'])) {
		echo "<p class='style1'><b>"._ARCHIVE_SAVE."</b></p><br>";
		$options = (isset($_POST['xmail'])?1:0) + (isset($_POST['pop'])?2:0)+ (isset($_POST['rc'])?4:0);
		if ($options == 0) exit("<br><center class='style3'>"._ARCHIVE_NOSELECT."<br><br>"._BT_RETURN."</center>");
		// Export RC database
		if (isset($_POST['rc'])) {
			if ($installed_mysql != RUNNING) exit("<br><center class='style3'>"._WARNING._ARCHIVE_NO_MYSQL."<br><br>"._BT_RETURN."</center>");
			$RoundCube = true;
			$line = file(PHPXMAIL_FILE);
			$tmp = explode("\t", $line[0]); $sqlpassword = xmdecrypt($tmp[4]);
			$db = @mysql_connect("localhost:".QNAP_MYSQL_PORT, "roundcube", $sqlpassword);
			if ($db) {
				mysql_select_db("roundcubemail", $db);
				if ($file=fopen(ROUNDCUBE_DIR.ROUNDCUBE_SQL,"w")) {
					$tables = mysql_query("SHOW TABLES FROM roundcubemail");
					while ($table = mysql_fetch_row($tables)) {
						$sql = "DELETE FROM ".$table[0].";\n";
						$table_query = mysql_query("SELECT * FROM `".$table[0]."`");
						while ($row = mysql_fetch_array($table_query)) {
							$sql .= "INSERT INTO ".$table[0]." VALUES(";
							for ($i=1; $i<=mysql_num_fields($table_query); $i++) 
								$sql .= ($i==1?"'":", '").mysql_real_escape_string($row[($i - 1)])."'";
							fwrite($file, "$sql);\n\n");
							$sql = NULL;
						}
					}
					fclose($file);
				} else $msg = _ARCHIVE_NO_DB;
				$transaction = mysql_close($db);
			} else $msg = _ARCHIVE_NO_DB;
		}
		if ($msg == "") $msg = _ARCHIVE_SAVE_DONE;
		exec(WRITE_HTTP."archive=save&options=$options&rotate=".ARCHIVE_MAX_NB.WRITE_LOG);
		exit("<br><center class='style3'>$msg<br><br>"._BT_RETURN."</center>");
	}


// Import an archive
	if (isset($_POST['import'])) {
		echo "<p class='style1'><b>"._ARCHIVE_RESTORE."</b></p><br>";
		$tmp_name = $_FILES['importfile']['tmp_name'];
		$filename = basename($_FILES['importfile']['name']);
		$filetype = basename($_FILES['importfile']['type']);
		if ( ($filetype != "x-compressed") && ($filetype != "x-gzip-compressed") ) $msg = _ARCHIVE_IMPORT_FAILED;
		elseif (is_uploaded_file($tmp_name)) {
			if ( move_uploaded_file( $tmp_name, ARCHIVE_DIR.$filename) ) $msg = _ARCHIVE_IMPORT_COMPLETED;
			else $msg = _ARCHIVE_IMPORT_FAILED;
		} else $msg = _ARCHIVE_IMPORT_FAILED;
		exit("<br><center class='style3'>$msg<br><br>"._BT_RETURN."</center>");
	}


// Restore from archive
	if (isset($_POST['restore']) && ($_POST['restore'] == 1)) {
		echo "<p class='style1'><b>"._ARCHIVE_RESTORE."</b></p><br>";
		if (!isset($_POST['sel_archive'])) $msg = _ARCHIVE_RESTORE_NOSELECT;
		else {
			$options = (isset($_POST['xmail'])?1:0) + (isset($_POST['pop'])?2:0)+ (isset($_POST['rc'])?4:0);
			if ($options == 0) $msg = _ARCHIVE_RESTORE_NOSELECT;
			else {
				$options = (isset($_POST['xmail'])?1:0) + (isset($_POST['pop'])?2:0)+ (isset($_POST['rc'])?4:0);
				exec(WRITE_HTTP."xdove=stop".WRITE_LOG);
				exec(WRITE_HTTP."archive=restore&options=$options&file=".$_POST['sel_archive'].WRITE_LOG);
			}
		}
		// Reimport RC database (DB shall exists)
		if (isset($_POST['rc'])) {
			if ($installed_mysql != RUNNING) exit("<br><center class='style3'>"._WARNING._ARCHIVE_NO_MYSQL."<br><br>"._BT_RETURN."</center>");
			$line = file(PHPXMAIL_FILE);
			$tmp = explode("\t", $line[0]); $sqlpassword = xmdecrypt($tmp[4]);
			$db = @mysql_connect("localhost:".QNAP_MYSQL_PORT, "roundcube", $sqlpassword);
			if ($db) {
				mysql_select_db("roundcubemail", $db);
				if ($content=file(ROUNDCUBE_DIR.ROUNDCUBE_SQL))
					foreach($content as $sql) {
						$sql = trim($sql);
						if (trim($sql)!="") mysql_query($sql) or die("QUERY ERROR - ".mysql_error()." query: ".$sql);
					}
			} else $msg = _ARCHIVE_NO_DB;
		}
		if ($msg == "") $msg = _ARCHIVE_RESTORE_DONE;
		exit("<br><center class='style3'>$msg<br><br>"._BT_RETURN."</center>");
	}



// Cron task on backup
	if (isset($_POST['cron']) || isset($_POST['delcron'])) {
		echo "<p class='style1'><b>"._ARCHIVE_CRON."</b></p><br>";
		$options = (isset($_POST['xmail'])?1:0) + (isset($_POST['pop'])?2:0)+ (isset($_POST['rc'])?4:0);
		if ($options == 0) exit("<br><center class='style3'>"._ARCHIVE_NOSELECT."<br><br>"._BT_RETURN."</center>");
		if ($_POST['quickset'] == 1) {
			switch($_POST['mode']) {
				case 0: $cmd = "0 * * * *"; break;	// Every hours
				case 1: $cmd = "0 0 * * *"; break;	// Every days at 00:00
				case 2: $cmd = "0 0 * * 0"; break;	// Every week on sunday at 00:00
				case 3: $cmd = "0 0 1 * *"; break;	// Every month the 1st at 00:00
				case 4: $cmd = "0 0 1 1 *"; break;	// Every year on january 1st at 00:00
			}
		} else {
			$month = (isset($_POST['all_months'])?"*":$_POST['months']);
			$dayweek = (isset($_POST['all_weekdays'])?"*":$_POST['weekdays']);
			$day = (isset($_POST['all_days'])?"*":$_POST['days']);
			$hour = (isset($_POST['all_hours'])?"*":$_POST['hours']);
			$minute = (isset($_POST['all_mins'])?"*":$_POST['mins']);
			$cmd  = $minute." ".$hour." ".$day." ".$month." ".$dayweek;
		}
		$cmd .= " ".ARCHIVE_SCRIPT." save options $options rotate ".ARCHIVE_MAX_NB." > ".XDOVE_DIR.LOGS_DIR."cron.log ";
		if (isset($_POST['cron'])) {
			$f_handle = fopen(ARCHIVE_CRON,"w");
			fputs($f_handle, $cmd);
			fclose($f_handle);
			exec(WRITE_HTTP."archive=setcron".WRITE_LOG);
			$msg = _ARCHIVE_CRON_DONE;
		} else {
			exec(WRITE_HTTP."archive=delcron".WRITE_LOG);
			$msg = _ARCHIVE_CRON_DONE_DEL;
		}
		exit("<br><center class='style3'>$msg<br><br>"._BT_RETURN."</center>");
	}

// Create number drop list
	function number_droplist($name, $start, $len) {
		$content = "<td><select size='1' name='$name' id='$name' disabled>";
		for ($i=$start; $i <$start+$len; $i++) $content .= "<option value='$i'>$i</option>";
		$content .= "</select></td>";
		return $content;
	}

	function date_droplist($name) {
		if ($name == "weekdays") { $start =0; $stop = 7; } else { $start = 0; $stop = 12; }
		$content = "<td><select size='1' name='$name' id='$name' disabled>";
		for ($i=$start; $i <$stop; $i++)
			if ($name == "weekdays") $content .= "<option value='$i' >".date("l",mktime(0,0,0,1,$i,1990))."</option>\n";
			else $content .= "<option value='".($i+1)."' >".date("F",mktime(0,0,0,$i+1,1,1990))."</option>\n";
		$content .= "</select></td>";
		return $content;
	}


// List the archives
	function list_archive($mode) {

	global $lid, $action, $content;

	function date_compare($a, $b) { return ($a[1] < $b[1]) ? 1 : -1;}

	if (ARCHIVE_MAX_NB > 0) $msg = "("._MAX." ".ARCHIVE_MAX_NB.")";
	$content = "<center>
		<span class='caption'>"._ARCHIVE_LIST." $msg</span>
		<table class=' archive'><tr>
		<th>"._ARCHIVE_ARCHIVE_NAMES."</th><th>"._ARCHIVE_ARCHIVE_DATE."</th><th>"._ARCHIVE_ARCHIVE_SIZE."</th><th>";
	if (!$mode) $content .= "<img src='".PHPXMAIL_URL."gfx/ico_delete.gif' alt='' height='16' width='16' border='0' alt='' onclick=\"javascript:if (window.confirm('"._ARCHIVE_DELETE_CONFIRM."')) location='archive.php?lid=$lid&amp;action=$action&amp;delete=delall';\">";
	$content .= "</th></tr>";

		if ($handle = opendir(ARCHIVE_DIR)) {
			$files = array();
			while ( false !== ($file = readdir($handle)) ) {
				$fileExt = explode(".", $file);
				if ($fileExt[1] == ARCHIVE_SUFFIX) {
					$filedate = explode("T", $fileExt[0]);
					if ($filedate[1]=="") $f_date = fileatime(ARCHIVE_DIR.$file);
					else $f_date = mktime(substr($filedate[1],8,2),substr($filedate[1],10,2),substr($filedate[1],12,2),substr($filedate[1],4,2),substr($filedate[1],6,2),substr($filedate[1],0,4));
					$files[] = array($file,$f_date);
					usort($files, 'date_compare');
				}
			}
			foreach($files as $data) {
				$file = $data[0]; $filedate = $data[1];
				$file_size = filesize("../".ARCHIVE_WEB.$file);
				$file_size = ($file_size>1073741824?round($file_size/1073741824,2)." "._GBYTES:($file_size>1048576?round($file_size/1048576,2)." "._MBYTES:($file_size>1024?round($file_size/1024,2)." "._KBYTES:"$file_size "._BYTES)));
				$content .= "<tr><td align='left'>";
				if ($mode) $content .= "<input type='radio' name='sel_archive' value='$file'> ";
					$content .= "&nbsp;<a href='../".ARCHIVE_WEB."$file'>$file</a></td><td align='right'>";
					$content .= date(_DATE_FORMAT, $filedate). " &nbsp;</td><td align='right'>$file_size</td><td align='center'>";
				if (!$mode) $content .= "<a href='archive.php?lid=$lid&amp;action=$action&amp;delete=$file'><img src='".PHPXMAIL_URL."gfx/ico_delete.gif' alt='' height='16' width='16' border='0' alt=''></a>";
				echo "</td></tr>";
			}
			closedir($handle);
		}
	$content .= "
			</td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
	</table></center><br>";
	}


// Check if a cron trask is planned for archiving and if there are archives
	$installed_cron = NOT_CONFIGURED;
	if ($handle = opendir(ARCHIVE_DIR)) {
		while ( false !== ($file = readdir($handle)) ) {
			$fileExt = explode(".", $file);
			if ($fileExt[1] == ARCHIVE_SUFFIX) {
				$installed_cron = STOPPED; break;
			}
		}
	}
	exec(WRITE_HTTP."archive=readcron".WRITE_LOG); $tmp = file(CRON_STATUS);


// Main
	echo "<div class='qs_install'>
	<form name='frmarchive' action='archive.php?lid=$lid&amp;action=$action' method='post' enctype='multipart/form-data'>";

	switch($action) {

	// Archive mailboxes
		case "save":
			echo "<p class='style1'><b>"._ARCHIVE_SAVE."</b></p><br>";
			echo "<p class='style3'>"._ARCHIVE_SAVE_1."</p><br><br><center class='style3'>";
			list_archive(false); echo $content;
			echo _ARCHIVE_SAVE_2."<br><b>"._ARCHIVE."</b>
			<input type='checkbox' name='xmail' value='xmail' checked>"._ARCHIVE_XMAIL."
			<input type='checkbox' name='pop' value='pop'>"._ARCHIVE_POP."
			<input type='checkbox' name='rc' value='rc'>"._ARCHIVE_RC."
			<br><br>
			<input class='button' type='submit' name='save' value='"._BT_SAVE."'>
			</center>";
			break;

	// Restore mailboxes
		case "restore":
			echo "<p class='style1'><b>"._ARCHIVE_RESTORE."</b></p><br>";
			echo "<p class='style3'>"._ARCHIVE_RESTORE_1."<br><br>"._ARCHIVE_RESTORE_2."</p><br><center class='style3'>";
			list_archive(true); echo $content;
			echo "
			"._ARCHIVE_IMPORT."
			<input class='button' type='file' name='importfile' value='' size='30' onchange=\"javascript:if (this.value.split('.').reverse()[0] != '".ARCHIVE_SUFFIX."') { alert('"._ARCHIVE_IMPORT_NOFILE."'); this.value=''; }\">&nbsp;
			<input class='button' type='submit' name='import' value='"._BT_IMPORT."'><br><br>
			"._ARCHIVE_RESTORE_3."<br><b>"._RESTORE."</b>
			<input type='checkbox' name='xmail' value='xmail'>"._ARCHIVE_XMAIL."
			<input type='checkbox' name='pop' value='pop'>"._ARCHIVE_POP."
			<input type='checkbox' name='rc' value='rc'>"._ARCHIVE_RC."
			<br><br>
			<input type='hidden' name='restore' value='0'>
			<input class='button' type='button' value='"._BT_RESTORE."' onclick=\"javascript:if (window.confirm('"._ARCHIVE_RESTORE_CONFIRM."')) { document.frmarchive.restore.value = '1';document.frmarchive.submit();}\">
			</center>";
			break;

	// Plan a automatic archiving
		case "cron":
			echo "<p class='style1'><b>"._ARCHIVE_CRON."</b></p><br>";
			echo "<p class='style3'>"._ARCHIVE_CRON_1."<br>"._ARCHIVE_CRON_2.
				 " <a href='../".LOGS_DIR."cron.log' target='_blank'>"._HERE."</a></p><br>
				 <p align='center'><b>";

			echo ($tmp[0]==1?_ARCHIVE_CRON_PLANNED:_ARCHIVE_CRON_NOTPLANNED)."</b></p><br>";
			echo"
				<center><table class='style3'><tr><td align='left'>
					<input type='radio' name='quickset' value='1' checked onClick=\"javascript: document.getElementById('cron_table').style.display='none';\">
					<select name='mode'>
					<option value='0'>"._ARCHIVE_CRON_EVERY_H."</option>
					<option value='1' selected>"._ARCHIVE_CRON_EVERY_D."</option>
					<option value='2'>"._ARCHIVE_CRON_EVERY_W."</option>
					<option value='3'>"._ARCHIVE_CRON_EVERY_M."</option>
					<option value='4'>"._ARCHIVE_CRON_EVERY_Y."</option>
					</select><br>
					<input type='radio' name='quickset' value='0' onClick=\"javascript: document.getElementById('cron_table').style.display='block';\"> "._ARCHIVE_CRON_PROGRAM."</td>
				</tr></table><br>
				<table class='cron_table' id='cron_table'>";
			echo "<tr class='cronset' id='cronset'>
					<td class='cron_section'><b>"._ARCHIVE_CRON_MONTHS."</b><br>
					<input type='checkbox' name='all_months' checked id='all_months' value='1' onClick=\"javascript: document.getElementById('months').disabled=this.checked;\"> "._ARCHIVE_CRON_ALL."<br>
					<table><tr>";
			echo date_droplist("months");
			echo "	</tr></table></td>
					<td class='cron_section'><b>"._ARCHIVE_CRON_WEEKS."</b><br>
					<input type='checkbox' name='all_weekdays' checked id='all_weekdays' value='1' onClick=\"javascript: document.getElementById('weekdays').disabled=this.checked;\"> "._ARCHIVE_CRON_ALL."<br>
					<table><tr>";
			echo date_droplist("weekdays");
			echo "	</tr></table></td>
					<td class='cron_section'><b>"._ARCHIVE_CRON_DAYS."</b><br>
					<input type='checkbox' name='all_days' checked id='all_days' value='1' onClick=\"javascript: document.getElementById('days').disabled=this.checked;\"> "._ARCHIVE_CRON_ALL."<br>
					<table><tr>";
			echo number_droplist("days", 1, 31);
			echo "	</tr></table></td>
					<td class='cron_section'><b>"._ARCHIVE_CRON_HOURS."</b><br>
					<input type='checkbox' name='all_hours' checked id='all_hours' value='1' onClick=\"javascript: document.getElementById('hours').disabled=this.checked;\"> "._ARCHIVE_CRON_ALL."<br>
					<table> <tr>";
			echo number_droplist("hours", 0, 24);
			echo "	</tr></table></td>
					<td class='cron_section'><b>"._ARCHIVE_CRON_MINS."</b><br>
					<input type='checkbox' name='all_mins' checked id='all_mins' value='1' onClick=\"javascript: document.getElementById('mins').disabled=this.checked;\"> "._ARCHIVE_CRON_ALL."<br>
					<table><tr>";
			echo number_droplist("mins", 0, 60);
			echo "	</tr></table></td>
				  </tr><tr><td>&nbsp</td></tr>
				</table><p>";
			echo _ARCHIVE_CRON_3."<br><b>"._ARCHIVE."</b>
			<input type='checkbox' name='xmail' value='xmail' checked>"._ARCHIVE_XMAIL."
			<input type='checkbox' name='pop' value='pop'>"._ARCHIVE_POP."
			<input type='checkbox' name='rc' value='rc'>"._ARCHIVE_RC."
			</p><br>
			<input class='button' type='submit' name='cron' value='"._BT_SUBMIT."'> &nbsp; <input class='button' type='submit' name='delcron' value='"._BT_DELETE."'>
			</center>";
			break;
		default: die("<p>System error</p>");

	}

	echo "</form></div>";
	include('../.config/footer.php');

?>
