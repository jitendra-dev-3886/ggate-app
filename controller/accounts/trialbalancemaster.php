<?php
class trialbalancemaster
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

	public function listData()
	{
		$financialYearID = generateOptions(getMasterList('accountFinancialYear', 'financialYearID', 'year'));
?>
		<div class="well quickForm" id="searchForm">
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=trialBalance'; ?>" method="post" enctype="multipart/form-data">
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
						<button type="submit" class="btn btn-success">Submit</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Reset" data-url="<?php echo $this->redirectUrl; ?>">Reset</button>
					</div>
				</div>
			</form>
		</div>
	<?php
	}

	public function trialBalance()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
		$financialYearID = $_REQUEST['financialYearID'];
		$arrAllAccounts = array();

		//Debit Amounts
		$dictDebits = array();
		$totalDebitAmount = 0.0;
		$sqlDebitAmount = pro_db_query("select abm.accountID as debitAccountID, abm.adjustmentAmount as totalDebit
										from accountBalanceMaster abm
										where abm.societyID = " . $_SESSION['societyID'] . " and abm.financialYearID = " . $financialYearID . "
										and abm.totalAmountDebited > abm.totalAmountCredited");
		while ($res = pro_db_fetch_array($sqlDebitAmount)) {
			$dictDebits[$res['debitAccountID']] = $res['totalDebit'];
			$totalDebitAmount += $res['totalDebit'];
		}

		//Credit Amounts
		$dictCredits = array();
		$totalCreditAmount = 0.0;
		$sqlCreditAmount = pro_db_query("select abm.accountID as creditAccountID, abm.adjustmentAmount as totalCredit
										from accountBalanceMaster abm
										where abm.societyID = " . $_SESSION['societyID'] . " and abm.financialYearID = " . $financialYearID . "
										and abm.totalAmountDebited < abm.totalAmountCredited");
		while ($res = pro_db_fetch_array($sqlCreditAmount)) {
			$dictCredits[$res['creditAccountID']] = $res['totalCredit'];
			$totalCreditAmount += $res['totalCredit'];
		}

		//Head Accounts
		$queryHead = pro_db_query("select DISTINCT agm.accountGroupID, agm.groupName, abm.accountHeadID, ahm.headSide
								from accountBalanceMaster abm
								join accountGroupMaster agm on agm.accountGroupID = abm.accountGroupID
								join accountHeadMaster ahm on ahm.accountHeadID = abm.accountHeadID
								where abm.financialYearID = " . $financialYearID . " and abm.societyID = " . $_SESSION['societyID'] . "
								and abm.adjustmentAmount > 0 ");
		while ($res = pro_db_fetch_array($queryHead)) {
			$headDebitAmount = 0.0;
			$headCreditAmount = 0.0;

			//Sub Account
			$arrSubAccounts = array();
			$queryAccount = pro_db_query("select distinct am.accountID, am.accountName, ahm.accountHeadID, ahm.headSide
										from accountMaster am
										join accountBalanceMaster abm on abm.accountID = am.accountID
										join accountHeadMaster ahm on ahm.accountHeadID = abm.accountHeadID
										where abm.accountGroupID = " . $res['accountGroupID'] . " and  abm.societyID = " . $_SESSION['societyID'] . " and 
										abm.financialYearID = " . $financialYearID . " and abm.adjustmentAmount > 0");
			while ($resAccount = pro_db_fetch_array($queryAccount)) {
				$accountID = $resAccount['accountID'];
				$debitAmount = isset($dictDebits[$accountID]) ? $dictDebits[$accountID] : '';
				$creditAmount = isset($dictCredits[$accountID]) ? $dictCredits[$accountID] : '';
				$objSubAccount =
					array(
						'title' => $resAccount['accountName'],
						'amtDebit' => $debitAmount,
						'amtCredit' => $creditAmount,
						'isHead' => false
					);
				$arrSubAccounts[] = $objSubAccount;
				//Total Amount
				if ($debitAmount > 0) {
					$headDebitAmount += $debitAmount;
				}
				if ($creditAmount > 0) {
					$headCreditAmount += $creditAmount;
				}
			}
			//Append Main Account
			$objHeadAccount =
				array(
					'title' => $res['groupName'],
					'amtDebit' =>  $headDebitAmount > 0 ? number_format($headDebitAmount, 2, '.', '') : '',
					'amtCredit' => $headCreditAmount > 0 ? number_format($headCreditAmount, 2, '.', '') : '',
					'isHead' => true
				);
			$arrAllAccounts[] = $objHeadAccount;
			//Append Sub Accounts
			foreach ($arrSubAccounts as $subAccount) {
				$arrAllAccounts[] = $subAccount;
			}
		}
		//Append Final Total Amount
		$objHeadAccount =
			array(
				'title' => 'Grand Total',
				'amtDebit' => $totalDebitAmount > 0 ? number_format($totalDebitAmount, 2, '.', '') : '',
				'amtCredit' => $totalCreditAmount > 0 ? number_format($totalCreditAmount, 2, '.', '') : '',
				'isHead' => true
			);
		$arrAllAccounts[] = $objHeadAccount;

	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4 style="font-size: 23px;" align="center">
					<?php $societyqry = pro_db_query("select societyName from societyMaster where societyID = " . $_SESSION['societyID']);
					$societyrs = pro_db_fetch_array($societyqry);
					echo $societyrs['societyName']; ?>
				</h4>
				<h4 style="font-size: 20px;" align="center">Trial Balance</h4>
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
										<tr style=" text-align: center">
											<th rowspan="2"></th>
											<th colspan="2" style="font-size:16px;">Opening Balance</th>
										</tr>
										<tr class="odd" style="text-align: right; width:25%; background: #F1F3FF;">
											<th>Debit</th>
											<th>Credit</td>
										</tr>
										<?php
										foreach ($arrAllAccounts as $objAccount) {
										?>
											<tr <?php if ($objAccount['title'] == 'Grand Total') { ?> style="background:antiquewhite;" <?php } ?>>
												<td <?php if ($objAccount['isHead']) { ?> style="font-weight: bold;  font-size: large;" <?php } ?>>
													<?php if ($objAccount['isHead']) {
														echo $objAccount['title'];
													} else { ?>
														<li style="margin-left: 20px; font-weight: 200;  font-size: 16px;">
															<?php echo $objAccount['title']; ?>
														</li>
													<?php
													}
													?>
												</td>
												<td align="right" <?php if ($objAccount['isHead']) { ?> style="font-weight: bold; font-size: large; text-decoration: underline;" <?php } else { ?> style="font-weight:200; font-size: 16px;" <?php } ?>>
													<?php echo $objAccount['amtDebit']; ?>
												</td>
												<td align="right" <?php if ($objAccount['isHead']) { ?> style="font-weight: bold; font-size: large; text-decoration: underline;" <?php } else { ?> style="font-weight:200; font-size: 16px;" <?php } ?>>
													<?php echo $objAccount['amtCredit']; ?>
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