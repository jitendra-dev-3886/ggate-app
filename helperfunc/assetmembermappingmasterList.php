<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$amenityID = $_REQUEST['amenityID'] ?? 0;
	$allowMembershipArray = array("1" => "Allowed", "0" => "Restricted");

	$queryAsset = pro_db_query("SELECT assetTitle FROM amenityMaster WHERE status = 1 and societyID = " . $_SESSION['societyID'] . " and assetID = " . $amenityID);
	$resAsset = pro_db_fetch_array($queryAsset);
	$assetTitle = '<td>' . $resAsset['assetTitle'] . '</td>';

	$queryMembers = pro_db_query("SELECT mem.memberID, mem.memberName, mem.memberMobile, mem.memberImage, 
								blk.blockName, bffm.flatID, bffm.floorNo, bffm.flatNumber, bffm.status
								FROM blockFloorFlatMapping bffm 
								left join memberMaster mem on bffm.memberID = mem.memberID 
								left join blockMaster blk on bffm.blockID = blk.blockID 
								WHERE bffm.status = 1 and bffm.societyID = " . $_SESSION['societyID'] . " 
								order by blk.blockName, bffm.floorNo, bffm.flatNumber");
	while ($res = pro_db_fetch_array($queryMembers)) {
		$memberName = '<td>' . $res['memberName'] . '</td>';
		if ($res['memberImage'] == null || empty($res['memberImage'])) {
			$res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$flatID = $res['flatID'];
		$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['flatNumber'] . '</td>';

		//Mapping
		$queryMapping = pro_db_query("SELECT assetMappingID, allowMembership, amount FROM assetMemberMapping
									WHERE status = 1 and societyID = " . $_SESSION['societyID'] . " 
									and flatID = " . $res['flatID'] . " and assetID = " . $amenityID . " 
									ORDER BY modifieddate desc limit 1");
		$totalMapping = pro_db_num_rows($queryMapping);
		if ($totalMapping > 0) {
			while ($resMapping = pro_db_fetch_array($queryMapping)) {
				$assetMappingID = $resMapping['assetMappingID'];
				$pk = "assetMappingID:" . $assetMappingID;
				$amount = '<td>' . $resMapping['amount'] . '</td>';

				$isAllowed = $resMapping['allowMembership'];
				if ($isAllowed == 1) {
					$allowMembership = '<td><span class="badge badge-info">' . $allowMembershipArray[$isAllowed] . '</span></td>';
					$Action = '<td><a title="Restrict Access"
								href="index.php?controller=amenities&action=assetmembermappingmaster&subaction=restrictAccess&assetMappingID=' . $assetMappingID . '&assetID=' . $amenityID . '&flatID=' . $flatID . '">
								<i class="fas fa-user-minus text-danger"></i></a></td>';
				} else {
					$allowMembership = '<td><span class="badge badge-danger">' . $allowMembershipArray[$isAllowed] . '</span></td>';
					$Action = '<td><a title="Allow Access"
								href="index.php?controller=amenities&action=assetmembermappingmaster&subaction=allowAccess&assetMappingID=' . $assetMappingID . '&assetID=' . $amenityID . '&flatID=' . $flatID . '">
								<i class="fas fa-user-plus text-success"></i></a></td>';
				}
			}
		} else {
			$assetMappingID = 0;
			$pk = "assetMappingID:" . $assetMappingID;
			$amount = '<td>0</td>';

			$isAllowed = 1;
			$allowMembership = '<td><span class="badge badge-info">' . $allowMembershipArray[$isAllowed] . '</span></td>';

			$Action = '<td><a title="Restrict Access"
						href="index.php?controller=amenities&action=assetmembermappingmaster&subaction=restrictAccess&assetMappingID=' . $assetMappingID . '&assetID=' . $amenityID . '&flatID=' . $flatID . '">
						<i class="fas fa-user-minus text-danger icon-sm"></i></a></td>';
		}
		$result['aaData'][] = array("$flatNumber", "$memberImage", "$memberName", "$assetTitle", "$amount", "$allowMembership", "$Action");
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