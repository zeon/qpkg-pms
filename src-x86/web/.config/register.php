<?php
/************************************************************************
	XDove is a GPL'ed software developed by

	 - Andy Chuo (QNAP)

	**********************************************
	- File:			register.php
	- Developer: 	Laurent (AdNovea)
	- Date:			December 27th, 2008
	- Version:		1.0
	- Description:  XDove and XMail Admin users management

*************************************************************************/

	require("conf.php");
	require_once(PHPXMAIL_DIR."class.xmail.php");
	load_language("accounts");	// Load language strings for account management
	set_js_vars();

	// List XDove Admin user
	function listUsers() {
		$content = "";
		if (file_exists(HTPASSWD_FILE)) {
			$f_text = file(HTPASSWD_FILE);
			foreach( $f_text as $line ) {
				$tmp = explode(':', trim($line));
				if ($tmp[0] != "") $content .= "<option value=\"".$tmp[0]."\">".$tmp[0]."</option>\n";
			}
		}
		return $content;
	}


	// Remove XDove Admin user
	function removeUser($username) {

		if (count(file(HTPASSWD_FILE)) < 2) return _ACCOUNTS_LAST;

		if (file_exists(HTPASSWD_FILE)) {
			$f_text = file(HTPASSWD_FILE);
			$file_handle = fopen(HTPASSWD_FILE,"w");
			foreach( $f_text as $line ) {
				 if ( !strstr($line, $username) ) fputs($file_handle, $line);
			}
			fclose($file_handle);
		}
	}


	// Add a new XDove Admin user
	function registerXDoveUser($username,$pass) {

		// Check user existence
		$pfile = fopen(HTPASSWD_FILE,"a+");
		rewind($pfile);
		while (!feof($pfile)) {
			$line = fgets($pfile);
			$tmp = explode(':', $line);
			if ($tmp[0] == $username) {
				$errorText = _ACCOUNTS_USEREXIST;
				break;
			}
		}
		// If everything is OK -> store user data
		if ($errorText == '') exec(HTPASSWD." -bm ".HTPASSWD_FILE." $username $pass",$output);
		fclose($pfile);
		return $errorText.$output[0];
	}


	// Remove XDove administrator
	if (isset($_POST['delete']) && ($_POST['delete'] == _BT_DELETE)) {
		$username = $_POST['username'];
		$error = removeUser($username);
	}


	// Modify XMail Admin account
	function registerXMailUser($username,$pass) {
		// Modify servers.php
		$output = null; exec("echo \"".QNAP_NAME."\t".QNAP_IP."\t6017\t$username\t".xmcrypt($pass)."\t0\" > ".PHPXMAIL_FILE, $output);
		$error = $output[0];
		
		// Modify ctrlaccounts.tab
		exec(WRITE_HTTP."writefile=".XMAIL_PWD_FILE."&action=overwrite&$username=".xmcrypt($pass).WRITE_LOG);
		return $error;
	}


	// Tests on XMail & XDove accounts parameters
	if ( isset($_POST['add']) && ($_POST['add'] == _BT_REGISTER) ) {
		// Get user input
		$username  = isset($_POST['newuser']) ? $_POST['newuser'] : "";
		$password1 = isset($_POST['password1']) ? $_POST['password1'] : "";
		$password2 = isset($_POST['password2']) ? $_POST['password2'] : "";

		// Check passwords
		$error = '';
		if ($password1 != $password2) $error = _ACCOUNTS_PWD_NOT_IDEM;
		if (strlen($password1) < $admin_min_pwd) $error = _ACCOUNTS_PWD_TOOSHORT." $admin_min_pwd";
		if ($password1 == "") $error = _ACCOUNTS_INVALID;
		if ($username == "") $error = _ACCOUNTS_INVALID;

		if ($error == "") { 
			if (isset($_GET['xmail']) && ($_GET['xmail'] == "pwd"))
				$error = registerXMailUser($username,$password1);	// Change XMail account
			else
				$error = registerXDoveUser($username,$password1);	// Try to register the new XDoveuser
		}
	}


	include('header.php');
	
// Set text and gets according to the type of account edition
	if (isset($_GET['xmail']) && ($_GET['xmail'] == "pwd")) {
		$admin_title = $admin_xmail_pwd;
		$show_this = "visibility: hidden;";
		$getstring = "&xmail=pwd";
		$accounts_title = _ACCOUNTS_XMAIL_TITLE;
		$accounts_desc = _ACCOUNTS_XMAIL_USER;
	} else {
		$admin_title = $admin_reg_user;
		$show_this = "visibility: visible";
		$getstring = "";
		$accounts_title = _ACCOUNTS_TITLE;
		$accounts_desc = _ACCOUNTS_REG_USER;
	}
	
	echo "<p class=\"style1\">$accounts_title</p><center>";
	
	if ((!isset($_POST['add']) && !isset($_POST['delete'])) || ($error != '')) {
		echo "
			<div id=\"result\" style=\"width: 99%; height: 190px;\">
			<form action=\"register.php?lid=$lid$getstring\" method=\"post\" name=\"registerform\">
				<input type=\"hidden\" name=\"lid\" value=\"$lid\">
				<table width=\"100%\" class=\"login_table\">
				<tr><td colspan=\"2\" align=\"center\">$accounts_desc<br>&nbsp;</td></tr>
				<tr style=\"$show_this;\"><td width=\"50%\" align=\"right\">"._ACCOUNTS_USERNAME."</td><td>
				<select  name=\"username\" class=\"username\" onchange=\"javascript:if (this.value == '"._ACCOUNTS_ADD_NEW."') admin_config(0); else admin_config(1);\">
					<option value=\""._ACCOUNTS_ADD_NEW."\">"._ACCOUNTS_ADD_NEW."</option>";
		echo listUsers();
		echo"
				</select></td></tr>
				<tr><td width=\"50%\" align=\"right\">"._ACCOUNTS_ADD_NEW_NAME."</td><td> <input class=\"text\" id=\"ad_newuser\" name=\"newuser\" type=\"text\" ></td></tr>
				<tr><td align=\"right\">"._ACCOUNTS_PASSWORD."</td><td> <input class=\"text\"id=\"ad_password1\" name=\"password1\" type=\"password\"></td></tr>
				<tr><td align=\"right\">"._ACCOUNTS_CONFIRM."</td><td> <input id=\"ad_password2\" class=\"text\" name=\"password2\" type=\"password\"></td></tr>
				<tr><td colspan=\"2\">&nbsp;</td></tr>
				<tr><td align=\"center\" colspan=\"2\">
					<input class=\"button\" type=\"submit\" id=\"ad_add\" name=\"add\" value=\""._BT_REGISTER."\">
					<input class=\"button\" type=\"submit\" id=\"ad_del\" name=\"delete\" value=\""._BT_DELETE."\" style=\"display: none;\">
				</td></tr>
				</table>
			</form>
		</div><center>
		";
	}

    if (isset($_POST['add']) || isset($_POST['delete']) ) {
		echo "<center>
			<div id=\"result\" style=\"width: 99%; height: 100px\">
			<table width=\"100%\" class=\"login_table\">
			<tr><td align=\"center\"><br>";
		if ($error == '') {
			echo "$username : "._ACCOUNTS_RESULT_OK."<br>
			<br><br>
			<input class=\"button\" type=\"button\" value=\""._BT_CONTINUE."\" onclick=\"javascript:location='register.php?lid=$lid$getstring';\" tabindex=\"1\">";
		}
		else echo _ACCOUNTS_RESULT." $error";

		echo "
			</td></tr>
			</table>
		</div></center>
		";
    }

	include('footer.php');

?>
