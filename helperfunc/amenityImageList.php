<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$queryString = pro_db_query("select ai.*, am.assetTitle from amenityImage ai
                                    join amenityMaster am on ai.assetID = am.assetID
                                    where am.complexID =" . $_SESSION['complexID'] . " group by ai.assetID");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "imageID:" . $res['imageID'];
		$imageID = '<td>' . $res['imageID'] . '</td>';
		$assetTitle = '<td>' . $res['assetTitle'] . '</td>';
		$queryImage = pro_db_query("select imageID, assetImage from amenityImage where assetID = " . $res['assetID']);
		$assetsImage = '<td>';
		while ($resImage = pro_db_fetch_array($queryImage)) {
			$assetsImage .= '<div class="ggate-img-wraps">';
			$assetsImage .= '<span class="closes" title="Delete">
							<a href="index.php?controller=amenities&action=amenityimage&subaction=delete&imageID=' . $resImage['imageID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
							</i></span>';
			$assetsImage .= '<img src="' . $resImage['assetImage'] . '" class="img-fluid" style="height : 150px ; width : 150px;border-radius:10px;margin:0.8rem;">';
			$assetsImage .= '</div>';
		}
		$assetsImage .= '</td>';
		$Action = '<td><a href="index.php?controller=amenities&action=amenityimage&subaction=editForm&imageID=' . $res['imageID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> |
                    <a href="index.php?controller=amenities&action=amenityimage&subaction=delete&imageID=' . $res['imageID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
                    </td>';
		$result['aaData'][] = array("$assetTitle", "$assetsImage");
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