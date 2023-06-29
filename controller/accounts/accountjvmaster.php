<?php
class accountjvmaster
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
		$entryType = generateStaticOptions(array("0" => "Journal", "2" => "Payment", "3" => "Sales", "4" => "Purchase", "5" => "Receipt"));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>New Voucher</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Voucher Date:</label>
									<input type="text" name="journalDate" class="form-control voucherDateTime" placeholder="" value="<?php echo date('Y-m-d H:i:s'); ?>">
								</div>
								<div class="form-group col-sm-2">
									<label>Reference#</label>
									<input type="text" name="referenceNumber" class="form-control" placeholder="" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Entry Type:</label>
									<select name="entryType" class="form-control custom-select mr-sm-2">
										<?php echo $entryType; ?>
									</select>
								</div>
								<div class="form-group col-sm-6">
									<label>Voucher Notes:</label>
									<input type="text" name="notes" class="form-control" placeholder="Enter Voucher Notes">
								</div>
							</div>
							<br>
							<h3>Choose Accounts:</h3>
							<br>
							<br>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Debit Account:</label>
									<select name="debitAccountID" id="debitAccountID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Debited Account</option>
										<?php
										$qry = pro_db_query("select agm.* FROM accountGroupMaster agm
										join accountMaster am on am.accountGroupID = agm.accountGroupID
										where am.status = 1 and agm.status = 1
										and (agm.societyID = " . $_SESSION['societyID'] . " or agm.societyID = 0)
										group by agm.accountGroupID");
										$rows = pro_db_num_rows($qry);
										if ($rows > 0) {
											while ($rs = pro_db_fetch_array($qry)) {
												print '<option style=" font-size: large;  color:#3a2121;" disabled>' . $rs['groupName'] . '</option>';
												$subttypeqry = pro_db_query("select * from accountMaster where status = 1 and accountGroupID = " . $rs['accountGroupID'] . " and societyID in (0, " . $_SESSION['societyID'] . ")");
												while ($subttypers = pro_db_fetch_array($subttypeqry)) {
													print '<option value="' . $subttypers['accountID'] . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $subttypers['accountName'] . '</option>';
												}
											}
										}
										?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Credit Account:</label>
									<select name="creditAccountID" id="creditAccountID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Credited Account</option>
										<?php
										$qry = pro_db_query("select agm.* FROM accountGroupMaster agm
										join accountMaster am on am.accountGroupID = agm.accountGroupID
										where am.status = 1 and agm.status = 1 
										and (agm.societyID = " . $_SESSION['societyID'] . " or agm.societyID = 0)
										group by agm.accountGroupID");
										$rows = pro_db_num_rows($qry);
										if ($rows > 0) {
											while ($rs = pro_db_fetch_array($qry)) {
												print '<option style=" font-size: large; color:#3a2121;" disabled>' . $rs['groupName'] . '</option>';
												$subttypeqry = pro_db_query("select * from accountMaster where status = 1 and accountGroupID = " . $rs['accountGroupID'] . " and societyID in (0, " . $_SESSION['societyID'] . ")");
												while ($subttypers = pro_db_fetch_array($subttypeqry)) {
													print '<option value="' . $subttypers['accountID'] . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $subttypers['accountName'] . '</option>';
												}
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12" style="padding-top:30px;">
									<h3 class="form-group">Manage Entries:</h3>
									<div class="input-field table-responsive">
										<table class="table table-bordered " id="table_entries" style="width :100%">
											<tr>
												<th style="width : 55%">Description</th>
												<th style="width : 25%">Amount</th>
												<th style="width : 15%">Add/Remove</th>
											</tr>
											<tr>
												<td><input type="text" class="form-control" name="description[]" required></td>
												<td><input type="number" min="0" class="form-control" name="amount[]" required></td>
												<td><input class="btn btn-warning" type="button" name="addEntries" id="addEntries" value="Add"></td>
											</tr>
										</table>
									</div>
								</div>
							</div>

							<div class="row">
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
		<script>
			// For Datetime Calendar
			$('.voucherDateTime').flatpickr({
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				maxDate: "today"
			});

			var entrieshtml = '<tr><td><input type="text" class="form-control" name="description[]" required></td><td><input type="number" min="0" class="form-control" name="amount[]" required></td><td><input class="btn btn-danger" type="button" name="removeEntries" id="removeEntries" value="Remove"></td></tr>';
			var entries = 1;
			var maxentries = 25;
			$("#addEntries").click(function() {
				if (entries <= maxentries) {
					$("#table_entries").append(entrieshtml);
					entries++;
				}
			});
			$("#table_entries").on('click', '#removeEntries', function() {
				$(this).closest('tr').remove();
				entries--;
			});
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select ajm.*, ajsm.creditAccountID, ajsm.debitAccountID, ajsm.amount as subAmount, ajsm.accountJVSubID,
							ajd.description, ajd.amount as detailAmount, ajsm.amountCredited, ajsm.amountDebited
							from accountJVMaster ajm
							join accountJVSubMaster ajsm on ajm.accountJVID = ajsm.accountJVID
							join accountJVDetails ajd on ajm.accountJVID = ajd.accountJVID
							where ajm.accountJVID = " . (int)$_REQUEST['accountJVID'] . " limit 1");
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$entryType = generateStaticOptions(array("0" => "Journal", "2" => "Payment", "3" => "Sales", "4" => "Purchase", "5" => "Receipt"), $rs['entryType']);
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
								<div class="form-group col-sm-2">
									<label>Date:</label>
									<input type="text" name="journalDate" class="form-control voucherDateTime" placeholder="" value="<?php echo $rs['journalDate']; ?>">
								</div>
								<div class="form-group col-sm-2">
									<label>Reference#</label>
									<input type="text" name="referenceNumber" class="form-control" placeholder="" value="<?php echo $rs['referenceNumber']; ?>" required>
								</div>
								<div class="form-group col-sm-2">
									<label>Entry Type:</label>
									<select name="entryType" class="form-control custom-select mr-sm-2">
										<?php echo $entryType; ?>
									</select>
								</div>
								<div class="form-group col-sm-6">
									<label>Notes:</label>
									<input type="text" name="notes" class="form-control" placeholder="" value="<?php echo $rs['notes']; ?>">
								</div>
							</div>
							<br>
							<h3>Choose Accounts:</h3>
							<br>
							<br>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Debit Account:</label>
									<select name="debitAccountID" id="debitAccountID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Debited Account</option>
										<?php
										$qry = pro_db_query("select agm.* FROM accountGroupMaster agm
										join accountMaster am on am.accountGroupID = agm.accountGroupID
										where am.status = 1 and agm.status = 1 and agm.societyID in (0, " . $_SESSION['societyID'] . ")");
										$rows = pro_db_num_rows($qry);
										if ($rows > 0) {
											while ($debitrs = pro_db_fetch_array($qry)) {
												print '<option style=" font-size: large;font-weight: bold;" disabled>' . $debitrs['groupName'] . '</option>';
												$subttypeqry = pro_db_query("select * from accountMaster where status = 1 and accountGroupID = " . $debitrs['accountGroupID'] . " and societyID in (0, " . $_SESSION['societyID'] . ")");
												while ($subttypers = pro_db_fetch_array($subttypeqry)) {
													if ($rs['debitAccountID'] == $subttypers['accountID']) {
														print '<option value="' . $subttypers['accountID'] . '" selected>' . $subttypers['accountName'] . '</option>';
													} else {
														print '<option value="' . $subttypers['accountID'] . '">' . $subttypers['accountName'] . '</option>';
													}
												}
											}
										}
										?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Amount Debited</label>
									<input type="text" class="form-control" placeholder="" value="<?php echo $rs['amountDebited']; ?>" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Credit Account:</label>
									<select name="creditAccountID" id="creditAccountID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Credited Account</option>
										<?php
										$qry = pro_db_query("select agm.* FROM accountGroupMaster agm
										join accountMaster am on am.accountGroupID = agm.accountGroupID
										where am.status = 1 and agm.status = 1 and agm.societyID in (0, " . $_SESSION['societyID'] . ")");
										$rows = pro_db_num_rows($qry);
										if ($rows > 0) {
											while ($creditrs = pro_db_fetch_array($qry)) {
												print '<option style=" font-size: large;font-weight: bold;" disabled>' . $creditrs['groupName'] . '</option>';
												$subttypeqry = pro_db_query("select * from accountMaster where status = 1 and accountGroupID = " . $creditrs['accountGroupID'] . " and societyID in (0, " . $_SESSION['societyID'] . ")");
												while ($subttypers = pro_db_fetch_array($subttypeqry)) {
													if ($rs['creditAccountID'] == $subttypers['accountID']) {
														print '<option value="' . $subttypers['accountID'] . '" selected>' . $subttypers['accountName'] . '</option>';
													} else {
														print '<option value="' . $subttypers['accountID'] . '">' . $subttypers['accountName'] . '</option>';
													}
												}
											}
										}
										?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Amount Credited</label>
									<input type="text" class="form-control" placeholder="" value="<?php echo $rs['amountCredited']; ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12" style="padding-top:30px;">
									<h3 class="form-group">Manage Entries:</h3>
									<div class="input-field table-responsive">
										<table class="table table-bordered " id="table_entries" style="width :100%">
											<tr>
												<th style="width : 55%">Description</th>
												<th style="width : 25%">Amount</th>
												<th style="width : 15%">Add/Remove</th>
											</tr>
											<tr>
												<td><input type="text" class="form-control" name="description[]" value="<?php echo $rs['description']; ?>"></td>
												<td><input type="text" class="form-control" name="amount[]" value="<?php echo $rs['detailAmount']; ?>"></td>
												<td><input class="btn btn-warning" type="button" name="addEntries" id="addEntries" value="Add"></td>
											</tr>
											<?php
											$query = pro_db_query("select description , amount as detailAmount from accountJVDetails where accountJVID = '" . (int)$_REQUEST['accountJVID'] . "' limit 100 offset 1");
											while ($res = pro_db_fetch_array($query)) {
											?>
												<tr>
													<td><input type="text" class="form-control" name="description[]" value="<?php echo $res['description']; ?>"></td>
													<td><input type="text" class="form-control" name="amount[]" value="<?php echo $res['detailAmount']; ?>"></td>
													<td><input class="btn btn-danger" type="button" name="removeEntries" id="removeEntries" value="Remove"></td>
												</tr>
											<?php
											}
											?>
										</table>
									</div>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<label></label>
								<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
								<input type="hidden" name="accountJVID" value="<?php echo $rs['accountJVID']; ?>">
								<input type="hidden" name="accountJVSubID" value="<?php echo $rs['accountJVSubID']; ?>">
								<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// For Datetime Calendar
			$('.voucherDateTime').flatpickr({
				enableTime: true,
				dateFormat: "Y-m-d H:i",
				maxDate: "today"
			});

			var entrieshtml = '<tr><td><input type="text" class="form-control" name="description[]" required></td><td><input type="number" min="0" class="form-control" name="amount[]" required></td><td><input class="btn btn-danger" type="button" name="removeEntries" id="removeEntries" value="Remove"></td></tr>';
			var entries = 1;
			var maxentries = 25;
			$("#addEntries").click(function() {
				if (entries <= maxentries) {
					$("#table_entries").append(entrieshtml);
					entries++;
				}
			});
			$("#table_entries").on('click', '#removeEntries', function() {
				$(this).closest('tr').remove();
				entries--;
			});
		</script>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata['journalDate'] = $_POST['journalDate'];
		$formdata['referenceNumber'] = $_POST['referenceNumber'];
		$formdata['notes'] = $_POST['notes'];
		$formdata['entryType'] = $_POST['entryType'];
		$formdata['societyID'] = $_POST['societyID'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d H:i:s');
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if (pro_db_perform('accountJVMaster', $formdata)) {
			$accountJVID = pro_db_insert_id();
			$subJVdata['accountJVID'] = $accountJVID;

			//CREDIT ACCOUNT DETAILS
			$creditAccountID = $_POST['creditAccountID'];
			$dcreditaccounttypeqry = pro_db_query("select accountGroupID from accountMaster where accountID = " . $_POST['creditAccountID']);
			$creditaccounttypers = pro_db_fetch_array($dcreditaccounttypeqry);
			$creditAccountGroupID = $creditaccounttypers['accountGroupID'];

			//DEBIT ACCOUNT DETAILS
			$debitAccountID = $_POST['debitAccountID'];
			$debitaccounttypeqry = pro_db_query("select accountGroupID from accountMaster where accountID = " . $_POST['debitAccountID']);
			$debitaccounttypers = pro_db_fetch_array($debitaccounttypeqry);
			$debitAccountGroupID = $debitaccounttypers['accountGroupID'];

			//submaster entry
			$subJVdata['creditAccountID'] = $creditAccountID;
			$subJVdata['creditAccountGroupID'] = $creditAccountGroupID;
			$subJVdata['debitAccountID'] = $debitAccountID;
			$subJVdata['debitAccountGroupID'] = $debitAccountGroupID;
			$subJVdata['societyID'] = $_POST['societyID'];
			$subJVdata['username'] = $_SESSION['username'];
			$subJVdata['createdate'] = date('Y-m-d H:i:s');
			$subJVdata['modifieddate'] = date('Y-m-d H:i:s');
			$subJVdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$subJVdata['status'] = 1;
			pro_db_perform('accountJVSubMaster', $subJVdata);
			$accountJVSubID = pro_db_insert_id();

			//Description and amount for details table
			$totalamount = 0;
			if (isset($_POST['description']) && isset($_POST['amount'])) {
				$description = $_POST['description'];
				$amount = $_POST['amount'];

				foreach ($description as $key => $value) {
					//JV details entry
					$jvDetails['accountJVID'] = $accountJVID;
					$jvDetails['accountJVSubID'] = $accountJVSubID;
					$jvDetails['description'] = $value;
					$jvDetails['amount'] = $amount[$key];
					$jvDetails['username'] = $_SESSION['username'];
					$jvDetails['createdate'] = date('Y-m-d H:i:s');
					$jvDetails['modifieddate'] = date('Y-m-d H:i:s');
					$jvDetails['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$jvDetails['status'] = 1;
					pro_db_perform('accountJVDetails', $jvDetails);
					$accountJVDetailsID = pro_db_insert_id();

					//total amount for insert into master tables	
					$totalamount += $amount[$key];
				}
			}

			if (!empty($_POST['creditAccountID'])) {
				$totalAmountCredited = $totalamount;
			} else {
				$totalAmountCredited = 0;
			}

			if (!empty($_POST['debitAccountID'])) {
				$totalAmountDebited = $totalamount;
			} else {
				$totalAmountDebited = 0;
			}

			//amount entry into JV sub master
			pro_db_query("update accountJVSubMaster set amount = " . $totalamount . ", amountCredited = " . $totalAmountCredited . ",
						amountDebited = " . $totalAmountDebited . " where accountJVSubID = " . $accountJVSubID);

			//amount entry into JV master
			pro_db_query("update accountJVMaster set amount = " . $totalamount . " where accountJVID = " . $accountJVID);

			//financial year
			$yearqry = pro_db_query("select financialYearID from accountFinancialYear where currentYear = 1");
			$yearrs = pro_db_fetch_array($yearqry);

			//Transaction master entry
			$transactiondata['referenceID'] = $accountJVID;
			$transactiondata['societyID'] = $_POST['societyID'];
			$transactiondata['transactionType'] = $_POST['entryType'];
			$transactiondata['financialYearID'] = $yearrs['financialYearID'];
			$transactiondata['transactionDate'] = date('Y-m-d H:i:s');
			$transactiondata['amount'] = $totalamount;
			$transactiondata['notes'] = !empty($_POST['notes']) ? $_POST['notes'] : "Transaction Entry";
			$transactiondata['username'] = $_SESSION['username'];
			$transactiondata['createdate'] = date('Y-m-d H:i:s');
			$transactiondata['modifieddate'] = date('Y-m-d H:i:s');
			$transactiondata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$transactiondata['status'] = 1;
			pro_db_perform('accountTransactionMaster', $transactiondata);
			$transactionID = pro_db_insert_id();

			//Transaction Sub master entry
			$subTransactiondata = array();
			$subTransactiondata['transactionID'] = $transactionID;
			$subTransactiondata['societyID'] = $_POST['societyID'];
			$subTransactiondata['creditAccountID'] = $creditAccountID;
			$subTransactiondata['creditAccountGroupID'] = $creditAccountGroupID;
			$subTransactiondata['amountCredited'] = $totalAmountCredited;
			$subTransactiondata['debitAccountID'] = $debitAccountID;
			$subTransactiondata['debitAccountGroupID'] = $debitAccountGroupID;
			$subTransactiondata['amountDebited'] =  $totalAmountDebited;
			$subTransactiondata['amount'] = $totalamount;
			$subTransactiondata['username'] = $_SESSION['username'];
			$subTransactiondata['createdate'] = date('Y-m-d H:i:s');
			$subTransactiondata['modifieddate'] = date('Y-m-d H:i:s');
			$subTransactiondata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$subTransactiondata['status'] = 1;
			pro_db_perform('accountTransactionSubMaster', $subTransactiondata);
			$subTransactionID = pro_db_insert_id();

			//Transaction Details
			if (isset($_POST['description']) && isset($_POST['amount'])) {
				$description = $_POST['description'];
				$amount = $_POST['amount'];

				foreach ($description as $key => $value) {
					//JV details entry
					$transactionDetails['transactionID'] = $transactionID;
					$transactionDetails['transactionSubID'] = $subTransactionID;
					$transactionDetails['description'] = $value;
					$transactionDetails['amount'] = $amount[$key];
					$transactionDetails['username'] = $_SESSION['username'];
					$transactionDetails['createdate'] = date('Y-m-d H:i:s');
					$transactionDetails['modifieddate'] = date('Y-m-d H:i:s');
					$transactionDetails['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$transactionDetails['status'] = 1;
					pro_db_perform('accountTransactionDetails', $transactionDetails);
					$transactionDetailsID = pro_db_insert_id();
				}
			}

			//credit entry for acccount balance master
			$creditbalancemasterqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $_POST['creditAccountID']);
			$creditbalancers = pro_db_fetch_array($creditbalancemasterqry);
			$balanceTotalAmountCredited = $totalAmountCredited + $creditbalancers['totalAmountCredited'];
			$creditAdjustmentAmount = 0;
			if ($balanceTotalAmountCredited > $creditbalancers['totalAmountDebited']) {
				$creditAdjustmentAmount = $balanceTotalAmountCredited - $creditbalancers['totalAmountDebited'];
			} else {
				$creditAdjustmentAmount = $creditbalancers['totalAmountDebited'] - $balanceTotalAmountCredited;
			}
			pro_db_query("update accountBalanceMaster set totalAmountCredited = " . $balanceTotalAmountCredited . ",
						adjustmentAmount = " . $creditAdjustmentAmount . " where accountID = " . $_POST['creditAccountID']);

			//Debit entry for acccount balance master
			$debitbalancemasterqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $_POST['debitAccountID']);
			$debitbalancers = pro_db_fetch_array($debitbalancemasterqry);

			$balanceTotalAmountDebited = $totalAmountDebited + $debitbalancers['totalAmountDebited'];
			$debitAdjustmentAmount = 0;
			if ($balanceTotalAmountDebited > $debitbalancers['totalAmountCredited']) {
				$debitAdjustmentAmount = $balanceTotalAmountDebited - $debitbalancers['totalAmountCredited'];
			} else {
				$debitAdjustmentAmount = $debitbalancers['totalAmountCredited'] - $balanceTotalAmountDebited;
			}
			pro_db_query("update accountBalanceMaster set totalAmountDebited = " . $balanceTotalAmountDebited . ",
						adjustmentAmount = " . $debitAdjustmentAmount . " where accountID = " . $_POST['debitAccountID']);

			//Response
			$msg = '<p class="bg-success p-3">Journal details has been saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3"> Issues saving Journal details!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$formdata['journalDate'] = $_POST['journalDate'];
		$formdata['referenceNumber'] = $_POST['referenceNumber'];
		$formdata['notes'] = $_POST['notes'];
		$formdata['entryType'] = $_POST['entryType'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$oldamountQuery = pro_db_query("select amountCredited, creditAccountID, debitAccountID from accountJVSubMaster where accountJVSubID = " . $_POST['accountJVSubID']);
		$oldamountrs = pro_db_fetch_array($oldamountQuery);
		$oldAmount = $oldamountrs['amountCredited'];
		$oldcreditAccountID = $oldamountrs['creditAccountID'];
		$olddebitAccountID = $oldamountrs['debitAccountID'];

		$whr = "accountJVID = " . $_POST['accountJVID'];

		if (pro_db_perform('accountJVMaster', $formdata, 'update', $whr)) {
			$where = "accountJVSubID = " . $_POST['accountJVSubID'];
			$subJVdata = array();
			$subJVdata['accountJVID'] = $_POST['accountJVID'];

			//CREDIT ACCOUNT DETAILS
			$creditAccountID = $_POST['creditAccountID'];
			$dcreditaccounttypeqry = pro_db_query("select accountGroupID from accountMaster where accountID = " . $_POST['creditAccountID']);
			$creditaccounttypers = pro_db_fetch_array($dcreditaccounttypeqry);
			$creditAccountGroupID = $creditaccounttypers['accountGroupID'];

			//DEBIT ACCOUNT DETAILS
			$debitAccountID = $_POST['debitAccountID'];
			$debitaccounttypeqry = pro_db_query("select accountGroupID from accountMaster where accountID = " . $_POST['debitAccountID']);
			$debitaccounttypers = pro_db_fetch_array($debitaccounttypeqry);
			$debitAccountGroupID = $debitaccounttypers['accountGroupID'];

			//submaster entry
			$subJVdata['creditAccountID'] = $creditAccountID;
			$subJVdata['creditAccountGroupID'] = $creditAccountGroupID;
			$subJVdata['debitAccountID'] = $debitAccountID;
			$subJVdata['debitAccountGroupID'] = $debitAccountGroupID;
			$subJVdata['username'] = $_SESSION['username'];
			$subJVdata['modifieddate'] = date('Y-m-d H:i:s');
			$subJVdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$subJVdata['status'] = 1;

			pro_db_perform('accountJVSubMaster', $subJVdata, 'update', $where);

			//delete entries from dteails first
			pro_db_query("delete from accountJVDetails where accountJVID = " . $_POST['accountJVID']);

			//Description and amount for details table
			$totalamount = 0;
			if (isset($_POST['description']) && isset($_POST['amount'])) {
				$description = $_POST['description'];
				$amount = $_POST['amount'];

				foreach ($description as $key => $value) {
					//JV details entry
					$jvDetails['accountJVID'] = $_POST['accountJVID'];
					$jvDetails['accountJVSubID'] = $_POST['accountJVSubID'];
					$jvDetails['description'] = $value;
					$jvDetails['amount'] = $amount[$key];
					$jvDetails['username'] = $_SESSION['username'];
					$jvDetails['createdate'] = date('Y-m-d H:i:s');
					$jvDetails['modifieddate'] = date('Y-m-d H:i:s');
					$jvDetails['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$jvDetails['status'] = 1;
					pro_db_perform('accountJVDetails', $jvDetails);
					$accountJVDetailsID = pro_db_insert_id();

					//total amount for insert into master tables	
					$totalamount += $amount[$key];
				}
			}

			$totalAmountCredited = $totalamount;
			$totalAmountDebited = $totalamount;

			$totalamount;
			$where;
			//amount entry into JV sub master
			$sql = "update accountJVSubMaster set amount = " . $totalamount . ", amountCredited = " . $totalAmountCredited . ",
					amountDebited = " . $totalAmountDebited . " where accountJVSubID = " . $_POST['accountJVSubID'];
			pro_db_query($sql);

			//amount entry into JV master
			pro_db_query("update accountJVMaster set amount = " . $totalamount . " where accountJVID = " . $_POST['accountJVID']);

			//Transaction Master
			pro_db_query("update accountTransactionMaster set amount = " . $totalamount . " where referenceID = " . $_POST['accountJVID']);

			//SubTransaction 
			$subtransactionIDquery = pro_db_query("select transactionID from accountTransactionMaster where referenceID = " . $_POST['accountJVID']);
			$subtransactionrs = pro_db_fetch_array($subtransactionIDquery);
			$queryTransactionID = $subtransactionrs['transactionID'];
			$subtransactionwhr = "transactionID = " . $queryTransactionID;

			$masterTransactiondata = array();
			$masterTransactiondata['transactionDate'] = $_POST['journalDate'];
			$masterTransactiondata['referenceNumber'] = $_POST['referenceNumber'];
			$masterTransactiondata['notes'] = $_POST['notes'];
			$masterTransactiondata['transactionType'] = $_POST['entryType'];
			$masterTransactiondata['username'] = $_SESSION['username'];
			$masterTransactiondata['modifieddate'] = date('Y-m-d H:i:s');
			$masterTransactiondata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('accountTransactionMaster', $masterTransactiondata, 'update', $subtransactionwhr);

			$subTransactiondata = array();
			$subTransactiondata['creditAccountID'] = $creditAccountID;
			$subTransactiondata['creditAccountGroupID'] = $creditAccountGroupID;
			$subTransactiondata['amountCredited'] = $totalAmountCredited;
			$subTransactiondata['debitAccountID'] = $debitAccountID;
			$subTransactiondata['debitAccountGroupID'] = $debitAccountGroupID;
			$subTransactiondata['amountDebited'] = $totalAmountDebited;
			$subTransactiondata['amount'] = $totalamount;
			$subTransactiondata['username'] = $_SESSION['username'];
			$subTransactiondata['createdate'] = date('Y-m-d H:i:s');
			$subTransactiondata['modifieddate'] = date('Y-m-d H:i:s');
			$subTransactiondata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			$subTransactiondata['status'] = 1;
			pro_db_perform('accountTransactionSubMaster', $subTransactiondata, 'update', $subtransactionwhr);

			$detailsTransactionIDquery = pro_db_query("select transactionSubID from accountTransactionSubMaster where transactionID = " . $queryTransactionID);
			$detailsTransactionrs = pro_db_fetch_array($detailsTransactionIDquery);
			$queryTransactionSubID = $detailsTransactionrs['transactionSubID'];

			//delete entries from details first
			pro_db_query("delete from accountTransactionDetails where transactionID = " . $queryTransactionID);

			//Transaction Details
			if (isset($_POST['description']) && isset($_POST['amount'])) {
				$description = $_POST['description'];
				$amount = $_POST['amount'];

				foreach ($description as $key => $value) {
					//JV details entry
					$transactionDetails['transactionID'] = $queryTransactionID;
					$transactionDetails['transactionSubID'] = $queryTransactionSubID;
					$transactionDetails['description'] = $value;
					$transactionDetails['amount'] = $amount[$key];
					$transactionDetails['username'] = $_SESSION['username'];
					$transactionDetails['createdate'] = date('Y-m-d H:i:s');
					$transactionDetails['modifieddate'] = date('Y-m-d H:i:s');
					$transactionDetails['remote_ip'] = $_SERVER['REMOTE_ADDR'];
					$transactionDetails['status'] = 1;
					pro_db_perform('accountTransactionDetails', $transactionDetails);
					$transactionDetailsID = pro_db_insert_id();
				}
			}

			if ($oldcreditAccountID == $_POST['creditAccountID']) {
				if ($oldAmount != $totalAmountCredited) {
					//credit entry for acccount balance master
					$creditbalancemasterqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $_POST['creditAccountID']);
					$creditbalancers = pro_db_fetch_array($creditbalancemasterqry);
					if ($oldAmount > $totalAmountCredited) {
						$finalBalanceCreditAmount = $oldAmount - $totalAmountCredited;
						$balanceTotalAmountCredited = $creditbalancers['totalAmountCredited'] - $finalBalanceCreditAmount;
					} else {
						$finalBalanceCreditAmount = $totalAmountCredited - $oldAmount;
						$balanceTotalAmountCredited = $creditbalancers['totalAmountCredited'] + $finalBalanceCreditAmount;
					}
					//$balanceTotalAmountCredited = $totalAmountCredited + $creditbalancers['totalAmountCredited'];
					$creditAdjustmentAmount = 0;
					if ($balanceTotalAmountCredited > $creditbalancers['totalAmountDebited']) {
						$creditAdjustmentAmount = $balanceTotalAmountCredited - $creditbalancers['totalAmountDebited'];
					} else {
						$creditAdjustmentAmount = $creditbalancers['totalAmountDebited'] - $balanceTotalAmountCredited;
					}
					pro_db_query("update accountBalanceMaster set totalAmountCredited = " . $balanceTotalAmountCredited . ",
								adjustmentAmount = " . $creditAdjustmentAmount . " where accountID = " . $_POST['creditAccountID']);
				}
			} else {
				//old Account Settlement
				$oldcreditbalanceqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $oldcreditAccountID);
				$oldcreditbalancers = pro_db_fetch_array($oldcreditbalanceqry);
				$oldaccountCreditBalanceSettle = $oldcreditbalancers['totalAmountCredited'] - $oldAmount;
				$oldaccountCreditAdjustAmountSettle = 0;
				if ($oldaccountCreditBalanceSettle > $oldcreditbalancers['totalAmountDebited']) {
					$oldaccountCreditAdjustAmountSettle = $oldaccountCreditBalanceSettle - $oldcreditbalancers['totalAmountDebited'];
				} else {
					$oldaccountCreditAdjustAmountSettle = $oldcreditbalancers['totalAmountDebited'] - $oldaccountCreditBalanceSettle;
				}
				pro_db_query("update accountBalanceMaster set totalAmountCredited=" . $oldaccountCreditBalanceSettle . ",
							adjustmentAmount=" . $oldaccountCreditAdjustAmountSettle . "
							where accountID = " . $oldcreditAccountID);

				//new account settlement
				$creditbalancemasterqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $_POST['creditAccountID']);
				$creditbalancers = pro_db_fetch_array($creditbalancemasterqry);
				$balanceTotalAmountCredited = $totalAmountCredited + $creditbalancers['totalAmountCredited'];
				$creditAdjustmentAmount = 0;
				if ($balanceTotalAmountCredited > $creditbalancers['totalAmountDebited']) {
					$creditAdjustmentAmount = $balanceTotalAmountCredited - $creditbalancers['totalAmountDebited'];
				} else {
					$creditAdjustmentAmount = $creditbalancers['totalAmountDebited'] - $balanceTotalAmountCredited;
				}
				pro_db_query("update accountBalanceMaster set totalAmountCredited = " . $balanceTotalAmountCredited . ",
							adjustmentAmount = " . $creditAdjustmentAmount . " where accountID = " . $_POST['creditAccountID']);
			}


			if ($olddebitAccountID == $_POST['debitAccountID']) {
				if ($oldAmount != $totalAmountDebited) {
					//Debit entry for acccount balance master
					$debitbalancemasterqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $_POST['debitAccountID']);
					$debitbalancers = pro_db_fetch_array($debitbalancemasterqry);
					// $balanceTotalAmountDebited = $totalAmountDebited + $debitbalancers['totalAmountDebited'];
					$debitAdjustmentAmount = 0;
					if ($oldAmount > $totalAmountDebited) {
						$finalBalanceDebitAmount = $oldAmount - $totalAmountDebited;
						$balanceTotalAmountDebited = $debitbalancers['totalAmountDebited'] - $finalBalanceDebitAmount;
					} else {
						$finalBalanceDebitAmount = $totalAmountDebited - $oldAmount;
						$balanceTotalAmountDebited = $debitbalancers['totalAmountDebited'] + $finalBalanceDebitAmount;
					}

					if ($balanceTotalAmountDebited > $debitbalancers['totalAmountCredited']) {
						$debitAdjustmentAmount = $balanceTotalAmountDebited - $debitbalancers['totalAmountCredited'];
					} else {
						$debitAdjustmentAmount = $debitbalancers['totalAmountCredited'] - $balanceTotalAmountDebited;
					}
					pro_db_query("update accountBalanceMaster set totalAmountDebited = " . $balanceTotalAmountDebited . ",
								adjustmentAmount = " . $debitAdjustmentAmount . " where accountID = " . $_POST['debitAccountID']);
				}
			} else {
				//old Account Settlement
				$olddebitbalanceqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $olddebitAccountID);
				$olddebitbalancers = pro_db_fetch_array($olddebitbalanceqry);
				$olddebitAccountID;
				$olddebitbalancers['totalAmountDebited'];
				$oldaccountDebitBalanceSettle = $olddebitbalancers['totalAmountDebited'] - $oldAmount;
				$oldaccountDebitAdjustAmountSettle = 0;
				if ($oldaccountDebitBalanceSettle > $olddebitbalancers['totalAmountCredited']) {
					$oldaccountDebitAdjustAmountSettle = $oldaccountDebitBalanceSettle - $olddebitbalancers['totalAmountCredited'];
				} else {
					$oldaccountDebitAdjustAmountSettle = $olddebitbalancers['totalAmountCredited'] - $oldaccountDebitBalanceSettle;
				}
				pro_db_query("update accountBalanceMaster set totalAmountDebited=" . $oldaccountDebitBalanceSettle . ",
							adjustmentAmount=" . $oldaccountDebitAdjustAmountSettle . "
							where accountID = " . $olddebitAccountID);

				//New Debit entry for acccount balance master
				$debitbalancemasterqry = pro_db_query("select * from accountBalanceMaster where accountID = " . $_POST['debitAccountID']);
				$debitbalancers = pro_db_fetch_array($debitbalancemasterqry);
				$balanceTotalAmountDebited = $totalAmountDebited + $debitbalancers['totalAmountDebited'];
				$debitAdjustmentAmount = 0;
				if ($balanceTotalAmountDebited > $debitbalancers['totalAmountCredited']) {
					$debitAdjustmentAmount = $balanceTotalAmountDebited - $debitbalancers['totalAmountCredited'];
				} else {
					$debitAdjustmentAmount = $debitbalancers['totalAmountCredited'] - $balanceTotalAmountDebited;
				}
				pro_db_query("update accountBalanceMaster set totalAmountDebited = " . $balanceTotalAmountDebited . ",
							adjustmentAmount = " . $debitAdjustmentAmount . " where accountID = " . $_POST['debitAccountID']);
			}

			//Response
			$msg = '<p class="bg-success p-3">Journal Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Journal Detail is not updated!!!!!!</p>';
		}

		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "update accountJVMaster set status = 126 where accountJVID = " . (int)$_GET['accountJVID'];
		pro_db_query("update accountJVSubMaster set status = 126 where accountJVID = " . (int)$_GET['accountJVID']);
		pro_db_query("update accountJVDetails set status = 126 where accountJVID = " . (int)$_GET['accountJVID']);

		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Journal Voucher has been deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Journal Voucher has not been deleted successfully</p>';
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
				<h4>Journal Vouchers</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Voucher</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="accountJVMasterList" width="100%">
								<thead>
									<tr>
										<th>Type</th>
										<th>Entry Date</th>
										<th>Cr. A/c Group</th>
										<th>Cr. A/c</th>
										<th>Dr. A/c Group</th>
										<th>Dr. A/c</th>
										<th>Amount</th>
										<th>Notes</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Type</th>
										<th>Entry Date</th>
										<th>Cr. A/c Group</th>
										<th>Cr. A/c</th>
										<th>Dr. A/c Group</th>
										<th>Dr. A/c</th>
										<th>Amount</th>
										<th>Notes</th>
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
			var listURL = 'helperfunc/accountJVMasterList.php';
			$('#accountJVMasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "accountJVMaster"
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
}
?>