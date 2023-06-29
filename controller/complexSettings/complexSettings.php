<?php

class complexSettings
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
		$qry = pro_db_query("select cm.* from complexMaster cm 
		where cm.complexID =" . (int)$_SESSION['complexID'] . "");
		$rs = pro_db_fetch_array($qry);

		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$mobilePreferences = generateStaticOptions(array("1" => "Visible", "0" => "Hidden"), $rs['mobilePreferences']);
		$complexOffice = generateStaticOptions(array("0" => "No", "1" => "Yes"), $rs['complexOffice']);
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Complex Settings - <?php echo $rs['complexName']; ?></h4>
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
										<input type="text" name="complexAddress" class="form-control" value="<?php echo $rs['complexAddress']; ?>" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Age to be considered for a child:</label>
										<input type="text" name="childAge" class="form-control" value="<?php echo $rs['childAge']; ?>" placeholder="" required>
									</div>
								</div>
								<?php if ($rs['allowChangePreferences'] == 1) { ?>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Primary Member's Number Preference:</label>
											<select name="mobilePreferences" class="form-control custom-select mr-sm-2">
												<?php echo $mobilePreferences; ?>
											</select>
										</div>
									</div>
								<?php } ?>
								<div class="form-group col-sm-3">
									<label>Does Complex have Office?:</label>
									<select name="complexOffice" id="complexOffice" class="form-control custom-select mr-sm-2" required>
										<?php echo $complexOffice; ?>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Maximum Properties:</label>
										<input type="text" class="form-control" value="<?php echo $rs['maxProperties']; ?>" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Maximum Blocks:</label>
										<input type="text" class="form-control" value="<?php echo $rs['maxBlocks']; ?>" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enrolled Date:</label>
										<input type="text" class="form-control" value="<?php echo $rs['enrolledDate']; ?>" placeholder="" readonly>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Valid Up To Date:</label>
										<input type="text" class="form-control" value="<?php echo $rs['validUptoDate']; ?>" placeholder="" readonly>
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
			$('#isManually').on('change', function() {
				if (this.value == '1') {
					$("#invoiceGeneration").hide();
					$("#invoiceDay").hide();
					$("#invoiceMonth").hide();
					$("#invoiceDueDays").hide();
					$("#noticeIntervalOne").hide();
					$("#noticeIntervalTwo").hide();
					$("#noticeIntervalThree").hide();
					$("#intervalOnePenalty").hide();
					$("#intervalTwoPenalty").hide();
					$("#intervalThreePenalty").hide();
					$("#invoiceDay").prop('required', false);
					$("#invoiceMonth").prop('required', false);
					$("#invoiceGeneration").prop('required', false);
					$("#invoiceDueDays").prop('required', false);
					$("#noticeIntervalOne").prop('required', false);
					$("#noticeIntervalTwo").prop('required', false);
					$("#noticeIntervalThree").prop('required', false);
					$("#intervalOnePenalty").prop('required', false);
					$("#intervalTwoPenalty").prop('required', false);
					$("#intervalThreePenalty").prop('required', false);
				} else {
					$("#invoiceGeneration").show();
					$("#invoiceDay").show();
					$("#invoiceMonth").show();
					$("#invoiceDueDays").show();
					$("#noticeIntervalOne").show();
					$("#noticeIntervalTwo").show();
					$("#noticeIntervalThree").show();
					$("#intervalOnePenalty").show();
					$("#intervalTwoPenalty").show();
					$("#intervalThreePenalty").show();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', false);
					$("#invoiceGeneration").prop('required', true);
					$("#invoiceDueDays").prop('required', true);
					$("#noticeIntervalOne").prop('required', true);
					$("#noticeIntervalTwo").prop('required', false);
					$("#noticeIntervalThree").prop('required', false);
					$("#intervalOnePenalty").prop('required', true);
					$("#intervalTwoPenalty").prop('required', false);
					$("#intervalThreePenalty").prop('required', false);
				}
			});

			$('#invoiceType').on('change', function() {
				if (this.value == '0') {
					$("#invoiceDay").show();
					$("#invoiceMonth").hide();
					$("#invoiceDay").prop('required', true);
					$("#invoiceMonth").prop('required', false);
				} else {
					$("#invoiceDay").show();
					$("#invoiceMonth").show();
					$("#invoiceDay").prop('required', true);
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
		$formdata['modifieddate'] = date('Y-m-d H:i:s');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

		$qry = pro_db_query("select cm.complexOffice, cm.mobilePreferences from complexMaster cm 
							where cm.complexID = " . (int)$_SESSION['complexID']);
		$rs = pro_db_fetch_array($qry);

		if (isset($_POST['mobilePreferences'])) {
			if ($rs['mobilePreferences'] != $_POST['mobilePreferences']) {
				$data['mobilePreferences'] = $_POST['mobilePreferences'];
				$whr = "";
				$complexID = $_POST['complexID'];
				$whr = "complexID=" . $complexID;
				if ($_POST['mobilePreferences'] == 0) {
					pro_db_perform('blockFloorOfficeMapping', $data, 'update', $whr);
				} else {
					pro_db_perform('blockFloorOfficeMapping', $data, 'update', $whr);
				}
			}
		}



		if ($rs['complexOffice'] != $_POST['complexOffice']) {
			unset($data['mobilePreferences']);
			if ($_POST['complexOffice'] == 1) {
				unset($data['mobilePreferences']);
				$data['complexID'] = $_SESSION['complexID'];
				$data['blockname'] = "Office";
				$data['noOfFloors'] = 1;
				$data['officePerFloor'] = 1;
				$data['createdate'] = date('Y-m-d H:i:s');
				$data['modifieddate'] = date('Y-m-d H:i:s');
				$data['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				$data['status'] = 1;

				pro_db_perform('blockMaster', $data);
			} else {
				pro_db_query("Delete from blockMaster where blockName = 'Office' and complexID = " . $_SESSION['complexID']);
			}
		}

		if (pro_db_perform('complexMaster', $formdata, 'update', $whr)) {

			//dashboard log for society settings
			$dashboardlogdata = array();
			$dashboardlogdata['complexID'] = $_SESSION['complexID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexSettings";
			$dashboardlogdata['action'] = "complexSettings";
			$dashboardlogdata['subAction'] = "updatecomplexSettings";
			$dashboardlogdata['referenceID'] = $_SESSION['complexID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Complex Settings is saved...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Complex Setting is not saved!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=editForm";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}
}
?>
