<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$statusArray = array("0" => "Inactive", "1" => "Active");

	$queryString = pro_db_query("SELECT * from noticeMaster where status != 126 and officeID = 0 and complexID = " . $_SESSION['complexID'] . " order by noticeID desc");

	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "noticeID:" . $res['noticeID'];
	
		$date = date('d M Y', strtotime($res['createdate']));
		$time = date('H:i', strtotime($res['createdate']));
		$datetime = nl2br($date . " \n " . $time);
		$noticeDate = '<td>' . $date . '</td>';

		if ($res['noticeImage'] == null || empty($res['noticeImage'])) {
			$res['noticeImage'] = "https://cdn.ggate.app/icons/ico_amenity_placeholder.png";
		}
		$noticeImage = '<td><img src="' . $res['noticeImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';

		$noticeTitle = '<td "style="width: 80px;">' . $res['title'] . '</td>';
		$noticeDescription = '<td "style="width : 100px;">' . $res['noticeDescription'] . '</td>';
		$noticeStatus = '<td><a href="#"  class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';

		$Action = '<td><a href="index.php?controller=community&action=notice&subaction=editForm&noticeID=' . $res['noticeID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=community&action=notice&subaction=delete&noticeID=' . $res['noticeID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$noticeDate", "$noticeTitle", "$noticeDescription", "$noticeImage", "$noticeStatus", "$Action");
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
