<?php
include "config/config.php";
include "lib/base.php";

$msg = "";
if (isset($_GET['action']) && $_GET['action'] == 'chk') {
	$pass = pro_db_real_escape_string($_POST['pwd']);
	$login = pro_db_real_escape_string($_POST['user']);
	$check_hash = hash('sha256', $login . $pass);
	$chklogin = pro_db_query("select lm.*, cm.packageID as complexPackage, concat(pm.packageID, ',', pm.parentID) as packages
							from loginMaster lm
							join complexMaster cm on lm.complexID = cm.complexID
							left join packageMaster pm on cm.packageID = pm.packageID and pm.status = 1
							where cm.packageID > 0 and lm.loginID = '" . $login . "' and lm.status in (1, 'E')");
	if (pro_db_num_rows($chklogin) > 0) {
		while ($logrs = pro_db_fetch_array($chklogin)) {
			if ($logrs['userPwd'] == $check_hash) {
				$_SESSION['complexPackage'] = $logrs['complexPackage'];
				$_SESSION['packages'] = $logrs['packages'];
				$_SESSION['logged'] = 1;
				$_SESSION['username'] = $logrs['userName'];
				$_SESSION['groupID'] = $logrs['groupID'];
				$_SESSION['userID'] = $logrs['userID'];
				$_SESSION['blockID'] = $logrs['blockID'];
				$_SESSION['memberID'] = $logrs['memberID'];
				$_SESSION['complexID'] = $logrs['complexID'];
				$_SESSION['loginID'] = $logrs['loginID'];
				$_SESSION['email'] = $logrs['userEmail'];
				$_SESSION['memberType'] = $logrs['memberType'];
				if ($logrs['memberID'] > 0) {
					$getofficeID = pro_db_query("SELECT bfm.* FROM officeMemberMapping ofm
												join blockFloorOfficeMapping bfm on (bfm.memberID = ofm.employeeID or bfm.memberID = ofm.parentID)
												where bfm.isPrimary = 1 and ofm.employeeID = '" . $logrs['memberID'] . "'");
					$officeIDrs = pro_db_fetch_array($getofficeID);
					$_SESSION['officeID'] = $officeIDrs['officeMappingID'];
				}
				//if($logrs['memberType'] != 0){
					echo '<script>location.href="index.php";</script>';
				//}
				//else{
					//echo '<script>location.href="office/index.php";</script>';	
				//}
				//$insertsession = pro_db_query("Insert into sessionLogMaster (sessionLogID,userID,loginID,remote_ip,last_access,status) values(null,{$logrs['userID']},'" . $logrs['loginID'] . "','" . $_SERVER['REMOTE_ADDR'] . "',now(),'I')");
			} else {
				$msg = '<center><span class="erromsg">Sorry, Authorization is failed111111..</span></center>';
			}
		}
	} else {
		$msg = '<center><span class="erromsg">Sorry, Authorization is failed2222222222..</span></center>';
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GGATE Dashboard</title>

	<!-- CSS Files -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/login.css" rel="stylesheet" type="text/css">

	<!-- JS Files -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(function() {
			$(".login_error_message").slideDown("slow");
		});
	</script>
</head>

<body>
	<?php if ($msg != '') { ?> <div class="login_error_message"><?php echo $msg; ?></div> <?php } ?>
	<div class="login adaptable">
		<!-- Login -->
		<div class="login__block toggled">
			<div class="login__block__header">
				<img style="height:70px;" src="assets/images/ggate_logo_full.svg" class="img-fluid">
			</div>
			<div class="login__block__body">
				<br>
				<form action="login.php?action=chk" method="post">
					<div class="form-group form-group--float form-group--centered form-group--centered">
						<input type="text" class="form-control" name="user" id="inputEmail" placeholder="Your Login ID">
					</div>
					<div class="form-group form-group--float form-group--centered form-group--centered">
						<input type="password" class="form-control" name="pwd" id="inputPassword" placeholder="Your Password">
					</div>
					<button type="submit" class="btn btn-primary btn-block">Login</button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
