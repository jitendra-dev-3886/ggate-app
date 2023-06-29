<?php
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

$insertsession = pro_db_query("Insert into sessionLogMaster (userID,loginID,remote_ip,last_access,status) values('" . $_SESSION['userID'] . "','" . $_SESSION['loginID'] . "','" . $_SERVER['REMOTE_ADDR'] . "',now(),'O')");
session_destroy();

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>GGATE Dashboard</title>
	<!-- CSS Files -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/login.css" rel="stylesheet" rel="stylesheet" type="text/css">

	<!-- JS Files -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		jQuery(function(r, s) {
			$(".logoff_message").show(3000);
			$(".logoff_message").fadeOut(4000);
		});
	</script>
</head>
<div class="login adaptable">
	<div class="login__block toggled">
		<div class="login__block__header">
			<img src="assets/images/ggate_logo_full.svg">
		</div>
		<div class="login__block__body text-dark">
			<p class="py-3 mb-2"><?php echo '<strong>Last Login From :</strong>&nbsp;&nbsp;' . last_login_from(); ?></p>
			<a href="login.php" class="btn btn--light text-info">Click here to log in again</a>
		</div>
	</div>
</div>
</body>

</html>