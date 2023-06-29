<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	foreach ($_POST as $key => $value) {
		$chkSql = pro_db_query("select * from siteOptions where optTitle = '" . $key . "'");
		if (pro_db_num_rows($chkSql) > 0) {
			$sql = "UPDATE siteOptions SET optValue='" . $value . "' WHERE optTitle='" . $key . "'";
		} else {
			$sql = "insert into siteOptions SET optValue='" . $value . "', optTitle='" . $key . "'";
		}
		if (pro_db_query($sql)) {
			echo "Success";
		} else {
			echo "Error";
		}
	}
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>