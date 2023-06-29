<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from contact order by sortorder");
	while ($res = pro_db_fetch_array($queryString)) {
		if ($res['status'] == 'E') {
			$status = "Active";
		} else {
			$status = "Disabled";
		}
		$pk = "contactID:" . $res['contactID'];
		$select = '<td><center><input type="checkbox" class="case" id="' . $res['contactID'] . '"></center></a></td>';
		$contactTitle = '<td>' . $res['contactName'] . '</td>';
		$contactEmail = '<td>' . $res['contactEmail'] . '</td>';
		$contactMobile = '<td>' . $res['contactMobile'] . '</td>';
		$contactSubject = '<td>' . $res['contactSubject'] . '</td>';
		$contactMessage = '<td>' . $res['contactMessage'] . '</td>';

		$Action = '<td><a href="index.php?controller=contact&action=contact&subaction=delete&contactID=' . $res['contactID'] . '" data-toggle="tooltip" title="Delete Records" class="btn btn-danger" onClick="return confirmSubmit();" ><i class="fa fa-times"></i></a></td>';
		$result['aaData'][] = array("$select", "$contactTitle", "$contactEmail", "$contactMobile", "$contactSubject", "$contactMessage", "$Action");
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