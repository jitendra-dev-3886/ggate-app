<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$statusArray = array("0" => "Disable", "1" => "Active");

	$queryString = pro_db_query("select sm.societyID, sm.societyName, sm.societyLogo, sm.societyAddress, sm.societyContactNo,
					sm.societyEmail, cm.cityName, z.zone_name as stateName, c.countries_name as countryName, sm.maxBlocks, sm.maxProperties,
					sm.enrolledDate, sm.validUptoDate, sm.status from societyMaster sm
					left join cityMaster cm on sm.city_id = cm.cityID and cm.status = 1
					left join zones z on sm.zone_id = z.zone_id and z.status = 1
					left join countries c on sm.countries_id = c.countries_id and c.status = 1
					where sm.status = 1");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "societyID:" . $res['societyID'];
		$societyName = '<td>' . ucfirst($res['societyName']) . '</td>';

		if ($res['societyLogo'] == null || empty($res['societyLogo'])) {
			$res['societyLogo'] = "https://cdn.ggate.app/ggateweb/assets/images/logo-1.svg";
		}
		$societyLogo = '<td><img src="' . $res['societyLogo'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$societyAddress = '<td>' . ucfirst($res['societyAddress']) . '</td>';
		$societyContactNo = '<td>' . $res['societyContactNo'] . '</td>';
		$societyEmail = '<td>' . $res['societyEmail'] . '</td>';
		$cityName = '<td>' . $res['cityName'] . '</td>';
		$stateName = '<td>' . $res['stateName'] . '</td>';
		$countryName = '<td>' . $res['countryName'] . '</td>';
		$maxBlocks = '<td>' . $res['maxBlocks'] . '</td>';
		$maxProperties = '<td>' . $res['maxProperties'] . '</td>';
		$enrolledDate = '<td>' . $res['enrolledDate'] . '</td>';
		$validUptoDate = '<td>' . $res['validUptoDate'] . '</td>';

		if (isset($res['status'])) {
			if ($res['status'] == 1) {
				$status = '<td><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></td>';
			} else {
				$status = '<td><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></td>';
			}
		} else {
			$status = '<td><span class="badge badge-secondary">' . "Not Available" . '</td>';
		}

		// $action = '<td><a href="index.php?controller=masters&action=societymanagement&subaction=editForm&societyID=' . $res['societyID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;
		// 		<a href="index.php?controller=masters&action=societymanagement&subaction=delete&societyID=' . $res['societyID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>&nbsp;
		// 		<a href="index.php?controller=masters&action=societymanagement&subaction=societyInfoDetails&societyID=' . $res['societyID'] . '" title="Info" ><i class="fas fa-info-circle text-success"></i></a>
		// 		</td>';
		$action = '<td><a href="index.php?controller=masters&action=societymanagement&subaction=societyInfoDetails&societyID=' . $res['societyID'] . '" title="Info"><i class="fas fa-info-circle text-info"></i></a></td>';
		$result['aaData'][] = array("$societyLogo", "$societyName", "$societyAddress", "$cityName", "$stateName", "$countryName", "$societyContactNo", "$societyEmail", "$status", "$action");
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