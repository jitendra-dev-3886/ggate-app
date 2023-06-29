<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$queryString = pro_db_query("select ci.*, cm.clubTitle from clubImage ci
                                join clubMaster cm on ci.clubID = cm.clubID
                                where cm.societyID =" . $_SESSION['societyID'] . " group by ci.clubID");
	while ($res = pro_db_fetch_array($queryString)) {
		$pk = "imageID:" . $res['imageID'];
		$imageID = '<td>' . $res['imageID'] . '</td>';
		$clubTitle = '<td>' . $res['clubTitle'] . '</td>';
		$queryImage = pro_db_query("select imageID, clubImage from clubImage where clubID = " . $res['clubID']);
		$clubImage = '<td>';
		while ($resImage = pro_db_fetch_array($queryImage)) {
			$clubImage .= '<div class="ggate-img-wraps">';
			$clubImage .= '<span class="closes" title="Delete">
							<a href="index.php?controller=amenities&action=clubimage&subaction=delete&imageID=' . $resImage['imageID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
							</i></span>';
			$clubImage .= '<img src="' . $resImage['clubImage'] . '" class="img-fluid" style="height : 150px ; width : 150px;border-radius:10px;margin:0.8rem;">';
			$clubImage .= '</div>';
		}
		$clubImage .= '</td>';
		$Action = '<td><a href="index.php?controller=amenities&action=clubimage&subaction=editForm&imageID=' . $res['imageID'] . '" title="Edit"><i class="fe-edit text-info"></i></a> |
                    <a href="index.php?controller=amenities&action=clubimage&subaction=delete&imageID=' . $res['imageID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
                    </td>';
		// $result['aaData'][] = array("$imageID", "$assetTitle", "$assetsImage", "$Action");
		$result['aaData'][] = array("$clubTitle", "$clubImage");
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