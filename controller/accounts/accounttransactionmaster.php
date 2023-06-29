<?php
class accounttransactionmaster
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

	public function editForm()
	{
		$queryString = pro_db_query("select atm.transactionID, atm.referenceID, atm.societyID, atm.financialYearID,
									atm.transactionDate, atm.amount, atm.notes, atm.referenceNumber, atm.status, atsm.transactionSubID,
									acm.accountName as creditAccount, actm.groupName as creditAccountGroup, atsm.amountCredited,
									adm.accountName as debitAccount, adtm.groupName as debitAccountGroup, atsm.amountDebited
									from accountTransactionMaster atm
									join accountTransactionSubMaster atsm on atm.transactionID = atsm.transactionID
									join accountMaster acm on atsm.creditAccountID = acm.accountID
									join accountGroupMaster actm on atsm.creditAccountGroupID = actm.accountGroupID
									join accountMaster adm on atsm.debitAccountID = adm.accountID
									join accountGroupMaster adtm on atsm.debitAccountGroupID = adtm.accountGroupID
									where atm.transactionID = " . (int)$_REQUEST['transactionID']);
		$rs = pro_db_fetch_array($queryString);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Transaction</h4>
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
									<input type="text" class="form-control voucherDateTime" placeholder="" value="<?php echo $rs['transactionDate']; ?>">
								</div>
								<div class="form-group col-sm-2">
									<label>Reference#</label>
									<input type="text" name="referenceNumber" class="form-control" placeholder="" value="<?php echo $rs['referenceNumber']; ?>" required>
								</div>
								<div class="form-group col-sm-8">
									<label>Notes:</label>
									<input type="text" name="notes" class="form-control" placeholder="" value="<?php echo $rs['notes']; ?>">
								</div>
							</div>
							<br>
							<h4>Accounts:</h4>
							<br>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Credit Account:</label>
									<input type="text" class="form-control" value="<?php echo $rs['creditAccount']; ?>" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Amount Credited</label>
									<input type="text" class="form-control" value="<?php echo $rs['amountCredited']; ?>" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Debit Account:</label>
									<input type="text" class="form-control" value="<?php echo $rs['debitAccount']; ?>" readonly>
								</div>
								<div class="form-group col-sm-3">
									<label>Amount Debited</label>
									<input type="text" class="form-control" value="<?php echo $rs['amountDebited']; ?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<label></label>
									<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
									<input type="hidden" name="transactionID" value="<?php echo $rs['transactionID']; ?>">
									<input type="hidden" name="transactionSubID" value="<?php echo $rs['transactionSubID']; ?>">
									<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
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

	public function edit()
	{
		global $frmMsgDialog;
		$formdata['referenceNumber'] = $_POST['referenceNumber'];
		$formdata['notes'] = $_POST['notes'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$whr = "transactionID = " . $_POST['transactionID'];

		if (pro_db_perform('accountTransactionMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success p-3">Journal Detail is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Journal Detail is not updated!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Account Transactions</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="accountTransactionMasterList" width="100%">
								<thead>
									<tr>
										<th>Transaction Date</th>
										<th>Cr. A/c Group</th>
										<th>Cr. A/c</th>
										<th>Dr. A/c Group</th>
										<th>Dr. A/c</th>
										<th>Amount</th>
										<th>Transaction Notes</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Transaction Date</th>
										<th>Cr. A/c Group</th>
										<th>Cr. A/c</th>
										<th>Dr. A/c Group</th>
										<th>Dr. A/c</th>
										<th>Amount</th>
										<th>Transaction Notes</th>
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
			var listURL = 'helperfunc/accountTransactionMasterList.php';
			$('#accountTransactionMasterList').dataTable({
				"ajax": listURL,
				"deferRender": true,
				"iDisplayLength": 50,
				"stateSave": true,
				"order": []
			});
			$('.table').editable({
				selector: 'a.estatus,a.esortorder',
				params: {
					"tblName": "accountTransactionMasterList"
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