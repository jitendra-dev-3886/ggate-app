<?php

/**
 * Filter given keys from the source array
 * returns array without given filter keys
 *
 * @access public
 * @param array $sourceArray
 * @param array $filterOutKeys
 * @return array
 */
function filterArray($sourceArray, $filterOutKeys)
{
	$filteredArr = array_diff_key($sourceArray, array_flip($filterOutKeys));
	return $filteredArr;
}

function get_group_name($grpid)
{
	$chkqry = pro_db_query("select * from group_master where group_id = '" . $grpid . "'");
	if (pro_db_num_rows($chkqry) > 0) {
		$res = pro_db_fetch_array($chkqry);
	}
	return $res['group_name'];
}

function getControllerNames()
{
	$file_arr = array();
	$path = DIR_FS_CONTROLLER . "/";
	$i = 0;
	if ($handle = opendir($path)) {
		while ($file = readdir($handle)) {
			$whole = $path . $file;
			if (is_dir($whole)) {
				$file_arr[$i] = $file;
				$i++;
			}
		}
		return $file_arr;
	}
}

//Get Last Login Remote IP
function last_login_from()
{
	$last_login_sql = pro_db_query("select max(sessionLogID) as ss from sessionLogMaster ");
	$res_login = pro_db_fetch_array($last_login_sql);
	$log_sql = pro_db_query("select remote_ip from sessionLogMaster where sessionLogID = '" . $res_login['ss'] . "'");
	$res_log = pro_db_fetch_array($log_sql);
	return $res_log['remote_ip'];
}

function createSalt()
{
	$length = mt_rand(64, 255);
	$salt = '';
	for ($i = 0; $i < $length; $i++) {
		$salt .= chr(mt_rand(33, 255));
	}
	return $salt;
}

function get_sortorder($table_name, $primary_id)
{
	$srt_sql = pro_db_query("select " . $primary_id . " from " . $table_name . " order by " . $primary_id . " desc");
	$srt_res = pro_db_fetch_array($srt_sql);
	return $srt_res[$primary_id] + 1;
}

function getMasterArray($tblName, $keyfld, $valuefld, $whr = "", $orderfld = "")
{
	if (!empty($orderfld)) {
		$order = $orderfld;
	} else {
		$order = $valuefld;
	}

	if (!empty($whr)) {
		$arrSql = pro_db_query("select $keyfld, $valuefld from $tblName where status = 1 or status = 'E' and $whr order by $order");
	} else {
		$arrSql = pro_db_query("select $keyfld, $valuefld from $tblName where status = 1 or status = 'E' order by $order");
	}
	$masterArray = array();
	if (pro_db_num_rows($arrSql) > 0) {
		while ($rs = pro_db_fetch_array($arrSql)) {
			$masterArray[$rs[$keyfld]] = $rs[$valuefld];
		}
	} else {
		$masterArray[] = "No Value Defined..";
	}
	return $masterArray;
}
function getMasterList($tblName, $keyfld, $valuefld, $whr = "", $orderfld = "")
{
	if (!empty($orderfld)) {
		$order = $orderfld;
	} else {
		$order = $valuefld;
	}

	if (!empty($whr)) {
		$arrSql = pro_db_query("select $keyfld, $valuefld from $tblName where 1=1 and $whr order by $order");
	} else {
		$arrSql = pro_db_query("select $keyfld, $valuefld from $tblName order by $order");
	}
	$masterArray = array();
	if (pro_db_num_rows($arrSql) > 0) {
		while ($rs = pro_db_fetch_array($arrSql)) {
			$masterArray[$rs[$keyfld]] = $rs[$valuefld];
		}
	} else {
		$masterArray[] = "No Value Defined..";
	}
	return $masterArray;
}
function getfldValue($tblName, $keyfld, $keyfldvalue, $getfld)
{
	$arrSql = pro_db_query("select $getfld from $tblName where $keyfld = '" . $keyfldvalue . "' ");
	if (pro_db_num_rows($arrSql) > 0) {
		$rs = pro_db_fetch_array($arrSql);
		return $rs[$getfld];
	} else {
		return "No Value Defined..";
	}
}

