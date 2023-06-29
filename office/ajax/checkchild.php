<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	$primary_id = $_POST['primary_id'];
	$table_name = $_POST['table_name'];
	$field_name = $_POST['field_name'];

	$sql = pro_db_query("SELECT * FROM " . $table_name . " WHERE " . $field_name . " = " . $primary_id);
	if (pro_db_num_rows($sql) > 0) {
		$jsonArray['cnt'] = $cnt;
		$jsonArray['msg'] = "You Can't Delete this Record...";
	} else {
		$jsonArray['cnt'] = $cnt;
		$jsonArray['primary_id'] = $primary_id;
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