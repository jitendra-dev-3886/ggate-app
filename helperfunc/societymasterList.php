<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$companyID = 0;
	$apiSurveyStatus = 0;

	$requestData = $_REQUEST;
	$columns = array('societyID', 'societyName', 'societyLogo', 'societyAddress', 'societyCity', 'countries_name', 'zone_name', 'noOfProperty', 'societyContactNo', 'societyEmail');

	/* Now Build Where Condition */
	$whr = "";

	$totalQuery = pro_db_query("SELECT st.*, z.zone_name, cc.countries_name FROM societyMaster st left join zones z on st.zone_id = z.zone_id left join countries cc on st.countries_id = cc.countries_id");
	$totalRecords = pro_db_num_rows($totalQuery);
	$recordsFiltered = $totalRecords;
	$queryString = "SELECT st.*, z.zone_name, cc.countries_name FROM societyMaster st left join zones z on st.zone_id = z.zone_id left join countries cc on st.countries_id = cc.countries_id";

	$queryResult = pro_db_query($queryString);
	$recordsFiltered = pro_db_num_rows($queryResult);
	if ($totalRecords > 50 && $requestData['length'] > 0) {
		$queryString .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . " LIMIT " . (int)$requestData['start'] . " ," . (int)$requestData['length'] . "   ";
	} else {
		$queryString .= " limit 0,50  ";
	}
	$finalResult = pro_db_query($queryString);
	$data = array();
	$thumb_image = HTTP_SERVER . WS_ROOT . "timthumb.php?src=" . DIR_WS_BLOG_PATH . $res['societyLogo'] . "&w=100&h=67&zc=0";
	$status = "";
	$astatus = "";
	while ($res = pro_db_fetch_array($finalResult)) {
		$nestedData = array();

		if ($res['status'] == '1') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}

		$pk = "societyID:" . $res['societyID'];

		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$Action = '<td><a href="index.php?controller=societymaster&action=complexmasters&subaction=editForm&societyID=' . $res['societyID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;
				<a href="index.php?controller=complexmasters&action=societymaster&subaction=delete&societyID=' . $res['societyID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>&nbsp;
				<a href="index.php?controller=complexmasters&action=societymaster&subaction=societyInfo&societyID=' . $res['societyID'] . '" title="Delete" ><i class="fe-trash-2 text-success"></i></a>
				</td>';
		$societyLogo = '<td><img src="' . $thumb_image . '"></td>';

		$nestedData[] = $res['societyName'];
		$nestedData[] = $societyLogo;
		$nestedData[] = $res['societyAddress'];
		$nestedData[] = $res['societyCity'];
		$nestedData[] = $res['countries_name'];
		$nestedData[] = $res['zone_name'];
		$nestedData[] = $res['societyContactNo'];
		$nestedData[] = $res['societyEmail'];
		$nestedData[] = $status;
		$nestedData[] = $Action;
		$data[] = $nestedData;
	}
	// End While Loop

	$result = array(
		"draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal"    => intval($totalRecords),  // total number of records
		"recordsFiltered" => intval($recordsFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data"            => $data   // total data array
	);
	echo json_encode($result);
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>