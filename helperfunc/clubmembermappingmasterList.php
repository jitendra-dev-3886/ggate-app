<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$clubID = $_REQUEST['clubID'] ?? 0;
	$allowMembershipArray = array("1" => "Allowed", "0" => "Restricted");

	$queryClub = pro_db_query("SELECT clubTitle FROM clubMaster WHERE status = 1 and societyID = " . $_SESSION['societyID'] . " and clubID = " . $clubID);
	$resClub = pro_db_fetch_array($queryClub);
	$clubTitle = '<td>' . $resClub['clubTitle'] . '</td>';

	$queryMembers = pro_db_query("SELECT mem.memberID, mem.memberName, mem.memberMobile, mem.memberImage, 
								blk.blockName, bffm.flatID, bffm.floorNo, bffm.flatNumber, bffm.status
								FROM memberMaster mem 
								join blockFloorFlatMapping bffm on (mem.memberID = bffm.memberID or mem.parentID = bffm.memberID) 
								and bffm.status = 1 and bffm.isPrimary = 1
								join blockMaster blk on bffm.blockID = blk.blockID 
								WHERE bffm.societyID = " . $_SESSION['societyID'] . " 
								group by mem.memberID order by blk.blockName, bffm.floorNo, bffm.flatNumber, mem.parentID");
	while ($res = pro_db_fetch_array($queryMembers)) {
		$memberName = '<td>' . $res['memberName'] . '</td>';
		if ($res['memberImage'] == null || empty($res['memberImage'])) {
			$res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$memberID = $res['memberID'];
		$flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['flatNumber'] . '</td>';

		//Mapping
		$queryMapping = pro_db_query("SELECT clubMappingID, allowMembership, amount FROM clubMemberMapping
									WHERE (status = 1 or status = 0) and societyID = " . $_SESSION['societyID'] . " 
									and clubID = " . $clubID . " and memberID = " . $memberID . " 
									ORDER BY modifieddate desc limit 1");
		$totalMapping = pro_db_num_rows($queryMapping);
		if ($totalMapping > 0) {
			while ($resMapping = pro_db_fetch_array($queryMapping)) {
				$clubMappingID = $resMapping['clubMappingID'];
				$pk = "clubMappingID:" . $clubMappingID;
				$amount = '<td>' . $resMapping['amount'] . '</td>';

				$isAllowed = $resMapping['allowMembership'];
				if ($isAllowed == 1) {
					$allowMembership = '<td><span class="badge badge-info">' . $allowMembershipArray[$isAllowed] . '</span></td>';
					$Action = '<td><a title="Restrict Access"
								href="index.php?controller=amenities&action=clubmembermappingmaster&subaction=restrictAccess&clubMappingID=' . $clubMappingID . '&clubID=' . $clubID . '&memberID=' . $memberID . '">
								<i class="fas fa-user-minus text-danger"></i></a></td>';
				} else {
					$allowMembership = '<td><span class="badge badge-danger">' . $allowMembershipArray[$isAllowed] . '</span></td>';
					$Action = '<td><a title="Allow Access"
								href="index.php?controller=amenities&action=clubmembermappingmaster&subaction=allowAccess&clubMappingID=' . $clubMappingID . '&clubID=' . $clubID . '&memberID=' . $memberID . '">
								<i class="fas fa-user-plus text-success"></i></a></td>';
				}
			}
		} else {
			$clubMappingID = 0;
			$pk = "clubMappingID:" . $clubMappingID;
			$amount = '<td>0</td>';

			$isAllowed = 1;
			$allowMembership = '<td><span class="badge badge-info">' . $allowMembershipArray[$isAllowed] . '</span></td>';

			/* data - url = "<?php echo $this->redirectUrl; ?> */
			$Action = '<td><a title="Restrict Access"
						href="index.php?controller=amenities&action=clubmembermappingmaster&subaction=restrictAccess&clubMappingID=' . $clubMappingID . '&clubID=' . $clubID . '&memberID=' . $memberID . '">
						<i class="fas fa-user-minus text-danger icon-sm"></i></a></td>';
		}
		$result['aaData'][] = array("$flatNumber", "$memberImage", "$memberName", "$clubTitle", "$amount", "$allowMembership", "$Action");

		$queryFamilyMembers = pro_db_query("SELECT memberID, memberName, memberMobile, memberImage FROM memberMaster
																	WHERE  parentID = " . $memberID . " order by memberName");
		while ($resFamilyMember = pro_db_fetch_array($queryFamilyMembers)) {
			$memberName = '<td>' . $resFamilyMember['memberName'] . '</td>';
			if ($resFamilyMember['memberImage'] == null || empty($resFamilyMember['memberImage'])) {
				$resFamilyMember['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
			}
			$memberImage = '<td><img src="' . $resFamilyMember['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

			$memberID = $resFamilyMember['memberID'];
			$flatNumber = '<td></td>';

			//Mapping
			$queryMapping = pro_db_query("SELECT clubMappingID, allowMembership, amount FROM clubMemberMapping
									WHERE status = 1 and societyID = " . $_SESSION['societyID'] . " 
									and clubID = " . $clubID . " and memberID = " . $memberID . " 
									ORDER BY modifieddate desc limit 1");
			$totalMapping = pro_db_num_rows($queryMapping);
			if ($totalMapping > 0) {
				while ($resMapping = pro_db_fetch_array($queryMapping)) {
					$clubMappingID = $resMapping['clubMappingID'];
					$pk = "clubMappingID:" . $clubMappingID;
					$amount = '<td>' . $resMapping['amount'] . '</td>';

					$isAllowed = $resMapping['allowMembership'];
					if ($isAllowed == 1) {
						$allowMembership = '<td><span class="badge badge-info">' . $allowMembershipArray[$isAllowed] . '</span></td>';
						$Action = '<td><a title="Restrict Access"
								href="index.php?controller=amenities&action=clubmembermappingmaster&subaction=restrictAccess&clubMappingID=' . $clubMappingID . '&clubID=' . $clubID . '&memberID=' . $memberID . '">
								<i class="fas fa-user-minus text-danger"></i></a></td>';
					} else {
						$allowMembership = '<td><span class="badge badge-danger">' . $allowMembershipArray[$isAllowed] . '</span></td>';
						$Action = '<td><a title="Allow Access"
								href="index.php?controller=amenities&action=clubmembermappingmaster&subaction=allowAccess&clubMappingID=' . $clubMappingID . '&clubID=' . $clubID . '&memberID=' . $memberID . '">
								<i class="fas fa-user-plus text-success"></i></a></td>';
					}
				}
			} else {
				$clubMappingID = 0;
				$pk = "clubMappingID:" . $clubMappingID;
				$amount = '<td>0</td>';

				$isAllowed = 1;
				$allowMembership = '<td><span class="badge badge-info">' . $allowMembershipArray[$isAllowed] . '</span></td>';

				/* data - url = "<?php echo $this->redirectUrl; ?> */
				$Action = '<td><a title="Restrict Access"
						href="index.php?controller=amenities&action=clubmembermappingmaster&subaction=restrictAccess&clubMappingID=' . $clubMappingID . '&clubID=' . $clubID . '&memberID=' . $memberID . '">
						<i class="fas fa-user-minus text-danger icon-sm"></i></a></td>';
			}
			$result['aaData'][] = array("$flatNumber", "$memberImage", "$memberName", "$clubTitle", "$amount", "$allowMembership", "$Action");
		}
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