<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	$sql = pro_db_query("SELECT * FROM loginMaster WHERE loginID = '" . $_REQUEST['loginID'] . "'");
	$cnt = pro_db_num_rows($sql);
	if ($cnt > 0) {
		$jsonArray['valid'] = 0;
		$jsonArray['msg'] = "LoginID is not available !!!";
	} else {
		$jsonArray['valid'] = 1;
		$jsonArray['msg'] = "LoginID is available.";
	}
	print json_encode($jsonArray);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>