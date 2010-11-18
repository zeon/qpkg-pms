<?php
/************************************************************************
	XDove is a GPL'ed software developed by

	 - Andy Chuo (QNAP) and Laurent (AdNovea)

	**********************************************
	- File:			languages.php
	- Date:			December 27th, 2008
	- Version:		1.0
	- Description:  Keep track of available languages

*************************************************************************/

	$languages['English'] = Array(
		"name"		=> "English",
		"path"		=> "en_US",
		"locale"	=> "en_US");
		
	$languages['French'] = Array(
		"name"		=> "Français",
		"path"		=> "fr_FR",
		"locale"	=> "fr_FR.utf-8");
	
	$languages['Deutsch'] = Array(
		"name"          => "Deutsch",
		"path"          => "de_DE",
		"locale"        => "de_DE.utf-8");

	$languages['Italiano'] = Array( 
		 "name"          => "Italiano",
		 "path"          => "it_IT",
		 "locale"        => "it_IT.utf-8");

	$languages['Simplified'] = Array(
		"name"		=> "简体中文",
		"path"		=> "zh_CN",
		"locale"	=> "zh_CN.utf-8");
	
	$languages['Traditional'] = Array(
		"name"		=> "繁体中文",
		"path"		=> "zh_TW",
		"locale"	=> "zh_TW.utf-8");
	
	setlocale(LC_ALL, $languages[$lid]['locale']);

?>