function displayParents($tblName, $keyfld, $valuefld, $parentfld, $whr = null)
{
	$pArrSql = pro_db_query("select $keyfld, $valuefld from $tblName where $parentfld = 0 $whr and status = 'E' order by $keyfld desc");

	$parentArray = array("0" => '0-Self Parent');
	if (pro_db_num_rows($pArrSql) > 0) {
		while ($rs = pro_db_fetch_array($pArrSql)) {
			$parentArray[$rs[$keyfld]] = $rs[$valuefld];
		}
	} else {
		$parentArray[] = "No Value Defined..";
	}
	return $parentArray;
}

function displayProjectParents($tblName, $keyfld, $valuefld, $parentfld)
{
	$pArrSql = pro_db_query("select $keyfld, $valuefld from $tblName where $parentfld = 0 order by $keyfld Desc");

	$parentArray = array();
	if (pro_db_num_rows($pArrSql) > 0) {
		while ($rs = pro_db_fetch_array($pArrSql)) {
			$parentArray[$rs[$keyfld]] = $rs[$valuefld];
		}
	} else {
		$parentArray[] = "No Value Defined..";
	}
	return $parentArray;
}

function displayProjectStatus()
{

	$psSql = pro_db_query("SELECT p.project_id, p.project_name, ps.status_name FROM project_master p join projectStatus ps on p.project_status_id = ps.status_id order by p.project_status_id, project_id");

	$psArray = array();
	if (pro_db_num_rows($psSql) > 0) {
		while ($rs = pro_db_fetch_array($psSql)) {
			$psArray[$rs['project_id']] = $rs['project_id'] . " - " . $rs['project_name'] . " || " . $rs['status_name'];
		}
	} else {
		$psArray[] = "No Value Found!!!";
	}
	return $psArray;
}
function generateOptions($dataArr, $current = "")
{
	$options = null;
	$selected = null;
	foreach ($dataArr as $key => $value) {
		if (is_array($current)) {
			$selected = (in_array($key, $current)) ? 'selected' : '';
		} else {
			$selected = ($key == $current) ? 'selected' : '';
		}
		$options .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
	}
	return $options;
}

function generateStaticOptions($dataArr, $current = "")
{
	$options = null;
	$selected = null;
	foreach ($dataArr as $key => $value) {
		$selected = ($key == $current) ? 'selected' : '';
		$options .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
	}
	return $options;
}

/* For Password Utilities */
// Generate OTP
function genOTP($length)
{
	$chars = "0123456789";
	$otp = substr(str_shuffle($chars), 0, $length);
	return $otp;
}
// Generate Password
function genPassword($length = 6)
{
	$chars = "abcdefghkmnpqrstuvwxyzABCDEFGHKMNPQRSTUVWXYZ23456789";
	$pwd = substr(str_shuffle($chars), 0, $length);
	return $pwd;
}
// Send SMS
function sendSMS($to, $msg)
{
	$url = 'http://mobileadz.in/smsapi.aspx';
	$username = 'cpsurat';
	$password = 'Ker007';
	$sender = 'ECHALN';
	$route = 'route3';
	$content = '?username=' . rawurlencode($username) .
		'&password=' . rawurlencode($password) .
		'&sender=' . rawurlencode($sender) .
		'&to=' . rawurlencode($to) .
		'&message=' . rawurlencode($msg) .
		'&route=' . rawurlencode($route);

	$output = file_get_contents($url . $content);
	return $output;
}

// Check Child Project
function hasSubProjects($project_id)
{
	$chkQry = pro_db_query("select * from project_master where parent_project_id = {$project_id}");
	if (pro_db_num_rows($chkQry) > 0) {
		return true;
	} else {
		return false;
	}
}

// Get Parent Project
function getParentProject($project_id)
{
	$chkQry = pro_db_query("select * from project_master where project_id = {$project_id}");
	if (pro_db_num_rows($chkQry) > 0) {
		$parentRs = pro_db_fetch_array($chkQry);
		return $parentRs['parent_project_id'];
	} else {
		return 0;
	}
}

