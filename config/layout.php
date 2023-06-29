<?php include 'header.php';

$frmMsgDialog = '
	<div id="frmMessage" class="modal" tabindex="-1" data-reload-url="%s" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">GGATE</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-white">%s</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">Okay</button>
				</div>
			</div>
		</div>
	</div>';
?>

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>GGATE Dashboard</title>
	<!-- plugins:css -->
	<link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
	<link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
	<!-- endinject -->
	<!-- Plugin css for this page -->
	<!-- End plugin css for this page -->
	<!-- inject:css -->
	<!-- endinject -->
	<!-- Layout styles -->
	<link rel="stylesheet" href="assets/css/style.css">
	<!-- End layout styles -->
	<link rel="shortcut icon" href="assets/images/favicon.png" />
</head>

<body>
	<style>
		.scrollable-menu {
			height: auto;
			max-height: 400px;
			overflow-x: hidden;
		}
	</style>
	<div class="container-scroller">

		<?php
		//Check whether it is super society
		$isGGATESuperComplex = ($_SESSION['complexPackage'] == 1001);
		$_SESSION['isGGATESuperComplex'] = $isGGATESuperComplex;
		if ($isGGATESuperComplex) {
			$_SESSION['superComplexID'] = (int)$_SESSION['complexID'];
		}

		//Check for Maintenance
		$isMaintenance = false;
		if (!$isGGATESuperComplex) {
			$queryDashboardSettings = pro_db_query("select maintenance from ggateVersion where appType = 0");
			$resDashboardSettings = pro_db_fetch_array($queryDashboardSettings);
			$isMaintenance = ($resDashboardSettings['maintenance'] == 1);
		}
		?>

		<!-- partial:partials/_navbar.html -->
		<nav id="navbar_top" class="navbar navbar-expand-lg">
			<?php
			$qry = pro_db_query("select complexLogo, complexEmail, status from complexMaster where complexID = " . (int)$_SESSION['complexID']);
			$rs = pro_db_fetch_array($qry);
			$complexEmail = $rs['complexEmail'];
			$complexLogo = $rs['complexLogo'];

			//Check for Subscription
			$isSubscribed = $isGGATESuperComplex;
			if (!$isGGATESuperComplex) {
				$isSubscribed = ($rs['status'] == 1);
			}
			?>

			<a class="navbar-brand" href="index.php"><img width="auto" height="50" src="<?php echo $complexLogo; ?>" alt="logo" /></a>
			<div class="collapse navbar-collapse">
				<!-- partial -->
				<div class="container-fluid" style="padding-left: 5%">
					<div id="navigation">
						<?php
						if ($isSubscribed) {
						?>
							<!-- Navigation Menu-->
							<ul class="navigation-menu navbar-nav navbar-static-right navbar-nav-right">
								<li><a href="index.php">
										<p class="card-title"><i class="fe-airplay"></i>Dashboard</p>
									</a>
								</li>
								<?php
								//Manage Financial Package for Society
								$containFinancialPackage = false;
								//Parent Menus
								if ($isGGATESuperComplex) {
									$queryParentMenu = "
										select mm.moduleID, mm.moduleTitle, mm.moduleFile, mm.moduleIcon, mm.parentID, mm.sortorder
										from moduleMaster mm where mm.parentID = 0 and mm.isAppService = 0 and mm.status = 1
										group by mm.moduleID
										order by mm.sortorder";
									$containFinancialPackage = true;
								} else {
									//For Society Dashboard
									$queryParentMenu = "
										select mm.moduleID, mm.moduleTitle, mm.moduleFile, mm.moduleIcon, mm.parentID, mm.sortorder
										from moduleMaster mm
										join packageModuleMaster pmm on pmm.moduleID = mm.moduleID
										where mm.parentID = 0 and mm.status = 1 and mm.isAppService = 0
										and pmm.packageID in (" . $_SESSION['packages'] . ")
										group by mm.moduleID
										order by mm.sortorder";
									//Check whether Society contains any Financial Package
									if (strpos($_SESSION['packages'], "3") || strpos($_SESSION['packages'], "4")) {
										$containFinancialPackage = true;
									}
								}
								$_SESSION['containFinancialPackage'] = $containFinancialPackage;

								$resParentMenu = pro_db_query($queryParentMenu);
								if (pro_db_num_rows($resParentMenu) > 0) {
									$menuLinks = "";
									while ($rs = pro_db_fetch_array($resParentMenu)) {
										if (in_array($rs['moduleFile'], $_SESSION['gPermissions']) or $_SESSION['groupID'] < 6) {
											//Sub-Menus
											$isGGATEService = "";
											if ($_SESSION['complexPackage'] != 1001) {
												//For Society Dashboard
												$isGGATEService = " and mm.isGGATEService = 0";
											}
											$querySubMenu = "
												select mm.moduleID, mm.moduleTitle, mm.moduleFile, mm.moduleIcon, mm.parentID, mm.sortorder 
												from moduleMaster mm
												where mm.parentID = " . (int)$rs['moduleID'] . " and mm.status = 1 " . $isGGATEService . "
												group by mm.moduleID
												order by mm.sortorder";
											$resSubMenu = pro_db_query($querySubMenu);
											if (pro_db_num_rows($resSubMenu) > 0) {
												$moduleFile = $rs['moduleFile'];
												// $shouldPrevent = ($moduleFile == "masters");
												$shouldPrevent = (strpos($moduleFile, 'ggate') !== false);
												//Masters File - Only for GGATE Society
												if ($isGGATESuperComplex) {
													$shouldPrevent = false;
												}

												//Display Menu & Sub-Menu
												if (!$shouldPrevent) {
													$parentTitle = $rs['moduleTitle'];
													$menuLinks .= '
														<li class="has-submenu">
															<a href="#">
																<p class="card-title"><i class="mdi ' . $rs['moduleIcon'] . '"></i> ' . ucfirst($parentTitle) . ' </p>
															</a>
															<ul class="submenu scrollable-menu">
															';
													while ($srs = pro_db_fetch_array($resSubMenu)) {
														if (in_array($srs['moduleFile'], $_SESSION['gPermissions']) or $_SESSION['groupID'] < 6) {
															$menuLinks .= '<li><a href="index.php?controller=' . $moduleFile . '&action=' . $srs['moduleFile'] . '&subaction=listData" title="' . $srs['moduleTitle'] . '">
															<p class="card-title">' . ucfirst($srs['moduleTitle']) . '</p></a></li>';
														}
													}
													$menuLinks .= '
															</ul>
														</li>
                                                	';
												}
											} else {
												$menuLinks .= '<li><a href="index.php?controller=' . $moduleFile . '&action=' . $moduleFile . '&subaction=listData" title="' . $rs['moduleTitle'] . '"><i class="fe-' . $rs['moduleIcon'] . '"></i>' . ucfirst($rs['moduleTitle']) . '</a></li>';
											}
										} // End of Check if Group is Permitted
									}
								} else {
									$menuLinks .= '<li>No Module Loaded</li>';
								}
								echo $menuLinks;
								?>
							</ul>
							<!-- End navigation menu -->
						<?php
						}
						?>
					</div>
					<!-- end #navigation -->
				</div>
				<!-- end container -->
				<!-- end navbar-custom -->
				<li class="nav-item d-none d-lg-block full-screen-link">
					<a class="nav-link" style="color:#343a40; font-size: 30px">
						<i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
					</a>
				</li>
				<li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
					<a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
						<img class="img-xs rounded-circle" src="https://cdn.ggate.app/icons/GGate-Square512.png" alt="Profile image"> </a>
					<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
						<div class="dropdown-header text-center">
							<img class="img-md rounded-circle" style="width: 100px; height: 100px;" src="https://cdn.ggate.app/icons/GGate-Square512.png" alt="Profile image">
							<p class="mb-1 mt-3 font-weight-semibold">Complex Admin</p>
							<p class="font-weight-light text-muted mb-0"><?php echo $complexEmail; ?></p>
						</div>

						<?php
						if ($isSubscribed) {
						?>
							<a class="dropdown-item" type="button" href="<?php echo HTTP_SERVER . WS_ADMIN_ROOT; ?>index.php?controller=user&action=user&subaction=listData">Manage Users</a>
							<a class="dropdown-item" type="button" href="<?php echo HTTP_SERVER . WS_ADMIN_ROOT; ?>index.php?controller=permission&action=permission&subaction=listData">User Permissions</a>
							<a class="dropdown-item" type="button" href="<?php echo HTTP_SERVER . WS_ADMIN_ROOT; ?>index.php?controller=complexSettings&action=complexSettings&subaction=editForm">Complex Settings</a>
							<?php
							if ($containFinancialPackage) {
							?>
								<a class="dropdown-item" type="button" href="<?php echo HTTP_SERVER . WS_ADMIN_ROOT; ?>index.php?controller=complexSettings&action=complexAccountSettings&subaction=editForm">Accounts Settings</a>
							<?php
							}
							?>
						<?php
						}
						?>

						<div class="dropdown-divider"></div>
						<a class="dropdown-item" type="button" href="logoff.php">Logout</a>
					</div>
				</li>
				</ul>
				<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
					<span class="mdi mdi-menu"></span>
				</button>
			</div>
		</nav>

		<?php
		if ($isMaintenance) {
		?>
			<div class="main-panel">
				<div class="content-wrapper">
					<div class="container-fluid">
						<lottie-player src='assets/json/underMaintenance.json' background='transparent' speed='1' style='width: auto; height: 600px;' loop autoplay>
						</lottie-player>
					</div>
				</div>
				<footer class="footerGGate">
					<div class="container-fluid clearfix">
						<span class="d-block text-center text-sm-left d-sm-inline-block">Copyright &copy; <?php echo date('Y'); ?> - GGATE</span>
						<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Powered By <a href="https://www.recreatorinfotech.com" target="_blank">Recreator Infotech Pvt. Ltd.</a></span>
					</div>
				</footer>
			</div>
		<?php
		} else if (!$isSubscribed) {
		?>
			<div class="main-panel">
				<div class="content-wrapper">
					<div class="container-fluid">
						<lottie-player src='assets/json/subscriptionExpired.json' background='transparent' speed='1' style='width: auto; height: 600px;' loop autoplay>
						</lottie-player>
					</div>
				</div>
				<footer class="footerGGate">
					<div class="container-fluid clearfix">
						<span class="d-block text-center text-sm-left d-sm-inline-block">Copyright &copy; <?php echo date('Y'); ?> - GGATE</span>
						<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Powered By <a href="https://www.recreatorinfotech.com" target="_blank">Recreator Infotech Pvt. Ltd.</a></span>
					</div>
				</footer>
			</div>
		<?php
		} else {
		?>
			<div class="main-panel">
				<div class="content-wrapper">
					<div class="container-fluid">
						<?php include $content_include; ?>
					</div>
				</div>
				<footer class="footerGGate">
					<div class="container-fluid clearfix">
						<span class="d-block text-center text-sm-left d-sm-inline-block">Copyright &copy; <?php echo date('Y'); ?> - GGATE</span>
						<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Powered By <a href="https://www.recreatorinfotech.com" target="_blank">Recreator Infotech Pvt. Ltd.</a></span>
					</div>
				</footer>
			</div>
		<?php
		}
		?>

	</div>
	<script src="assets/js/hoverable-collapse.js"></script>
	<script src="assets/js/misc.js"></script>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			window.addEventListener('scroll', function() {
				if (window.scrollY > 70) {
					document.getElementById('navbar_top').classList.add('fixed-top');
					// add padding top to show content behind navbar
					navbar_height = document.querySelector('.navbar').offsetHeight;
					document.body.style.paddingTop = navbar_height + 'px';
				} else {
					document.getElementById('navbar_top').classList.remove('fixed-top');
					// remove padding top from body
					document.body.style.paddingTop = '0';
				}
			});
		});
	</script>
	<!-- endinject -->
	<?php include 'footer.php'; ?>
