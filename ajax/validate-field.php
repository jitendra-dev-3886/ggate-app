<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	$fldValue = pro_db_real_escape_string(trim($_REQUEST['fldValue']));
	$fldName = $_REQUEST['fldName'];
	$tblName = $_REQUEST['tblName'];
	$chkSql = pro_db_query("Select * from " . $tblName . " where " . $fldName . " = '" . $fldValue . "'");
	if (pro_db_num_rows($chkSql) > 0) {
		echo $fldValue . " is already in database, Duplicate entry is not allowed!!!";
	} else {
		echo "Correct";
	}
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>