// List Child Projects
function listChildProjects($project_id)
{
	$chkQry = pro_db_query("select * from project_master where parent_project_id = {$project_id} order by project_id DESC");
	$childList = "";
	while ($crs = pro_db_fetch_array($chkQry)) {
		$childList .= '<a href="index.php?controller=project&action=project&subaction=editForm&project_id=' . $crs['project_id'] . '" title="Edit"># ' . $crs['project_id'] . ' - ' . $crs['project_name'] . '</a><br>';
	}
	return $childList;
}

/* Calulate Avg LOI */
function averageLOI($project_id)
{
	$asql = pro_db_query("select round(avg((IFNULL(LOS,0)))) as AVGLOI from panellistRedirects where project_id = '" . $project_id . "' and redirect_status_id = 6 order by LOS");
	if (pro_db_num_rows($asql) > 0) {
		$avgLOI = pro_db_fetch_array($asql);
		return $avgLOI['AVGLOI'];
	} else {
		return 0;
	}
}
/* Calculate Total Ticket Count */
function showTicketCount($status, $priority, $project_id, $manager)
{
	$whr = "";
	$baseSql = "";
	$baseSql = "Select * from ticketMaster where 1=1";
	if ($status != "") {
		$whr .= " and status = '" . $status . "'";
	}
	if ($priority != "") {
		$whr .= " and priority = '" . $priority . "'";
	}
	if ($project_id != 0) {
		$whr .= " and project_id = '" . $project_id . "'";
	}
	if ($manager > 0) {
		$whr .= " and assign_to = '" . $manager . "'";
	}
	$baseSql .= $whr;
	$tsql = pro_db_query($baseSql);
	return pro_db_num_rows($tsql);
}
/* Get Current Project Status */
function getCurrentStatus($project_id)
{
	$pSql = pro_db_query("Select ps.status_name from project_master p join projectStatus ps on p.project_status_id = ps.status_id where p.project_id = " . $project_id);
	$psrs = pro_db_fetch_array($pSql);
	return $psrs['status_name'];
}

/* Get Count of Today's Messages */
function getMessageCount()
{
	$mSql = pro_db_query("Select * from messages where date(messageCreated) = current_date() and (messageHeader like 'Project Status%' or messageHeader like 'API Project%')");
	return pro_db_num_rows($mSql);
}

/* Get Count of Today's Messages */
function listMessages($limit = 0)
{
	if ($limit == 0) {
		$mSql = pro_db_query("Select * from messages where date(messageCreated) = current_date() and (messageHeader like 'Project Status%' or messageHeader like 'API Project%') order by messageCreated Desc");
	} else {
		$mSql = pro_db_query("Select * from messages where date(messageCreated) = current_date() and (messageHeader like 'Project Status%' or messageHeader like 'API Project%') order by messageCreated Desc limit " . $limit);
	}
	$messageBody = "";
	if (pro_db_num_rows($mSql) > 0) {
		while ($mrs = pro_db_fetch_array($mSql)) {
			$messageBody .= '<li><a href="#">' . '#' . $mrs['chainID'] . ' - ' . $mrs['messageHeader'] . '</a></li>';
		}
	} else {
		$messageBody .= '<li>There is no notifications</li>';
	}
	return $messageBody;
}

// Generate Year Combo
function genRptYear()
{
	$yearcmb = array();
	$currentYear = date('Y');
	for ($i = 2016; $i <= $currentYear; $i++) {
		$yearcmb[$i] = $i;
	}
	return $yearcmb;
}

// Generate Month Combo
function genRptMonth()
{
	//$months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	$info = cal_info(0);
	$months = $info['abbrevmonths'];
	return $months;
}
// Google Cloud Bucket Upload
function gcsUploadFile($bucketName, $fileContent, $cloudPath)
{
	global $storage;

	// set which bucket to work in
	$bucket = $storage->bucket($bucketName);

	// upload/replace file 
	$storageObject = $bucket->upload(
		$fileContent,
		['name' => $cloudPath]
	);

	return $storageObject != null;
}
// Google Cloud Bucket Delete
function gcsDeleteFile($bucketName, $objectName)
{
	global $storage;

	$bucket = $storage->bucket($bucketName);

	$object = $bucket->object($objectName);
	$object->delete();
}
?>