<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	$queryString = pro_db_query("select fq.*, fqm.faqType from faq fq
								join faqMaster fqm on fqm.faqTypeID = fq.faqTypeID
								order by fqm.sortorder");
	while ($res = pro_db_fetch_array($queryString)) {

		$statusArray = array("0" => "Inactive", "1" => "Active");
		$pk = "faqID:" . $res['faqID'];

		if ($res['status'] == 1) {
			$faqStatus = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		} else {
			$faqStatus = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
		}

		$faqTitle = '<td>' . $res['faqTitle'] . '</td>';
		$faqType = '<td>' . $res['faqType'] . '</td>';
		$faqDescription = '<td>' . $res['faqDescription'] . '</td>';
		$link = '<td>'. $res['link'] .'</td>';

		$action = '<td><a href="index.php?controller=ggatemasters&action=faqmaster&subaction=editForm&faqID=' . $res['faqID'] . '" title="Edit" ><i class="fe-edit text-warning"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
						<a href="index.php?controller=ggatemasters&action=faqmaster&subaction=delete&faqID=' . $res['faqID'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$faqType", "$faqTitle", "$faqDescription", "$link", "$faqStatus", "$action");
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