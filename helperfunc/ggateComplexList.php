<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$statusArray = array("0" => "Pending", "1" => "Active", "126" => "Disable");

	$queryString = pro_db_query(
		"select cm.complexID, cm.complexName, cm.complexLogo, cm.complexAddress, cm.complexContactNo, cm.complexEmail, city.cityName,
		z.zone_name as stateName, c.countries_name as countryName, cm.maxBlocks, cm.maxProperties, cm.enrolledDate, cm.validUptoDate, cm.status
		from complexMaster cm
		left join cityMaster city on cm.city_id = city.cityID and city.status = 1
		left join zones z on cm.zone_id = z.zone_id and z.status = 1
		left join countries c on cm.countries_id = c.countries_id and c.status = 1"
	);

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "complexID:" . $res['complexID'];
		$complexName = '<td>' . ucfirst($res['complexName']) . '</td>';

		if ($res['complexLogo'] == null || empty($res['complexLogo'])) {
			$res['complexLogo'] = "https://cdn.ggate.app/ggateweb/assets/images/logo-1.svg";
		}
		$complexLogo = '<td><img src="' . $res['complexLogo'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$complexAddress = '<td>' . ucfirst($res['complexAddress']) . '</td>';
		$complexContactNo = '<td>' . $res['complexContactNo'] . '</td>';
		$complexEmail = '<td>' . $res['complexEmail'] . '</td>';
		$cityName = '<td>' . $res['cityName'] . ', ' . $res['stateName'] . ', ' . $res['countryName'] . '</td>';
		// $stateName = '<td>' . $res['stateName'] . '</td>';
		// $countryName = '<td>' . $res['countryName'] . '</td>';
		$maxBlocks = '<td>' . $res['maxBlocks'] . '</td>';
		$maxProperties = '<td>' . $res['maxProperties'] . '</td>';
		$enrolledDate = '<td>' . $res['enrolledDate'] . '</td>';
		$validUptoDate = '<td>' . $res['validUptoDate'] . '</td>';

		if (isset($res['status'])) {
			if ($res['status'] == 1) {
				$status = '<td><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></td>';
				$statusAction = '<a href="index.php?controller=ggatemain&action=ggatedashboard&subaction=manageComplexStatus&complexID=' . $res['complexID'] . '&status=126" title="Disable"><i class="fas fa-user-minus text-danger"></i></a>';
			} else {
				$status = '<td><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></td>';
				$statusAction = '<a href="index.php?controller=ggatemain&action=ggatedashboard&subaction=manageComplexStatus&complexID=' . $res['complexID'] . '&status=1" title="Enable"><i class="fas fa-user-plus text-success"></i></a>';
			}
		} else {
			$status = '<td><span class="badge badge-secondary">' . "Not Available" . '</td>';
		}

		$action = '<td></td>';
		if ($res['complexID'] != $_SESSION['superComplexID']) {
			$action = '<td><a href="index.php?controller=ggatemain&action=ggatedashboard&subaction=editForm&complexID=' . $res['complexID'] . '" title="Edit"><i class="fe-edit text-warning"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
						<a href="index.php?controller=ggatemain&action=ggatedashboard&subaction=complexInfoDetails&complexID=' . $res['complexID'] . '" title="Info"><i class="fas fa-info-circle text-info"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
						<a href="index.php?controller=ggatemain&action=ggatedashboard&subaction=complexPackageForm&complexID=' . $res['complexID'] . '" title="Society Package"><i class="fas fa-list text-primary"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
						<a href="index.php?controller=ggatemain&action=ggatedashboard&subaction=complexResetPassword&complexID=' . $res['complexID'] . '" title="Reset Password"><i class="fas fa-sync-alt text-danger"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
						' . $statusAction . '
						</td>';
		}
		$result['aaData'][] = array("$complexLogo", "$complexName", "$complexAddress", "$cityName", "$complexContactNo", "$complexEmail", "$status", "$action");
	}
	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>
