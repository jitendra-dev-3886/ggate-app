<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {

	$categorySQL = "";
	$categoryID = $_REQUEST['categoryID'];
	if ($categoryID != null && !empty($categoryID)) {
		$categorySQL = " and vm.categoryID = " . $categoryID;
		print_r($categorySQL);
	}

	$result = array('aaData' => array());
	// Same from main controller File
	$statusArray = array("0" => "Inactive", "1" => "Active");
	$queryString = pro_db_query("select vm.*, cm.categoryTitle, ct.cityName, z.zone_name, c.countries_name from vendorMaster vm 
								left join categoryMaster cm on vm.categoryID = cm.categoryID 
								left join cityMaster ct on vm.city_id = ct.cityID 
								left join zones z on vm.zone_id = z.zone_id 
								left join countries c on vm.countries_id = c.countries_id 
								where vm.vendorTypeID = 7 " . $categorySQL . " order by cm.categoryTitle");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "vendorID:" . $res['vendorID'];
		$categoryTitle = '<td>' . $res['categoryTitle'] . '</td>';

		if ($res['vendorImage'] == null || empty($res['vendorImage'])) {
			$res['vendorImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
		}
		$vendorImage = '<td><center><img src="' . $res['vendorImage'] . '"style="height : 80px ; width : 200px;" class="img-fluid"></td></center>';
		$vendorName = '<td>' . $res['vendorName'] . '</td>';
		$vendorAddress = '<td>' . $res['vendorAddress'] . '</td>';
		$vendorCity = '<td>' . $res['vendorCity'] . '</td>';
		$authorisedPerson = '<td>' . $res['authorisedPerson'] . '</td>';
		if (!empty($res['vendorPhone'])) {
			$vendorMobile = '<td>' . $res['vendorPhone'] . '</td>';
		} else {
			$vendorMobile = '<td>' . $res['vendorMobile'] . '</td>';
		}
		
		if ($res['status'] == 1) {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></a></td>';
		} else {
			$status = '<td><a href="#" class="estatus" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status"><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></a></td>';
		}

		$querySociety = pro_db_query("select count(complexID) as enrolledSociety from vendorComplexMapping where vendorID = " . $res['vendorID']);
		$resSociety = pro_db_fetch_array($querySociety);
		if ($resSociety["enrolledSociety"] > 0) {
			$Action = '<td><a href="index.php?controller=ggatemasters&action=vendormasters&subaction=editForm&vendorID=' . $res['vendorID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=ggatemasters&action=vendormasters&subaction=vendorSocietyMappingList&vendorID=' . $res['vendorID'] . '" title="Complex Mapping" ><i class="fas fa-clipboard-list text-warning"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=ggatemasters&action=vendormasters&subaction=notifySocietyAboutVendor&vendorID=' . $res['vendorID'] . '" title="Notify Complex"><i class="fas fa-bullhorn text-danger"></i></a>
					</td>';
		} else {
			$Action = '<td><a href="index.php?controller=ggatemasters&action=vendormasters&subaction=editForm&vendorID=' . $res['vendorID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=ggatemasters&action=vendormasters&subaction=vendorSocietyMappingList&vendorID=' . $res['vendorID'] . '" title="Complex Mapping" ><i class="fas fa-clipboard-list text-warning"></i></a>
					</td>';
		}

		$result['aaData'][] = array("$categoryTitle", "$vendorImage", "$vendorName", "$vendorAddress", "$vendorCity", "$authorisedPerson", "$vendorMobile", "$status", "$Action");
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