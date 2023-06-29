<?php
class balancesheetmaster
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
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=balancesheet'; ?>" method="post" enctype="multipart/form-data">
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

	public function balancesheet()
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
		$sqlDebitAmount = pro_db_query("select agm.accountGroupID as debitAccountGroupID, sum(abm.totalAmountDebited) as totalAmountDebited,
										sum(abm.totalAmountCredited) as totalAmountCredited from accountBalanceMaster abm
										join accountGroupMaster agm on agm.accountGroupID = abm.accountGroupID
                                        join accountHeadMaster ah on ah.accountHeadID = abm.accountHeadID
										where abm.societyID = " . $_SESSION['societyID'] . " and abm.financialYearID = " . $financialYearID . "
										and ah.headType = 0 and (abm.totalAmountDebited > 0 or totalAmountCredited > 0) and ah.headSide = 0
										group by abm.accountGroupID");
		while ($res = pro_db_fetch_array($sqlDebitAmount)) {
			$groupDebitAmount = 0.0;
			if ($res['totalAmountDebited'] > $res['totalAmountCredited']) {
				$groupDebitAmount = $res['totalAmountDebited'] - $res['totalAmountCredited'];
			} else if ($res['totalAmountCredited'] > $res['totalAmountDebited']) {
				$groupDebitAmount = $res['totalAmountCredited'] - $res['totalAmountDebited'];
			} else {
				$groupDebitAmount = $res['totalAmountCredited'];
			}
			$dictDebits[$res['debitAccountGroupID']] = $groupDebitAmount;
			$totalDebitAmount += $groupDebitAmount;
		}

		//Credit Amounts
		$dictCredits = array();
		$totalCreditAmount = 0.0;
		$sqlCreditAmount = pro_db_query("select agm.accountGroupID as creditAccountGroupID, sum(abm.totalAmountDebited) as totalAmountDebited,
										sum(abm.totalAmountCredited) as totalAmountCredited from accountBalanceMaster abm
										join accountGroupMaster agm on agm.accountGroupID = abm.accountGroupID
                                        join accountHeadMaster ah on ah.accountHeadID = abm.accountHeadID
										where abm.societyID = " . $_SESSION['societyID'] . " and abm.financialYearID = " . $financialYearID . "
										and ah.headType = 0 and (abm.totalAmountDebited > 0 or totalAmountCredited > 0) and ah.headSide = 1
										group by abm.accountGroupID");
		while ($res = pro_db_fetch_array($sqlCreditAmount)) {
			$groupCreditAmount = 0.0;
			if ($res['totalAmountDebited'] > $res['totalAmountCredited']) {
				$groupCreditAmount = $res['totalAmountDebited'] - $res['totalAmountCredited'];
			} else if ($res['totalAmountCredited'] > $res['totalAmountDebited']) {
				$groupCreditAmount = $res['totalAmountCredited'] - $res['totalAmountDebited'];
			} else {
				$groupCreditAmount = $res['totalAmountCredited'];
			}
			$dictCredits[$res['creditAccountGroupID']] = $groupCreditAmount;
			$totalCreditAmount += $groupCreditAmount;
		}

		//Head Accounts
		$queryHead = pro_db_query("select ah.headName, ah.headSide , ah.accountHeadID from accountBalanceMaster abm
								join accountHeadMaster ah on abm.accountHeadID = ah.accountHeadID
								where abm.financialYearID = " . $financialYearID . " and abm.societyID = " . $_SESSION['societyID'] . " and 
								abm.adjustmentAmount > 0 and ah.headType = 0 and ah.status = 1 group by abm.accountHeadID");
		while ($res = pro_db_fetch_array($queryHead)) {
			$headDebitAmount = 0.0;
			$headCreditAmount = 0.0;

			$arrDrSubAccounts = array();
			$arrCrSubAccounts = array();

			//Sub Account
			$arrSubAccounts = array();
			$queryAccount = pro_db_query("select distinct agm.accountGroupID, agm.groupName, ah.headSide, ah.accountHeadID
										from accountBalanceMaster abm
										join accountGroupMaster agm on agm.accountGroupID = abm.accountGroupID
                                        join accountHeadMaster ah on ah.accountHeadID = abm.accountHeadID
										where abm.societyID = " . $_SESSION['societyID'] . " and abm.financialYearID = " . $financialYearID . "
										and abm.accountHeadID = " . $res['accountHeadID'] . " and abm.adjustmentAmount > 0");
			while ($resAccount = pro_db_fetch_array($queryAccount)) {
				$accountGroupID = $resAccount['accountGroupID'];
				$debitAmount = isset($dictDebits[$accountGroupID]) ? $dictDebits[$accountGroupID] : '';
				$creditAmount = isset($dictCredits[$accountGroupID]) ? $dictCredits[$accountGroupID] : '';
				if ($resAccount['headSide'] == 0) {
					$objDrSubAccount =
						array(
							'title' => $resAccount['groupName'],
							'amtDebit' => $debitAmount,
							'amtCredit' => $creditAmount,
							'isHead' => false
						);
					$arrDrSubAccounts[] = $objDrSubAccount;
				} else {
					$objCrSubAccount =
						array(
							'title' => $resAccount['groupName'],
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
						'title' => $res['headName'],
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
						'title' => $res['headName'],
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
				<h4 style="font-size: 20px;" align="center">Balance Sheet</h4>
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
											<th style="font-size:medium; text-align:left; width:30%; text-decoration: underline;">Liabilities</th>
											<th style="font-size:medium; width:18%; text-align:right;">Amount</th>
											<th style="width:4%;"></th>
											<th style="font-size:medium; width:30%; text-decoration: underline;">Assets</th>
											<th style="font-size:medium; text-align:right; width:18%;">Amount</th>
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
														<?php
														if ($objDebitAccount['isHead']) {
														?>
															<?php echo $objDebitAccount['title']; ?>
														<?php
														} else {
														?>
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
														<?php
														if ($objCreditAccount['isHead']) {
														?>
															<?php echo $objCreditAccount['title']; ?>
														<?php
														} else {
														?>
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
										<tr style="background:antiquewhite;">
											<td style="font-weight: bold; font-size: medium;">TOTAL</td>
											<td align="right" style="font-weight: bold; font-size: medium; text-decoration: underline;">
												<?php echo number_format($totalDebitAmount, 2, '.', ''); ?>
											</td>
											<td></td>
											<td style="font-weight: bold; font-size: medium;">TOTAL</td>
											<td align="right" style="font-weight: bold; font-size: medium; text-decoration: underline;">
												<?php echo number_format($totalCreditAmount, 2, '.', ''); ?>
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