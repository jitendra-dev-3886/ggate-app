<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
	$result = array('aaData' => array());
	// Same from main controller File
	$queryString = pro_db_query("SELECT * from module_master order by module_parent, sortorder");
	while ($res = pro_db_fetch_array($queryString)) {

		if ($res['status'] == 'E') {
			$status = "Active";
		} else {
			$status = "Inactive";
		}

		$pk = "module_id:" . $res['module_id'];

		$module_title = '<td>' . $res['module_title'] . '</td>';
		$module_parent = '<td>' . getfldValue('module_master', 'module_id', $res['module_parent'], 'module_title') . '</td>';
		$module_icon = '<td>' . $res['module_icon'] . '</td>';
		$module_file = '<td>' . $res['module_file'] . '</td>';
		$module_show_menu = '<td>' . $res['module_show_menu'] . '</td>';
		$module_private = '<td>' . $res['module_private'] . '</td>';
		$module_static = '<td>' . $res['module_static'] . '</td>';
		$module_status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $status . '</a></td>';
		$module_sortorder = '<td><a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $res['sortorder'] . '</a></td>';

		$Action = '<td><a href="index.php?controller=module&action=module&subaction=editForm&module_id=' . $res['module_id'] . '" title="Edit"><i class="fe-edit text-info"></i></a>&nbsp;&nbsp;<a href="index.php?controller=module&action=module&subaction=delete&module_id=' . $res['module_id'] . '" title="Delete"><i class="fe-trash-2 text-danger"></i></a>
					</td>';
		$result['aaData'][] = array("$module_title", "$module_parent", "$module_icon", "$module_file", "$module_show_menu", "$module_private", "$module_static", "$module_status", "$module_sortorder", "$Action");
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