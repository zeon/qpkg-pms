/************************************************************************
	Q-Apps System is developed by Ad'Novea®

	**********************************************
	- File:			qapps-admin.js
	- Developer: 	Laurent (AdNovea)
	- Date:			2010, April-Aug.
	- version:		1.1
	- Description:  Q-Apps System Javascript code
*************************************************************************/

//----------------------------------------------------------------------
// Debug console
//----------------------------------------------------------------------
	var logFile;
	var TimerSet = false;	// Avoid multiple tasks scheduling when selecting a new log file - otherwise a new scheduling can be set whereas the previous is not finished and it adds
	var httpObject = null;
	var TimerRun = false;

	function readFile() {
 		if (httpObject.readyState == 4) {
			if (httpObject.status == 200) {
				var stResponse = httpObject.responseText.replace(/\n\r/g,"<br>").replace(/\r\n/g,"<br>").replace(/\n/g,"<br>").replace(/\r/g,"<br>");
				document.getElementById('console').innerHTML = stResponse;
				document.getElementById('console').scrollTop = document.getElementById('console').scrollHeight;
				setTimeout('loadFile()', 1000)
				TimerSet = false;
			} else {
				TimerSet = false;
				document.getElementById('console').innerHTML = "File is missing or cannot be read!" + logFile;
			}
		}
	}
	
	function loadFile(logType) {

		if (logType !== undefined) {
			switch (logType) {
				case 1: logFile = docFile0; break;
				case 2: logFile = docFile1; break;
				default: logFile = docFile0; break;
			} 
		}
		if (!TimerSet) { TimerSet = true; if (TimerRun) setTimeout('transferHTML()', 1000); }
	}

	function transferHTML() {
	
		if (window.ActiveXObject) httpObject = new ActiveXObject("Microsoft.XMLHTTP");
		else if (window.XMLHttpRequest) httpObject = new XMLHttpRequest();
		else { alert("Your browser does not support AJAX."); return null; }
		// Solve the cache issue
		httpObject.open("GET", logFile +"?nocache=" + Math.random(), true);
		httpObject.onreadystatechange = readFile;
		httpObject.send(null);
	}


