<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	/* Setting General Post Values */
	$fldValue = pro_db_real_escape_string(trim($_REQUEST['value']));
	$fldName = $_REQUEST['name'];
	$tblName = $_REQUEST['tblName'];
	/* Setting Primary Key Values */
	$pkdata = explode(":", $_POST['pk']);
	$pkfldName = trim($pkdata[0]);
	$pkfldValue = trim($pkdata[1]);
	/* Preparing Query to Update Records */
	echo $updqry = "update $tblName set $fldName = '$fldValue' where $pkfldName = '" . (int)$pkfldValue . "' ";
	if (pro_db_query($updqry)) {
		return "Record Successfully Updated...";
	} else {
		return "Error in updating record!!!";
	}
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>