<?php
if (defined('ADMIN_ALLOWED') == true) {
	include($action . ".php");
	$listUrl = "index.php?controller=$controller&action=$action";
	$proClass = new $action($controller, $action, $listUrl);
	$proClass->$subaction();
} else {
?>
	<script>
		location.href = "login.php";
	</script>
<?php
}
?>