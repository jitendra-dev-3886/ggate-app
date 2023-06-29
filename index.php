<?php
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
include "lib/page_functions.php";
include "lib/seo_functions.php";

//TODO: Comment this code to use for localhost
//Code - Start
include "vendor/autoload.php";

use Google\Cloud\Storage\StorageClient;

$storage = new StorageClient([
	'keyFile' => json_decode($gcloudPrivateKey, true)
]);
//Code - End

if (defined('ADMIN_ALLOWED') == true) {
	$_SESSION['gPermissions'] = getMasterList("permissionMaster", "moduleName", "moduleName", 'userID = ' . $_SESSION['userID']);
	$permission = 1;
	if (isset($_REQUEST['controller']) && $_REQUEST['controller'] != "") {
		$controller = $_REQUEST['controller'];
		$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != '') ? $_REQUEST['action'] : '';
		$subaction = (isset($_REQUEST['subaction']) && $_REQUEST['subaction'] != '') ? $_REQUEST['subaction'] : 'listData';
		$moduleHeader = getMasterList("moduleMaster", "0", "moduleTitle", "moduleFile = '" . $action . "'")[0];
		if (in_array($action, $_SESSION['gPermissions']) or $_SESSION['groupID'] == 1  or $_SESSION['groupID'] == 5) {
			# Include corresponding class based on action
			if (strlen($action) > 2) {
				$content_include = DIR_WS_CONTROLLER_PATH . $controller . '/index.php';
			} else {
				$moduleHeader = "GGATE Dashboard";
				$content_include = DIR_WS_CONTROLLER_PATH . '/index.php';
			}
		} else {
			$permission = 0;
			$moduleHeader = "Access Denied";
			$content_include = DIR_WS_CONTROLLER_PATH . '/index.php';
		}
	}
	if (!isset($_REQUEST['controller']) or $_REQUEST['controller'] == "") {
		$moduleHeader = "GGATE Dashboard";
		$content_include = DIR_WS_CONTROLLER_PATH . 'index.php';
	}
	if (empty($moduleHeader) || ($moduleHeader == "No Value Defined..")) {
		$moduleHeader = "GGATE Dashboard";
	}
	include WS_ADMIN_CONFIG . 'layout.php';
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>
