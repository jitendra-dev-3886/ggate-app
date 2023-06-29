<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	if ($_SESSION['groupID'] < 6) {
		$blockID = (int)$_REQUEST['blockID'];
	} else {
		$blockID = $_SESSION['blockID'];
	}
	$sql = pro_db_query("select noOfFloors from blockMaster where blockID =" . $blockID);
	if (pro_db_num_rows($sql) > 0) {
		$brs = pro_db_fetch_array($sql);
		for ($i = 1; $i <= $brs['noOfFloors']; $i++) {
			$dropdown .= '<option value="' . $i . '">Floor - ' . $i . '</option>';
		}
	}
	print $dropdown;

	$sql1 = pro_db_query("select squareFeetArea, ownerAmount, rentalAmount from societyMaintenanceSettings where blockID =" . $blockID);
	$rs = pro_db_fetch_array($sql1);
	$_SESSION['ownerAmount'] = $rs['ownerAmount'];
	$_SESSION['rentalAmount'] = $rs['rentalAmount'];
	$_SESSION['squareFeetArea'] = $rs['squareFeetArea'];
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>