<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from bannerMaster");
	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['status'] == '1') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}

		$pk = "bannerId:" . $res['bannerId'];
		$thumb_image = HTTP_SERVER . WS_ROOT . "timthumb.php?src=" . DIR_WS_BLOG_PATH . $res['bannerImage'] . "&w=100&h=67&zc=0";
		$bannerTitle = '<td>' . $res['bannerTitle'] . '</td>';
		$bannerURL = '<td>' . $res['bannerURL'] . '</td>';
		$bannerImage = '<td><img src="' . $thumb_image . '"></td>';

		$status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';

		$Action = '<td><a href="index.php?controller=banner&action=banner&subaction=editForm&bannerId=' . $res['bannerId'] . '" title="Edit" ><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;
					<a href="index.php?controller=banner&action=banner&subaction=delete&bannerId=' . $res['bannerId'] . '" title="Delete" ><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$bannerTitle", "$bannerURL", "$bannerImage", "$status", "$Action");
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