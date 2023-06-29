<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$statusArray = array("0" => "Inactive", "1" => "Active");
	if ($_SESSION['memberID'] == 0) {
		$whr = 'officeContactNo as offiiceMobile';
	} else {
		$whr = "concat('******', RIGHT(officeContactNo, 4)) as offiiceMobile";
	}
	$queryString = pro_db_query("SELECT officeID, officeName, officeEmail, officeLogo, " . $whr . ", status  from officeMaster where (status = 1 or status = 0)");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "officeID:" . $res['officeID'];
		$officeName = '<td>' . $res['officeName'] . '</td>';
		$officeEmail = '<td>' . $res['officeEmail'] . '</td>';
		$offiiceMobile = '<td>' . $res['offiiceMobile'] . '</td>';
		if ($res['officeLogo'] == null || empty($res['officeLogo'])) {
			$res['officeLogo'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$officeLogo = '<td><img src="' . $res['officeLogo'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		if ($res['status'] == 1) {
			$Status = '<td><a href="#"  class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$Status = '<td><a href="#"  class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		$Action = '<td><a href="index.php?controller=officemasters&action=officemaster&subaction=editForm&officeID=' . $res['officeID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=officemasters&action=officemaster&subaction=delete&officeID=' . $res['officeID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$officeName", "$officeEmail", "$offiiceMobile", "$officeLogo", "$Status", "$Action");
	}
	// End While Loop
	// print_r($_SESSION);

	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>
