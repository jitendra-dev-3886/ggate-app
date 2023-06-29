<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
include "lib/phpqrcode/qrlib.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$statusArray = array("0" => "Inactive", "1" => "Active");

	$queryString = pro_db_query("SELECT * from locationMaster where complexID = " . $_SESSION['complexID']);

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "locationID:" . $res['locationID'];
		$locationName = '<td>' . $res['locationName'] . '</td>';
		$locationID = $res['locationID'];

		if ($isProduction != 0) {
			$qrCodeLocationName = "GGATE-LOCATION-" . $locationID;
			$codeFile = DIR_FS_QRCODES_TMP_PATH . "/" . $qrCodeLocationName . ".png";
			$codeWebFile = DIR_WS_QRCODES_TMP_PATH . "/" . $qrCodeLocationName . ".png";
			$qrCodeText = base64_encode($qrCodeLocationName);

			$ecc = 'L';
			$pixel_size = 4;
			$frame_size = 4;

			// Generates QR Code and Stores it in directory given
			QRcode::png($qrCodeText, $codeFile, $ecc, $pixel_size, $frame_size);
			$qrCode = '<td class="text-center"><img class="img-fluid" src="' . $codeWebFile . '" /></td>';
		} else {
			$qrCode = '<td></td>';
		}

		if ($res['status'] == 1) {
			$status = '<td><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></td>';
		} else {
			$status = '<td><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></td>';
		}
		$action = '<td><a href="index.php?controller=complexmasters&action=locationmaster&subaction=editForm&locationID=' . $res['locationID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=complexmasters&action=locationmaster&subaction=delete&locationID=' . $res['locationID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$qrCode", "$locationName", "$status", "$action");
	}
	// End While Loop

	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>