<?php
	chdir('../');
	include "config/config.php";
	include "lib/base.php";
	include "lib/general.php";

	$zone_id = (int)$_REQUEST['zone_id'];
	$whr = "zone_id = ".$zone_id;
	$State = generateOptions(getMasterList('cityMaster','cityID','cityName',$whr));
	echo $State;
?>