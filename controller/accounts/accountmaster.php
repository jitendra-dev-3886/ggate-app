<?php
class accountmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $makeAdminformaction;
	protected $cloudStorage;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>New Account</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Title:</label>
									<input type="text" name="accountName" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Account Type:</label>
									<select name="accountGroupID" id="accountGroupID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Type</option>
										<?php
										$qry = pro_db_query("select ahm.* FROM accountHeadMaster ahm
															join accountGroupMaster agm on agm.accountHeadID = ahm.accountHeadID
															where ahm.status = 1 and agm.status = 1 and
															(agm.societyID = " . $_SESSION['societyID'] . " or agm.societyID = 0) group by ahm.accountHeadID");
										$rows = pro_db_num_rows($qry);
										if ($rows > 0) {
											while ($rs = pro_db_fetch_array($qry)) {
												print '<option  style=" font-size: large;  color:#3a2121;" disabled>' . $rs['headName'] . '</option>';
												$subttypeqry = pro_db_query("select * from accountGroupMaster where status = 1 and accountHeadID = " . $rs['accountHeadID'] . " and (societyID = " . $_SESSION['societyID'] . " or societyID = 0)");
												while ($subttypers = pro_db_fetch_array($subttypeqry)) {
													print '<option value="' . $subttypers['accountGroupID'] . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $subttypers['groupName'] . '</option>';
												}
											}
										}
										?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Account Code:</label>
									<input type="text" name="accountCode" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Account Description:</label>
									<input type="text" name="accountDescription" class="form-control" placeholder="">
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from accountMaster where accountID = " . (int)$_REQUEST['accountID']);
		$rs = pro_db_fetch_array($qry);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Account</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Title:</label>
									<input type="text" name="accountName" class="form-control" value="<?php echo $rs['accountName']; ?>" placeholder="" required>
								</div>
								<div class="form-group col-sm-3">
									<label>Account Type:</label>
									<select name="accountGroupID" id="accountGroupID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Type</option>
										<?php
										$typeqry = pro_db_query("select ahm.* FROM accountHeadMaster ahm
																join accountGroupMaster agm on agm.accountHeadID = ahm.accountHeadID
																where ahm.status = 1 and agm.status = 1 and
																(agm.societyID = " . $_SESSION['societyID'] . " or agm.societyID = 0) group by ahm.accountHeadID");
										$rows = pro_db_num_rows($typeqry);
										if ($rows > 0) {
											while ($typers = pro_db_fetch_array($typeqry)) {
												print '<option style=" font-size: large;font-weight: bold;" disabled>' . $typers['headName'] . '</option>';
												$subttypeqry = pro_db_query("select * from accountGroupMaster
																			where status = 1 and accountHeadID = " . $typers['accountHeadID'] . "
																			and (societyID = " . $_SESSION['societyID'] . " or societyID = 0)");
												while ($subttypers = pro_db_fetch_array($subttypeqry)) {
													if ($rs['accountGroupID'] == $subttypers['accountGroupID']) {
														print '<option value="' . $subttypers['accountGroupID'] . '" selected>' . $subttypers['groupName'] . '</option>';
													} else {
														print '<option value="' . $subttypers['accountGroupID'] . '">' . $subttypers['groupName'] . '</option>';
													}
												}
											}
										}
										?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Account Code:</label>
									<input type="text" name="accountCode" class="form-control" value="<?php echo $rs['accountCode']; ?>" placeholder="">
								</div>
								<div class="form-group col-sm-3">
									<label>Account Description:</label>
									<input type="text" name="accountDescription" class="form-control" value="<?php echo $rs['accountDescription']; ?>" placeholder="">
								</div>
								<div class="form-group col-sm-2">
									<label>Status:</label>
									<select name="status" class="form-control custom-select mr-sm-2">
										<?php echo $status; ?>
									</select>
								</div>
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="hidden" name="accountID" value="<?php echo $rs['accountID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function preventBack() {
				window.history.forward();
			}
			window.onunload = function() {
				null;
			};
			setTimeout("preventBack()", 0);
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('accountMaster', $formdata)) {
			$accountID = pro_db_insert_id();
			$msg = '<p class="bg-success p-3">Account is created successfully...</p>';

			$balanceqry = pro_db_query("select balanceID  from accountBalanceMaster where accountID = " . $accountID);
			if (pro_db_num_rows($balanceqry) == 0) {
				$yearqry = pro_db_query("select financialYearID from accountFinancialYear where currentYear = 1");
				$yearrs = pro_db_fetch_array($yearqry);

				$headqry = pro_db_query("select accountHeadID from accountGroupMaster where accountGroupID = " . $_POST['accountGroupID']);
				$headrs = pro_db_fetch_array($headqry);

				$balancedata = array();
				$balancedata['accountID'] = $accountID;
				$balancedata['accountGroupID'] = $_POST['accountGroupID'];
				$balancedata['accountHeadID'] = $headrs['accountHeadID'];
				$balancedata['financialYearID'] = $yearrs['financialYearID'];
				$balancedata['societyID'] = $_SESSION['societyID'];
				pro_db_perform('accountBalanceMaster', $balancedata);
			}
		} else {
			$msg = '<p class="bg-danger p-3"> Issues creating Account!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "accountID = " . $_POST['accountID'];
		$formdata = $_POST;

		if (pro_db_perform('accountMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Account Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Detail is not updated!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update accountMaster set status = 126 where accountID = " . (int)$_GET['accountID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Account has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Not deleted successfully</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Account Master Management</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Create Account</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="accountMasterList" width="100%">
								<thead>
									<tr>
										<th>Account</th>
										<th>Group</th>
										<th>Account Head</th>
										<th>Code</th>
										<th>Description</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Account</th>
										<th>Group</th>
										<th>Account Head</th>
										<th>Code</th>
										<th>Description</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = 'helperfunc/accountMasterList.php';
			$('#accountMasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "accountMaster"
				},
				source: [{
					value: '1',
					text: 'Active'
				}, {
					value: '0',
					text: 'Disable'
				}]
			});
		</script>
	<?php
	}

	public function accountLedgerForm()
	{
		$financialYearID = generateOptions(getMasterList('accountFinancialYear', 'financialYearID', 'year'));
	?>
		<div class="well quickForm" id="searchForm">
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=accountLedger'; ?>" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="form-group col-sm-3">
						<label>Select Financial Year:</label>
						<select name="financialYearID" class="form-control custom-select mr-sm-2" required>
							<option value="">Select Financial Year</option>
							<?php echo $financialYearID; ?>
						</select>
					</div>
					<div class="form-group col-sm-6">
						<label>&nbsp;</label><br>
						<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
						<input type="hidden" name="accountID" value="<?php echo $_REQUEST['accountID']; ?>">
						<button type="submit" class="btn btn-success">Submit</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Reset" data-url="<?php echo $this->redirectUrl; ?>">Reset</button>
					</div>
				</div>
			</form>
		</div>
	<?php
	}

	public function accountLedger()
	{
		$financialYearID = $_REQUEST['financialYearID'];
		$arrAllAccounts = array();
		$accountID = $_REQUEST['accountID'];

		//Transacrion of Account
		$dictDebits = array();
		$dictCredits = array();
		$totalDebitAmount = 0.0;
		$totalCreditAmount = 0.0;
		$sqlDebitAmount = pro_db_query("select atm.transactionDate, atsm.debitAccountID, atsm.amountDebited as totalDebit, adm.accountName as debitAccountName,
										atsm.creditAccountID , atsm.amountCredited as totalCredit, acm.accountName as creditAccountName
										from accountTransactionSubMaster atsm
										left join accountMaster adm on adm.accountID = atsm.debitAccountID
										left join accountMaster acm on acm.accountID = atsm.creditAccountID
										join accountTransactionMaster atm on atsm.transactionID = atm.transactionID
										where (atsm.creditAccountID = " . $accountID . " or atsm.debitAccountID =  " . $accountID . ")
										and atm.financialYearID = " . $financialYearID);
		while ($res = pro_db_fetch_array($sqlDebitAmount)) {
			if (($accountID != $res['debitAccountID']) && ($accountID != $res['creditAccountID'])) {

				$dictDebits[$res['debitAccountID']] = $res['totalDebit'];
				$totalDebitAmount += $res['totalDebit'];

				$dictCredits[$res['creditAccountID']] = $res['totalCredit'];
				$totalCreditAmount += $res['totalCredit'];

				$objSubAccount =
					array(
						'date' =>  $res['transactionDate'],
						'Dr' => 'Dr',
						'debitTitle' => $res['debitAccountName'],
						'debitAmt' =>  $res['totalDebit'],
						'Cr' => 'Cr',
						'creditTitle' => $res['creditAccountName'],
						'creditAmt' =>  $res['totalCredit']
					);
			} else if ($accountID == $res['debitAccountID']) {

				$totalDebitAmount = $totalDebitAmount + 0;
				$dictCredits[$res['creditAccountID']] = $res['totalCredit'];
				$totalCreditAmount += $res['totalCredit'];

				$objSubAccount =
					array(
						'date' =>  $res['transactionDate'],
						'Dr' => '',
						'debitTitle' => '',
						'debitAmt' =>  '',
						'Cr' => 'Cr',
						'creditTitle' => $res['creditAccountName'],
						'creditAmt' =>  $res['totalCredit']
					);
			} else {
				$dictDebits[$res['debitAccountID']] = $res['totalDebit'];
				$totalDebitAmount += $res['totalDebit'];
				$totalCreditAmount = $totalCreditAmount + 0;

				$objSubAccount =
					array(
						'date' =>  $res['transactionDate'],
						'Dr' => 'Dr',
						'debitTitle' => $res['debitAccountName'],
						'debitAmt' =>  $res['totalDebit'],
						'Cr' => '',
						'creditTitle' => '',
						'creditAmt' =>  ''
					);
			}
			$arrAllAccounts[] = $objSubAccount;
		}

		$objHeadAccount =
			array(
				'date' =>  '',
				'Dr' => '',
				'debitTitle' => 'Grand Total',
				'debitAmt' =>   $totalDebitAmount > 0 ? $totalDebitAmount : '',
				'Cr' => '',
				'creditTitle' => '',
				'creditAmt' =>  $totalCreditAmount > 0 ? $totalCreditAmount : '',
			);

		$arrAllAccounts[] = $objHeadAccount;
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4 style="font-size: 23px;" align="center">
					<?php $accountnameqry = pro_db_query("select accountName from accountMaster where accountID = " . $accountID);
					$accountnamers = pro_db_fetch_array($accountnameqry);
					echo $accountnamers['accountName']; ?>
				</h4>
				<h4 style="font-size: 20px;" align="center">Account Transaction History</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="form-group col-sm-12">
								<div class="input-field table-responsive">
									<table class="table table-borderless" id="table_entries" style="width :100%">
										<!-- <table class="table table-bordered" id="table_entries" style="width :100%"> -->
										<tr class="odd" style="width:100%; background: #F1F3FF;">
											<th style="font-weight: bold; font-size: medium;">Date</th>
											<th style="font-weight: bold; font-size: medium;">Dr</th>
											<th style="font-weight: bold; font-size: medium;">Account</th>
											<th style="font-weight: bold; font-size: medium;">Amount</th>
											<th style="font-weight: bold; font-size: medium;">Cr</th>
											<th style="font-weight: bold; font-size: medium;">Account</th>
											<th style="font-weight: bold; font-size: medium;">Amount</th>
										</tr>
										<?php
										foreach ($arrAllAccounts as $objAccount) {
										?>
											<tr>
												<?php
												if ($objAccount['debitTitle'] != 'Grand Total') {
												?>
													<td style="font-size: medium;">
														<?php echo $objAccount['date']; ?>
													</td>
													<td style="font-size: medium;">
														<?php if (!empty($objAccount['debitTitle'])) {
															echo $objAccount['Dr'];
														} ?>
													</td>
													<td style="font-size: medium;">
														<?php echo $objAccount['debitTitle']; ?>
													</td>
													<td style="font-size: medium;">
														<?php if ($objAccount['debitAmt'] > 0) {
															echo $objAccount['debitAmt'];
														} ?>
													</td>
													<td style="font-size: medium;">
														<?php if (!empty($objAccount['creditTitle'])) {
															echo $objAccount['Cr'];
														} ?>
													</td>
													<td style="font-size: medium;">
														<?php echo $objAccount['creditTitle']; ?>
													</td>
													<td style="font-size: medium;">
														<?php if ($objAccount['creditAmt'] > 0) {
															echo $objAccount['creditAmt'];
														} ?>
													</td>
												<?php
												}
												?>
											</tr>
										<?php
										}
										?>
										<?php
										if ($totalDebitAmount !=  $totalCreditAmount) {
										?>
											<tr>
												<td style="font-size: medium;"></td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalCreditAmount >  $totalDebitAmount) {
														echo "Dr ";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalCreditAmount >  $totalDebitAmount) {
														echo "To Balance c/d";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalCreditAmount >  $totalDebitAmount) {
														echo $totalCreditAmount - $totalDebitAmount;
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalDebitAmount >  $totalCreditAmount) {
														echo "Cr";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalDebitAmount >  $totalCreditAmount) {
														echo "By Balance c/d";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalDebitAmount >  $totalCreditAmount) {
														echo $totalDebitAmount - $totalCreditAmount;
													} ?>
												</td>
											</tr>
										<?php
										}
										foreach ($arrAllAccounts as $objAccount) {
										?>
											<tr <?php if ($objAccount['debitTitle'] == 'Grand Total') {
												?> style="background:antiquewhite;" <?php } ?>>
												<?php
												if ($objAccount['debitTitle'] == 'Grand Total') {
												?>
													<td style="font-weight: bold;  font-size: large;">
														<?php echo $objAccount['debitTitle']; ?>
													</td>
													<td></td>
													<td></td>
													<td style="font-weight: bold;  font-size: large;">
														<?php if ($totalDebitAmount >  $totalCreditAmount) {
															echo $totalDebitAmount;
														} else {
															echo $totalCreditAmount;
														} ?>
													</td>
													<td></td>
													<td></td>
													<td style="font-weight: bold;  font-size:large;">
														<?php if ($totalDebitAmount >  $totalCreditAmount) {
															echo $totalDebitAmount;
														} else {
															echo $totalCreditAmount;
														} ?>
													</td>
												<?php
												}
												?>
											</tr>
										<?php
										}
										if ($totalDebitAmount != $totalCreditAmount) {
										?>
											<tr>
												<td style="font-size: medium;"></td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalDebitAmount >  $totalCreditAmount) {
														echo "Dr";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalDebitAmount >  $totalCreditAmount) {
														echo "To Balance b/d";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalDebitAmount >  $totalCreditAmount) {
														echo $totalDebitAmount - $totalCreditAmount;
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalCreditAmount >  $totalDebitAmount) {
														echo "Cr";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalCreditAmount >  $totalDebitAmount) {
														echo "By Balance b/d";
													} ?>
												</td>
												<td style="font-size: medium; font-weight: bold;">
													<?php if ($totalCreditAmount >  $totalDebitAmount) {
														echo $totalCreditAmount - $totalDebitAmount;
													} ?>
												</td>
											</tr>
										<?php
										}
										?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}
?>