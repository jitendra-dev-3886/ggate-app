<?php
class profitandlossmaster
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
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=profitandloss'; ?>" method="post" enctype="multipart/form-data">
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

	public function profitandloss()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
		$financialYearID = $_REQUEST['financialYearID'];
		$arrDrAllAccounts = array();
		$arrCrAllAccounts = array();
		$totalDifference = 0.0;
		$totalAmount = 0.0;

		//Debit Amounts
		$dictDebits = array();
		$totalDebitAmount = 0.0;

		$sqlDebitAmount = pro_db_query("select abm.adjustmentAmount as totalDebit,abm.accountID as debitAccountID from accountBalanceMaster abm
										join accountHeadMaster ahm on ahm.accountHeadID = abm.accountHeadID
										where abm.financialYearID = " . $financialYearID . " and abm.societyID = " . $_SESSION['societyID'] . "
										and abm.adjustmentAmount > 0 and ahm.headType = 1 and ahm.headSide = 0");
		while ($res = pro_db_fetch_array($sqlDebitAmount)) {
			$dictDebits[$res['debitAccountID']] = $res['totalDebit'];
			$totalDebitAmount += $res['totalDebit'];
		}

		//Credit Amounts
		$dictCredits = array();
		$totalCreditAmount = 0.0;
		$sqlCreditAmount = pro_db_query("select abm.adjustmentAmount as totalCredit,abm.accountID as creditAccountID from accountBalanceMaster abm
										join accountHeadMaster ahm on ahm.accountHeadID = abm.accountHeadID
										where abm.financialYearID = " . $financialYearID . " and abm.societyID = " . $_SESSION['societyID'] . "
										and abm.adjustmentAmount > 0 and ahm.headType = 1 and ahm.headSide = 1");
		while ($res = pro_db_fetch_array($sqlCreditAmount)) {
			$dictCredits[$res['creditAccountID']] = $res['totalCredit'];
			$totalCreditAmount += $res['totalCredit'];
		}

		//Head Accounts
		$queryHead = pro_db_query("select DISTINCT agm.accountGroupID, agm.groupName, abm.accountHeadID, ahm.headSide from accountBalanceMaster abm
									join accountGroupMaster agm on agm.accountGroupID = abm.accountGroupID
									join accountHeadMaster ahm on ahm.accountHeadID = abm.accountHeadID
									where abm.financialYearID = " . $financialYearID . " and abm.societyID = " . $_SESSION['societyID'] . "
									and abm.adjustmentAmount > 0 and ahm.headType = 1");
		while ($res = pro_db_fetch_array($queryHead)) {
			$headDebitAmount = 0.0;
			$headCreditAmount = 0.0;

			$arrDrSubAccounts = array();
			$arrCrSubAccounts = array();

			//Sub Account
			$arrSubAccounts = array();
			$queryAccount = pro_db_query("select distinct am.accountID, am.accountName, ahm.accountHeadID, ahm.headSide from accountMaster am
										join accountBalanceMaster abm on abm.accountID = am.accountID
										join accountHeadMaster ahm on ahm.accountHeadID = abm.accountHeadID
										where abm.accountGroupID = " . $res['accountGroupID'] . " and  abm.societyID = " . $_SESSION['societyID'] . "
										and abm.financialYearID = " . $financialYearID . " and abm.adjustmentAmount > 0 and ahm.headType = 1");
			while ($resAccount = pro_db_fetch_array($queryAccount)) {
				$accountID = $resAccount['accountID'];
				$debitAmount = isset($dictDebits[$accountID]) ? $dictDebits[$accountID] : '';
				$creditAmount = isset($dictCredits[$accountID]) ? $dictCredits[$accountID] : '';
				if ($resAccount['headSide'] == 0) {
					$objDrSubAccount =
						array(
							'title' => $resAccount['accountName'],
							'amtDebit' => $debitAmount,
							'amtCredit' => $creditAmount,
							'isHead' => false
						);
					$arrDrSubAccounts[] = $objDrSubAccount;
				} else {
					$objCrSubAccount =
						array(
							'title' => $resAccount['accountName'],
							'amtDebit' => $debitAmount,
							'amtCredit' => $creditAmount,
							'isHead' => false
						);
					$arrCrSubAccounts[] = $objCrSubAccount;
				}

				//Total Amount
				if ($debitAmount > 0) {
					$headDebitAmount += $debitAmount;
				}
				if ($creditAmount > 0) {
					$headCreditAmount += $creditAmount;
				}
			}

			//Append Main Account
			if ($res['headSide'] == 0) {
				$objDrHeadAccount =
					array(
						'title' => $res['groupName'],
						'amtDebit' => $headDebitAmount > 0 ? number_format($headDebitAmount, 2, '.', '') : '',
						'amtCredit' => $headCreditAmount > 0 ? number_format($headCreditAmount, 2, '.', '') : '',
						'isHead' => true
					);
				$arrDrAllAccounts[] = $objDrHeadAccount;
				//Append Sub Accounts
				foreach ($arrDrSubAccounts as $drSubAccount) {
					$arrDrAllAccounts[] = $drSubAccount;
				}
			} else {
				$objCrHeadAccount =
					array(
						'title' => $res['groupName'],
						'amtDebit' => $headDebitAmount > 0 ?  number_format($headDebitAmount, 2, '.', '') : '',
						'amtCredit' => $headCreditAmount > 0 ?  number_format($headCreditAmount, 2, '.', '') : '',
						'isHead' => true
					);
				$arrCrAllAccounts[] = $objCrHeadAccount;
				//Append Sub Accounts
				foreach ($arrCrSubAccounts as $crSubAccount) {
					$arrCrAllAccounts[] = $crSubAccount;
				}
			}
		}

		//Total Difference & Amount
		$totalDifference = $totalCreditAmount - $totalDebitAmount;
		$totalAmount = ($totalCreditAmount > $totalDebitAmount) ? number_format($totalCreditAmount, 2, '.', '') : number_format($totalDebitAmount, 2, '.', '');
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4 style="font-size: 23px;" align="center">
					<?php $societyqry = pro_db_query("select societyName from societyMaster where societyID = " . $_SESSION['societyID']);
					$societyrs = pro_db_fetch_array($societyqry);
					echo $societyrs['societyName']; ?>
				</h4>
				<h4 style="font-size: 20px;" align="center">Profit & Loss</h4>
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
										<tr>
											<th style="font-size:medium; text-align:left; width:30%; text-decoration: underline;">DEBIT</th>
											<th style="width:18%;"></th>
											<th style="width:4%;"></th>
											<th style="width:30%;"></th>
											<th style="font-size:medium; text-align:right; width:18%; text-decoration: underline;">CREDIT</th>
										</tr>
										<?php
										$totalRows = (count($arrCrAllAccounts) > count($arrDrAllAccounts)) ? count($arrCrAllAccounts) : count($arrDrAllAccounts);
										for ($i = 0; $i < $totalRows; $i++) {
											$objDebitAccount = null;
											$objCreditAccount = null;
											if (isset($arrDrAllAccounts[$i])) {
												$objDebitAccount = $arrDrAllAccounts[$i];
											}
											if (isset($arrCrAllAccounts[$i])) {
												$objCreditAccount = $arrCrAllAccounts[$i];
											}
										?>
											<tr>
												<?php
												if ($objDebitAccount != null) {
												?>
													<td <?php if ($objDebitAccount['isHead']) { ?> style="font-weight: bold; font-size: medium;" <?php } ?>>
														<?php if ($objDebitAccount['isHead']) {
															echo $objDebitAccount['title'];
														} else { ?>
															<li style="margin-left: 20px; font-weight: 200; font-size: 16px;">
																<?php echo $objDebitAccount['title']; ?>
															</li>
														<?php
														}
														?>
													</td>
													<td align="right" <?php if ($objDebitAccount['isHead']) { ?> style="font-weight: bold; font-size: medium; text-decoration: underline;" <?php } else { ?> style="font-weight:200; font-size: 16px;" <?php } ?>>
														<?php echo $objDebitAccount['amtDebit']; ?>
													</td>
												<?php
												} else {
												?>
													<td></td>
													<td></td>
												<?php
												}
												?>
												<td></td>
												<?php
												if ($objCreditAccount != null) {
												?>
													<td <?php if ($objCreditAccount['isHead']) { ?> style="font-weight: bold; font-size: medium;" <?php } ?>>
														<?php if ($objCreditAccount['isHead']) {
															echo $objCreditAccount['title'];
														} else { ?>
															<li style="margin-left: 20px; font-weight: 200; font-size: 16px;">
																<?php echo $objCreditAccount['title']; ?>
															</li>
														<?php
														}
														?>
													</td>
													<td align="right" <?php if ($objCreditAccount['isHead']) { ?> style="font-weight: bold; font-size: medium; text-decoration: underline;" <?php } else { ?> style="font-weight:200; font-size: 16px;" <?php } ?>>
														<?php echo $objCreditAccount['amtCredit']; ?>
													</td>
												<?php
												} else {
												?>
													<td></td>
													<td></td>
												<?php
												}
												?>
											</tr>
										<?php
										}
										?>
										<tr>
											<td style="padding-bottom:30px;"></td>
										</tr>
										<?php
										if ($totalDifference != 0) {
										?>
											<tr>
												<?php
												if ($totalDifference > 0) {
												?>
													<td style="font-weight: bold; font-size: medium;">Net Profit</td>
													<td align="right" style="font-weight: bold; font-size: medium; text-decoration: underline;">
														<?php echo number_format(abs($totalDifference), 2, '.', ''); ?>
													</td>
													<td></td>
													<td></td>
													<td></td>
												<?php
												} else {
												?>
													<td></td>
													<td></td>
													<td></td>
													<td style="font-weight: bold; font-size: medium;">Net Loss</td>
													<td align="right" style="font-weight: bold; font-size: medium; text-decoration: underline;">
														<?php echo number_format(abs($totalDifference), 2, '.', ''); ?>
													</td>
												<?php
												}
												?>
											</tr>
										<?php
										}
										?>
										<tr style="background:antiquewhite;">
											<td style="font-weight: bold; font-size: medium;">TOTAL</td>
											<td align="right" style="font-weight: bold; font-size: medium; text-decoration: underline;">
												<?php echo $totalAmount; ?>
											</td>
											<td></td>
											<td style="font-weight: bold; font-size: medium;">TOTAL</td>
											<td align="right" style="font-weight: bold; font-size: medium; text-decoration: underline;">
												<?php echo $totalAmount; ?>
											</td>
										</tr>
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