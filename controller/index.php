<?php
if (defined('ADMIN_ALLOWED') == true) {

	if ($_SESSION['groupID'] == 7) {
		$whr = "and ";
	}

	$sqlChart = "(select inDateTime, DATE_FORMAT(inDateTime,'%D %b') as dayMonth, sum(if(visitorType = '1', 1, 0)) as 'totalDailyResource', 
				sum(if(visitorType = '2', 1, 0)) as 'totalGuest', sum(if(visitorType = '3', 1, 0)) as 'totalDeliveryBoy', 
				sum(if(visitorType = '4', 1, 0)) as 'totalCab' from dailyGateActivity 
				where complexID = " . $_SESSION["complexID"] . " and inDateTime is not null
				group by dayMonth order by inDateTime desc limit 7) order by inDateTime";
	$graphSql = pro_db_query($sqlChart);
	$resourceChartData = "";
	if (pro_db_num_rows($graphSql) > 0) {
		while ($grs = pro_db_fetch_array($graphSql)) {
			$resourceChartData .= "
				['" . $grs['dayMonth'] . "', " . $grs['totalDailyResource'] . ", " . $grs['totalGuest'] . ", 
				" . $grs['totalDeliveryBoy'] . ", " . $grs['totalCab'] . "],";
		}
	} else {
		$resourceChartData .= "['---', 0, 0, 0, 0]";
	}

	if ($_SESSION['groupID'] == 7) {
		$Invoicewhr = "and i.officeMappingID = " . $_SESSION['officeID'] . "";
	} else {
		$Invoicewhr = "";
	}

	//Invoice Data
	$queryInvoices = pro_db_query("select count(i.invoiceID) as totalInvoices, count(itPaid.transactionID) as paidInvoices, 
								count(itPending.transactionID) as pendingInvoices, count(itFailed.transactionID) as failedInvoices 
								from invoice i 
								left join invoiceTransaction itPaid on i.invoiceID = itPaid.invoiceID and itPaid.status = 1 
								left join invoiceTransaction itPending on i.invoiceID = itPending.invoiceID and itPending.status = 0
								left join invoiceTransaction itFailed on i.invoiceID = itFailed.invoiceID and itFailed.status = 2
								where i.complexID = " . (int)$_SESSION['complexID'] . " " . $Invoicewhr . "");
	$resInvoices = pro_db_fetch_array($queryInvoices);
	$totalInvoices = $resInvoices["totalInvoices"];
	$paidInvoices = $resInvoices["paidInvoices"];
	$pendingInvoices = $resInvoices["pendingInvoices"];
	$failedInvoices = $resInvoices["failedInvoices"];

	$queryDueInvoice = pro_db_query("select count(invoiceID) as dueInvoices from invoice where CURRENT_DATE > invoiceDueDate 
									and status = 0 and complexID = " . (int)$_SESSION['complexID'] . " " . $Invoicewhr . "");
	$resDueInvoice = pro_db_fetch_array($queryDueInvoice);
	$dueInvoices = $resDueInvoice["dueInvoices"];

	//Pending
	$pendingInvoices = $totalInvoices - $paidInvoices - $failedInvoices - $dueInvoices;

	//Invoice Chart Data
	$invoiceChartData = "";
	if ($totalInvoices > 0) {
		$invoiceChartData .= "['Due', " . $dueInvoices . "]";
		$invoiceChartData .= ",['Paid', " . $paidInvoices . "]";
		$invoiceChartData .= ",['Pending', " . $pendingInvoices . "]";
		$invoiceChartData .= ",['Failed', " . $failedInvoices . "]";
	}

	//Remaining Days Calculation
	$querySocietyDates = pro_db_query("select enrolledDate, validUptoDate from complexMaster where complexID = " . (int)$_SESSION['complexID']);
	$resSocietyDates = pro_db_fetch_array($querySocietyDates);
	$enrolledDate = $resSocietyDates["enrolledDate"];
	$validUptoDate = $resSocietyDates["validUptoDate"];
	//Difference Days
	$enrolledDateTime = strtotime($enrolledDate);
	$validUptoDateTime = strtotime($validUptoDate);
	$totalDaysDifference = ceil(abs($validUptoDateTime - $enrolledDateTime) / 86400);
	//Remaining Days
	$todayDateTime = strtotime(date('Y-m-d'));
	$remainingDays = ceil(abs($validUptoDateTime - $todayDateTime) / 86400);
	//Remaining Days - Percentage
	$percentage = $remainingDays / $totalDaysDifference * 100;
	$remainingPercentage = round($percentage);
	if ($remainingPercentage > 100) {
		$remainingPercentage = 100;
	}
	$finishedPercentage = 100 - $remainingPercentage;
	//Display Dates
	$displayEnrolledDate = date("jS M, Y", $enrolledDateTime);
	$displayValidUptoDate = date("jS M, Y", $validUptoDateTime);
?>

	<div class="row" id="proBanner">
		<div class="col-md-8 grid-margin stretch-card">
			<div class="card">
				<div class="card-body align-content-end">
					<div class="row">
						<div class="col md-8">
							<h4 class="md-5">
								<p class="text-dark text-left m-1 display-5">Welcome,
									<strong class="text-danger">
										<?php
										$qry = pro_db_query("select complexName from complexMaster where complexID = " . (int)$_SESSION['complexID']);
										$rs = pro_db_fetch_array($qry);
										echo $rs['complexName']; ?>
									</strong>
								</p>
							</h4>
						</div>
					</div>
					<div class="row">
						<br />
					</div>
					<div class="row">
						<div class="col-md-3 stretch-card">
							<i class="mdi mdi-home icon-md d-flex align-self-start mr-3 newhome"></i>
							<div>
								<p class="text-dark text-left m-1">
									<strong class="card-title">
										<?php
										$qry = pro_db_query("select count(blockID) as blocks from blockMaster where status = 1 and complexID = " . (int)$_SESSION['complexID']);
										$rs = pro_db_fetch_array($qry);
										echo $rs['blocks'];
										?>
									</strong>
								</p>
								<p class="card-description">Total Blocks</p>
							</div>
						</div>

						<div class="col-md-3 stretch-card">
							<i class="mdi mdi-city icon-md d-flex align-self-start mr-3 newolid"></i>
							<div>
								<p class="text-dark text-left m-1">
									<strong class="card-title">
										<?php
										$qry = pro_db_query("select count(officeMappingID) as offices from blockFloorOfficeMapping where status = 1 and complexID = " . (int)$_SESSION['complexID']);
										$rs = pro_db_fetch_array($qry);
										echo $rs['offices'];
										?>
									</strong>
								</p>
								<p class="card-description">Total Residence</p>
							</div>
						</div>

						<div class="col-md-3 stretch-card">
							<i class="mdi mdi-account-multiple icon-md d-flex align-self-start mr-3 newoji"></i>
							<div>
								<p class="text-dark text-left m-1">
									<strong class="card-title">
										<?php
										$qry = pro_db_query("select count(memberID) as members from memberMaster where complexID = " . (int)$_SESSION['complexID']);
										$rs = pro_db_fetch_array($qry);
										echo $rs['members'];
										?>
									</strong>
								</p>
								<p class="card-description">Total Members</p>
							</div>
						</div>

						<div class="col-md-3 stretch-card">
							<i class="mdi mdi-account-check icon-md d-flex align-self-start mr-3 newopi"></i>
							<div>
								<p class="text-dark text-left m-1">
									<strong class="card-title">
										<?php
										$qry = pro_db_query("select count(memberID) as members from memberActivity where status = 4 and complexID = '" . $_SESSION['complexID'] . "'");
										$rs = pro_db_fetch_array($qry);
										echo $rs['members'];
										?>
									</strong>
								</p>
								<p class="card-description">Active Users</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-4 grid-margin stretch-card">
			<div class="card">
				<div class="card-body align-content-end">
					<div class="row">
						<div class="col md-6">
							<p class="text-dark text-left m-1">Enrolled Date</p>
							<p class="text-dark text-left m-1"><strong class="text-info"><?php echo $displayEnrolledDate ?></strong></p>
						</div>
						<div class="col md-6">
							<p class="text-dark text-right m-1">Valid upto Date:</p>
							<p class="text-dark text-right m-1"><strong class="text-danger"><?php echo $displayValidUptoDate ?></strong></p>
						</div>
					</div>
					<div class="py-3">
						<div class="progress" style="height: 25px;">
							<div class="progress-bar <?php if ($finishedPercentage > 75) { ?> badge badge-gradient-progress-danger <?php } else { ?> badge badge-gradient-progress-success <?php } ?>" role="progressbar" style="width: <?php echo $finishedPercentage ?>%" aria-valuenow="<?php echo $finishedPercentage ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $finishedPercentage . "%"; ?></div>
						</div>
					</div>
					<div class="row">
						<div class="col md-6">
							<p class="text-dark text-right m-1">Remaining Days: <strong class=" <?php if ($finishedPercentage > 75) { ?> text-danger <?php } else { ?> text-info <?php } ?>"><?php echo $remainingDays ?></strong></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		<div class="row">
			<div class="col-md-8 grid-margin stretch-card">
				<div class="card">
					<div class="card-body chart_wrap">
						<h4 class="card-title">Daily Complex Visitors</h4>
						<div id="society_visitor_chart" style="width: 100%; height: 400px; position:inherit;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4 grid-margin stretch-card">
				<div class="card">
					<div class="card-body invoice_wrap">
						<h4 class="card-title">Invoices Chart</h4>
						<div id="invoice_chart" style="width: 100%; height: 400px; position:inherit;"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
		<div class="col-sm-12 grid-margin">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Pending Staff Requests</h4>
					<div class="table-responsive">
						<table class="table table-striped dataTable psr" id="pendingdailystaffmasterList">
							<thead>
								<tr>
									<th>Office</th>
									<th>Name</th>
									<th>Resource Type</th>
									<!-- <th>Nick Name</th> -->
									<th>Profession</th>
									<th>Valid Upto</th>
									<th>ID Type</th>
									<th>ID Value</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 grid-margin">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Pending Office Requests</h4>
					<div class="table-responsive">
						<table class="table table-striped dataTable pfr" id="pendingpropertyList">
							<thead>
								<tr>
									<th>Office</th>
									<th>Name</th>
									<th>Contact</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 grid-margin">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Pending Member Requests</h4>
					<div class="table-responsive">
						<table class="table table-striped dataTable pmr" id="pendingmemberList">
							<thead>
								<tr>
									<th>Office</th>
									<th>Member</th>
									<th>Mobile</th>
									<th>Email</th>
									<th>Address</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		var listURL = 'helperfunc/pendingdailystaffmasterList.php';
		$('#pendingdailystaffmasterList').dataTable({
			"ajax": listURL,
			"deferRender": true,
			"stateSave": true,
			"iDisplayLength": 5
		});
		$('.psr').editable({
			selector: 'a.estatus',
			params: {
				"tblName": "dailyStaffMaster"
			},
			source: [{
				value: '1',
				text: 'Accept'
			}, {
				value: '0',
				text: 'Pending'
			}, {
				value: '2',
				text: 'Reject'
			}]
		});
		var listURL = 'helperfunc/pendingpropertyList.php';
		$('#pendingpropertyList').dataTable({
			"ajax": listURL,
			"deferRender": true,
			"stateSave": true,
			 scrollX: false,
			"iDisplayLength": 5
		});
		$('.pfr').editable({
			selector: 'a.estatus',
			params: {
				"tblName": "blockFloorOfficeMapping"
			},
			source: [{
				value: '1',
				text: 'Accept'
			}, {
				value: '0',
				text: 'Pending'
			}, {
				value: '2',
				text: 'Reject'
			}]
		});
		var listURL = 'helperfunc/pendingmemberList.php';
		$('#pendingmemberList').dataTable({
			"ajax": listURL,
			"deferRender": true,
			"stateSave": true,

			"iDisplayLength": 5
		});
		$('.pmr').editable({
			selector: 'a.estatus',
			params: {
				"tblName": "memberApproverRequest"
			},
			source: [{
				value: '1',
				text: 'Accept'
			}, {
				value: '0',
				text: 'Pending'
			}, {
				value: '2',
				text: 'Reject'
			}]
		});
	</script>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {
				'packages': ['corechart', 'gauge']
			});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart() {
				//Resources Chat
				var dataResources = google.visualization.arrayToDataTable([
					['Day', 'Daily Resources', 'Guest', 'Delivery Boy', 'Cab'],
					<?php echo $resourceChartData; ?>
				]);
				var optionsResources = {
					chartArea: {
						left: '5%',
						right: '5%',
						top: 50,
						width: '50%',
						height: '70%',
					},
					legend: {
						position: 'bottom',
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-medium',
							fontSize: '13',
							bold: false,
							italic: true
						}
					},
					vAxis: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-light',
							fontSize: '12',
							bold: true,
						}
					},
					hAxis: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-light',
							fontSize: '12',
							bold: true,
						}
					},
					seriesType: 'bars',
					series: [{
							color: '#9694ff',
							visibleInLegend: true,
						},
						{
							color: '#ffbf76',
							visibleInLegend: true
						},
						{
							color: '#5ddab4',
							visibleInLegend: true
						},
						{
							color: '#ff7976',
							visibleInLegend: true
						}
					],
					tooltip: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-medium',
							fontSize: '12',
							bold: false,
							italic: true
						}
					}
				};
				var chartResources = new google.visualization.ComboChart(document.getElementById('society_visitor_chart'));
				chartResources.draw(dataResources, optionsResources);
				//Invoice Chart
				var dataInvoices = google.visualization.arrayToDataTable([
					['Invoices', 'Count'],
					<?php echo $invoiceChartData; ?>
				]);
				var optionsInvoices = {
					chartArea: {
						width: '80%',
						height: '80%',
					},
					pieHole: 0.4,
					legend: {
						position: 'bottom',
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-medium',
							fontSize: '13',
							bold: false,
							italic: true
						}
					},
					pieSliceText: 'label',
					slices: [{
							color: '#9694ff',
							visibleInLegend: true,
						},
						{
							color: '#5ddab4',
							visibleInLegend: true
						},
						{
							color: '#ffbf76',
							visibleInLegend: true
						},
						{
							color: '#ff7976',
							visibleInLegend: true
						}
					],
					tooltip: {
						textStyle: {
							color: '#343A40',
							fontName: 'ubuntu-medium',
							fontSize: '12',
							bold: false,
							italic: true
						}
					}
				};
				var chartInvoices = new google.visualization.PieChart(document.getElementById('invoice_chart'));
				chartInvoices.draw(dataInvoices, optionsInvoices);
			}
		</script>
		<style>
			.chart_wrap {
				position: relative;
				height: 50;
				/* overflow: scroll; */
			}

			#society_visitor_chart {
				position: relative;
				top: 0;
				left: 0;
				width: 100%;
				height: 200px;
			}

			.invoice_wrap {
				position: relative;
				height: 50;
				/* overflow: scroll; */
			}

			#invoice_chart {
				position: relative;
				top: 0;
				left: 0;
				width: 100%;
				height: 200px;
			}
		</style>
	<?php
} else {
	header(HTTP_SERVER . WS_ADMIN_ROOT . "login.php");
}
	?>