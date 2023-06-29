<?php
class complexAccountSettings
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->makeAdminformaction = $this->redirectUrl . "&subaction=makeAdmin";
	}

	public function editForm()
	{
		$qry = pro_db_query("select sas.*,sm.complexName, sm.complexAddress from complexAccountSettings sas
							join complexMaster sm on sas.complexID = sm.complexID
							where sas.complexID = " . (int)$_SESSION['complexID']);
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$isGSTApplicable = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['isGSTApplicable']);
		$isManually = generateStaticOptions(array("0" => "Automatic", "1" => "Manually"), $rs['isManually']);
		$invoiceType = generateStaticOptions(array("0" => "Monthly", "1" => "Quaterly", "2" => "Half yearly", "3" => "Yearly"), $rs['invoiceType']);
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4><?php echo $rs['complexName']; ?> - Account Settings</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label>Complex Address:</label>
										<input type="text" name="complexaddress" class="form-control" value="<?php echo $rs['complexAddress']; ?>" placeholder="" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Bank Name:</label>
										<input type="text" name="bankName" class="form-control" value="<?php echo $rs['bankName']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-5">
									<div class="form-group">
										<label>Bank Address:</label>
										<input type="text" name="bankAddress" class="form-control" value="<?php echo $rs['bankAddress']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Bank IFSC Code:</label>
										<input type="text" name="bankIFSC" class="form-control" value="<?php echo $rs['bankIFSC']; ?>" placeholder="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Account Name:</label>
										<input type="text" name="accountName" class="form-control" value="<?php echo $rs['accountName']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Account Number</label>
										<input type="number" name="accountNumber" class="form-control" value="<?php echo $rs['accountNumber']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Account Type:</label>
										<input type="text" name="accountType" class="form-control" value="<?php echo $rs['accountType']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>PAN Number:</label>
										<input type="text" maxlength=10 name="panNumber" class="pan form-control" value="<?php echo $rs['panNumber']; ?>" placeholder="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Is GST Applicable?</label>
									<select name="isGSTApplicable" id="isGSTApplicable" class="form-control custom-select mr-sm-2" required>
										<?php echo $isGSTApplicable; ?>
									</select>
								</div>
								<div class="col-sm-2" id="gstNumber" <?php if ($rs['isGSTApplicable'] == 1) { ?> style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>GST Number:</label>
										<input type="text" maxlength="15" name="gstNumber" class="form-control gst" value="<?php echo $rs['gstNumber']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2" id="hsnCode" <?php if ($rs['isGSTApplicable'] == 1) { ?> style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>HSN Code:</label>
										<input type="number" maxlength="5" name="hsnCode" class="form-control" value="<?php echo $rs['hsnCode']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2" id="cgstRate" <?php if ($rs['isGSTApplicable'] == 1) { ?> style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>CGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="cgstRate" id="cgstRate1" onchange="iGSTRate()" class="form-control" value="<?php echo $rs['cgstRate']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2" id="sgstRate" <?php if ($rs['isGSTApplicable'] == 1) { ?> style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>SGST Rate:</label>
										<input type="number" min=0 step="0.1" max=14 inputmode="decimal" name="sgstRate" id="sgstRate1" onchange="iGSTRate()" class="form-control" value="<?php echo $rs['sgstRate']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2" id="igstRate" <?php if ($rs['isGSTApplicable'] == 1) { ?> style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>IGST Rate:</label>
										<input type="number" min=0 step="0.1" max=28 inputmode="decimal" name="igstRate" id="igstRate1" class="form-control" value="<?php echo $rs['cgstRate'] + $rs['sgstRate']; ?>" placeholder="" readonly>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-2">
									<label>Invoice Type:</label>
									<select name="isManually" id="isManually" class="form-control custom-select mr-sm-2" required>
										<?php echo $isManually; ?>
									</select>
								</div>
								<div class="form-group col-sm-2" id="invoiceGeneration" <?php if ($rs['isManually'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<label>Mode:</label>
									<select name="invoiceType" id="invoiceType" class="form-control custom-select mr-sm-2" required>
										<?php echo $invoiceType; ?>
									</select>
								</div>
								<div class="col-sm-2" id="invoiceDay" <?php if ($rs['isManually'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Day:</label>
										<input type="number" min=1 max=31 name="invoiceDay" class="form-control" value="<?php echo $rs['invoiceDay']; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2" id="invoiceMonth" <?php if ($rs['isManually'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Month:</label>
										<input type="number" mix=1 max=12 id="txtInvoiceMonth" name="invoiceMonth" class="form-control" value="<?php echo $rs['invoiceMonth']; ?>" placeholder="" <?php if ($rs['invoiceType'] == 0) { ?> readonly <?php } ?>>
									</div>
								</div>
								<div class="col-sm-2" id="invoiceDueDays" <?php if ($rs['isManually'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Due Days:</label>
										<input type="number" min=0 name="invoiceDueDays" class="form-control" value="<?php echo $rs['invoiceDueDays']; ?>" placeholder="" required>
									</div>
								</div>
								<div class="col-sm-2" id="waiverDays" <?php if ($rs['isManually'] == 0) { ?>style="display:show" <?php } else { ?> style="display:none" <?php } ?>>
									<div class="form-group">
										<label>Waiver Days:</label>
										<input type="number" min=0 name="waiverDays" class="form-control" value="<?php echo $rs['waiverDays']; ?>" placeholder="" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2">
									<div class="form-group">
										<label>1st Notice Period:</label>
										<input type="number" min=0 name="noticePeriod1" class="form-control" value="<?php echo $rs['noticePeriod1'] ?? 0; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>2nd Notice Period:</label>
										<input type="number" min=0 name="noticePeriod2" class="form-control" value="<?php echo $rs['noticePeriod2'] ?? 0; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>3rd Notice Period:</label>
										<input type="number" min=0 name="noticePeriod3" class="form-control" value="<?php echo $rs['noticePeriod3'] ?? 0; ?>" placeholder="">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo WS_ADMIN_ROOT; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(".pan").change(function() {
				var inputvalues = $(this).val();
				var regex = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
				if (!regex.test(inputvalues)) {
					$(".pan").val("");
					alert("Kindly enter valid PAN Number");
					return regex.test(inputvalues);
				}
			});

			$(".gst").change(function() {
				var inputvalues = $(this).val();
				var regex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
				if (!regex.test(inputvalues)) {
					$(".gst").val("");
					alert("Kindly enter valid GST Number");
					return regex.test(inputvalues);
				}
			});

			function iGSTRate() {
				$cgstrate = document.getElementById('cgstRate1').value;
				$sgstrate = document.getElementById('sgstRate1').value;
				$total = parseInt($cgstrate) + parseInt($sgstrate);
				document.getElementById('igstRate1').value = $total;
			}

			$('#isManually').on('change', function() {
				if (this.value == '1') {
					$("#invoiceGeneration").hide();
					$("#invoiceDay").hide();
					$("#invoiceMonth").hide();
					$("#invoiceDueDays").hide();
					$("#waiverDays").hide();
					$("#invoiceDay").prop('required', false);
					$("#invoiceMonth").prop('required', false);
					$("#invoiceGeneration").prop('required', false);
					$("#invoiceDueDays").prop('required', false);
					$("#waiverDays").prop('required', false);

				} else {
					$("#invoiceGeneration").show();
					$("#invoiceDay").show();
					$("#invoiceMonth").show();
					$("#invoiceDueDays").show();
					$("#waiverDays").show();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', false);
					$("#invoiceGeneration").prop('required', true);
					$("#invoiceDueDays").prop('required', true);
					$("#waiverDays").prop('required', true);
				}
			});

			$('#isGSTApplicable').on('change', function() {
				if (this.value == '1') {
					$("#gstNumber").show();
					$("#hsnCode").show();
					$("#cgstRate").show();
					$("#sgstRate").show();
					$("#igstRate").show();
					$("#gstNumber").prop('required', true);
					$("#hsnCode").prop('required', true);
					$("#sgstRate").prop('required', true);
					$("#cgstRate").prop('required', true);

				} else {
					$("#gstNumber").hide();
					$("#hsnCode").hide();
					$("#cgstRate").hide();
					$("#sgstRate").hide();
					$("#igstRate").hide();
					$("#gstNumber").prop('required', false);
					$("#hsnCode").prop('required', false);
					$("#sgstRate").prop('required', false);
					$("#cgstRate").prop('required', false);
				}
			});

			$('#invoiceType').on('change', function() {
				if (this.value == '0') {
					$("#invoiceDay").show();
					$("#invoiceMonth").hide();
					$("#invoiceDay").prop('required', true);
					$("#txtInvoiceMonth").attr('readonly', true);
					$("#invoiceMonth").prop('required', false);
				} else {
					$("#invoiceDay").show();
					$("#invoiceMonth").show();
					$("#invoiceDay").prop('required', true);
					$("#txtInvoiceMonth").attr('readonly', false);
					$("#invoiceMonth").prop('required', true);
				}
			});
		</script>
<?php
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "complexID=" . $_SESSION['complexID'];
		$formdata = $_POST;
		unset($formdata['complexaddress']);

		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		if ($_POST['isGSTApplicable'] == 0) {
			$formdata['gstNumber'] = null;
			$formdata['hsnCode'] = null;
			$formdata['cgstRate'] = 0;
			$formdata['sgstRate'] = 0;
			$formdata['igstRate'] = 0;
		}

		if (isset($_POST['invoiceMonth'])) {
			if ($_POST['invoiceType'] == 0) {
				$formdata['invoiceMonth'] = 0;
			} else {
				$formdata['invoiceMonth'] = $_POST['invoiceMonth'];
			}
		} else {
			$formdata['invoiceMonth'] = 0;
		}

		if (pro_db_perform('complexAccountSettings', $formdata, 'update', $whr)) {

			//dashboard log for society account settings
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexSettings";
			$dashboardlogdata['action'] = "complexAccountSettings";
			$dashboardlogdata['subAction'] = "updatecomplexAccountSettings";
			$dashboardlogdata['referenceID'] = $_SESSION['complexID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$qry = pro_db_query("select isManually from complexAccountSettings where complexID = " . (int)$_SESSION['complexID']);
			$rs = pro_db_fetch_array($qry);

			if ($rs['isManually'] != $_POST['isManually']) {
				// if($_POST['isManually'] == 0){
				// 	$qry = pro_db_query("delete from flatInvoiceMapping where societyID = ".(int)$_SESSION['societyID']);
				// }

				//-------------- No need to change anything here --------------//
				//Header
				$notificationPayload = (int) round(microtime(true) * 1000);
				$notificationHashKey = "2X9xHfKfOYCBZ6FnvoePwsWpty0" . "com.ripl.ggate";
				$notificationHashValue = hash('sha256', ($notificationPayload . $notificationHashKey));
				$headers = [
					'Content-Type: application/json', 'AUTHORIZATION: ' . $notificationHashValue,
					'PAYLOAD: ' . $notificationPayload
				];

				//Notification Params
				$requestParams = [
					'complexID' => $_SESSION['complexID'],
					'isManually' => $_POST['isManually']
				];

				$CURL_REQUEST_URL = GGATE_APP_DASHBORD_COMMUNITY_URL . "cancelScheduledInvoiceEvent";

				$ch = curl_init();
				curl_setopt(
					$ch,
					CURLOPT_URL,
					// GGATE_SOCIETY_INVOICE_CANCEL_URL
					$CURL_REQUEST_URL
				);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt(
					$ch,
					CURLOPT_POSTFIELDS,
					json_encode($requestParams)
				);

				$result = curl_exec($ch);

				if ($result === FALSE) {
					die('Problem occurred: ' . curl_error($ch));
				}
				curl_close($ch);
			}
			if ($_POST['isManually'] == 0) {
				//-------------- No need to change anything here --------------//
				//Header
				$notificationPayload = (int) round(microtime(true) * 1000);
				$notificationHashKey = "2X9xHfKfOYCBZ6FnvoePwsWpty0" . "com.ripl.ggate";
				$notificationHashValue = hash('sha256', ($notificationPayload . $notificationHashKey));
				$headers = [
					'Content-Type: application/json', 'AUTHORIZATION: ' . $notificationHashValue,
					'PAYLOAD: ' . $notificationPayload
				];

				//Notification Params
				$requestParams = [
					'complexID' => $_SESSION['complexID']
				];

				$CURL_REQUEST_URL = GGATE_APP_DASHBORD_COMMUNITY_URL . "generateSocietyInvoices";

				$ch = curl_init();
				curl_setopt(
					$ch,
					CURLOPT_URL,
					// GGATE_SOCIETY_INVOICE_GENERATE_URL
					$CURL_REQUEST_URL
				);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt(
					$ch,
					CURLOPT_POSTFIELDS,
					json_encode($requestParams)
				);

				$result = curl_exec($ch);

				if ($result === FALSE) {
					die('Problem occurred: ' . curl_error($ch));
				}
				curl_close($ch);
			}
			$msg = '<p class="bg-success p-3">Account Settings is saved...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Account Setting is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=editForm";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}
}
?>
