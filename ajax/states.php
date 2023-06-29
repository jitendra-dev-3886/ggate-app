<?php
	chdir('../');
	include "config/config.php";
	include "lib/base.php";
	include "lib/general.php";

	$countryID = (int)$_REQUEST['countries_id'];
	$whr = "zone_country_id = " . $countryID;
	$State = generateOptions(getMasterList('zones','zone_id','zone_name',$whr));
	print $State;
?>