<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$statusArray = array("0" => "Pending", "1" => "Active", "2" => "Rejected");
	$occupationTypeArray = array("1" => "Owner", "2" => "Tenant", "3" => "Looking for Tenant");
	$flatMaintenanceTypeArray = array("0" => "Monthly", "1" => "Quaterly", "2" => "Half yearly", "3" => "Yearly");

	$queryString = pro_db_query("SELECT f.*, b.blockName, o.officeName, m.memberName, fim.type as flatInvoiceType, sms.maintenanceType as complexInvoiceType,
								cas.isManually, sms.squareFeetArea, 
								case 
								when (f.occupationType = 1 and f.officeMaintenanceAmt = 0.00) then sms.ownerAmount 
								when (f.occupationType = 2 and f.officeMaintenanceAmt = 0.00) then sms.rentalAmount 
								else f.officeMaintenanceAmt end as flatMaintenanceAmount
								FROM blockFloorOfficeMapping f
								join blockMaster b on f.blockID = b.blockID
								join officeMaster o on f.officeID = o.officeID
								left join complexMaintenanceSettings sms on f.blockID = sms.blockID and f.complexID = sms.complexID 
                                left join complexAccountSettings cas on cas.complexID = f.complexID
								join memberMaster m on f.memberID = m.memberID
								left join flatInvoiceMapping fim on f.officeMappingID = fim.officeMappingID
								join complexMaster sm on sm.complexID = f.complexID
								where f.status = 1 and f.complexID = " . $_SESSION['complexID'] . " order by b.blockName, f.floorNo, cast(f.officeNumber as unsigned)");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "officeMappingID:" . $res['officeMappingID'];
		$memberName = '<td>' . $res['memberName'] . '</td>';
		$blockName = '<td>' . $res['blockName'] . '</td>';
		$floorNo = '<td>' . $res['floorNo'] . '</td>';
		$officeNumber = '<td>' . $res['officeNumber'] . '</td>';
		$officeName = '<td>' . $res['officeName'] . '</td>';
		$squareFeetArea = $res['officeArea'];
		if ($squareFeetArea == null || empty($squareFeetArea) || $squareFeetArea == 0.0) {
			$squareFeetArea = $res['squareFeetArea'];
		}
		$flatArea = '<td>' . $squareFeetArea . '</td>';
		$flatMaintenanceAmt = '<td>' . $res['officeMaintenanceAmt'] . '</td>';
		if ($res['isManually'] > 0) {
			if (isset($res['flatInvoiceType'])) {
				$flatMaintenanceType = '<td>' . $flatMaintenanceTypeArray[$res['flatInvoiceType']] . '</td>';
			} else {
				$flatMaintenanceType = '<td>' . "-" . '</td>';
			}
		} else {
			$flatMaintenanceType = '<td>' . $flatMaintenanceTypeArray[$res['complexInvoiceType']] . '</td>';
		}
		if ($res['occupationType'] == 1) {
			$occupationType = '<td><i class="badge badge-info">' . $occupationTypeArray[$res['occupationType']] . '</i></td>';
		} else if ($res['occupationType'] == 2) {
			$occupationType = '<td><i class="badge badge-danger">' . $occupationTypeArray[$res['occupationType']] . '</i></td>';
		} else {
			$occupationType = '<td><i class="badge badge-secondary">' . $occupationTypeArray[$res['occupationType']] . '</i></td>';
		}
		$flatStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		if ($res['status'] == 2) {
			$Action = '<td><a href="index.php?controller=complexmasters&action=flatmapping&subaction=editForm&officeMappingID=' . $res['officeMappingID'] . '" title="Edit"><i class="fe-edit text-primary"></i></a>&nbsp;&nbsp;
				 <a href="index.php?controller=complexmasters&action=flatmapping&subaction=delete&officeMappingID=' . $res['officeMappingID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a></td>';
		} else if ($res['status'] == 1) {
			if ($res['isManually'] > 0) {
				$Action = '<td><a href="index.php?controller=complexmasters&action=flatmapping&subaction=editForm&officeMappingID=' . $res['officeMappingID'] . '" title="Edit"><i class="fe-edit text-primary"></i></a>&nbsp;&nbsp;
							<a href="index.php?controller=complexmasters&action=flatmapping&subaction=generateInvoiceForm&officeMappingID=' . $res['officeMappingID'] . '" title="Invoice"><i class="fas fa-rupee-sign text-danger"></i>&nbsp;&nbsp;
							<a href="index.php?controller=complexmasters&action=flatmapping&subaction=transferFlatForm&officeMappingID=' . $res['officeMappingID'] . '" title="Tranfer Property"><i class="fas fa-exchange-alt text-warning"></i></a></td>';
			} else {
				$Action = '<td><a href="index.php?controller=complexmasters&action=flatmapping&subaction=editForm&officeMappingID=' . $res['officeMappingID'] . '" title="Edit"><i class="fe-edit text-primary"></i></a>&nbsp;&nbsp;
				<a href="index.php?controller=complexmasters&action=flatmapping&subaction=transferFlatForm&officeMappingID=' . $res['officeMappingID'] . '" title="Tranfer Property"><i class="fas fa-exchange-alt text-warning"></i></a></td>';
			}
		} else {
			$Action = '<td><a href="index.php?controller=complexmasters&action=flatmapping&subaction=editForm&officeMappingID=' . $res['officeMappingID'] . '" title="Edit"><i class="fe-edit text-primary"></i></a></td>';
		}
		$result['aaData'][] = array("$blockName", "$floorNo", "$officeNumber", "$officeName", "$memberName", "$flatArea", "$flatMaintenanceAmt", "$occupationType", "$flatStatus", "$Action");
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