<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from sosTypeMaster where status = 1");
	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['status'] == '1') {
			$status = "Active";
		} else {
			$status = "Inactive";
		}

		$pk = "sosTypeID:" . $res['sosTypeID'];
		$thumb_image = HTTP_SERVER . WS_ROOT . "timthumb.php?src=" . DIR_WS_BLOG_PATH . $res['sosImage'] . "&w=100&h=67&zc=0";
		$sosSelectedImage = '<td><center><img src="' . $res['sosSelectedImage'] . '"style="height : 80px ; width : 80px;" class="img-fluid"></td></center>';
		$sosType = '<td>' . $res['sosTypeTitle'] . '</td>';
		$sosImage = '<td><center><img src="' . $res['sosImage'] . '"style="height : 80px ; width : 80px;" class="img-fluid"></td></center>';
		$sendSMS = '<td>' . $res['sendSMS'] . '</td>';
		$sendOBD = '<td>' . $res['sendOBD'] . '</td>';
		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$Action = '<td><a href="index.php?controller=ggatemasters&action=sostypemaster&subaction=editForm&sosTypeID=' . $res['sosTypeID'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=ggatemasters&action=sostypemaster&subaction=delete&sosTypeID=' . $res['sosTypeID'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$sosType", "$sosImage", "$sosSelectedImage", "$sendSMS", "$sendOBD", "$status", "$Action");
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