<?php
class assetmembermappingmaster
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $addformaction;
	protected $editformaction;
	protected $makeAdminformaction;
	protected $addfeesformaction;
	protected $cloudStorage;
	protected $updatefeesformaction;
	protected $mediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->addfeesformaction = $this->redirectUrl . "&subaction=addFees";
		$this->updatefeesformaction = $this->redirectUrl . "&subaction=updateFees";
	}

	public function addForm()
	{
		$allowMembership = generateStaticOptions(array("1" => "Yes", "0" => "No"));
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
		$assetID = generateOptions(getMasterList('amenityMaster', 'assetID', 'assetTitle', "assetType = 0 and status = 1 and societyID=" . $_SESSION['societyID']));
		$flatID = generateOptions(getMasterList('blockFloorFlatMapping bfm, blockMaster bm', 'flatID', 'concat(blockName," - ",flatNumber)', "bfm.status = 1 and bfm.blockID = bm.blockID and bfm.societyID=" . $_SESSION['societyID']));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Restrict Members to access Amenity</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form name="frmAddTeam" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data" class="needs-validation">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Amenity:</label>
									<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" data-target-list="bookingTimeSlotID" data-target-url="ajax/timeslot.php" required>
										<option value="">Select Amenity</option>
										<?php echo $assetID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3">
									<label>Residence Number:</label>
									<select name="flatID[]" id="flatID" class="custom-select mr-sm-2 form-control" multiple data-live-search="true" required>
										<?php echo $flatID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Enter Amount:</label>
										<input type="number" name="amount" class="form-control" placeholder="0" value="0">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
										<input type="submit" class="btn btn-success" value="Save">&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			// Material Select Initialization
			$('.mdb-select').materialSelect();
			$('select').selectpicker();
		</script>
	<?php
	}

	public function editForm()
	{
		$qry = pro_db_query("select * from assetMemberMapping where assetMappingID = " . (int)$_REQUEST['assetMappingID']);
		$rs = pro_db_fetch_array($qry);

		$allowMembership = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['allowMembership']);
		$status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
		$assetID = generateOptions(getMasterList('amenityMaster', 'assetID', 'assetTitle', "societyID=" . $_SESSION['societyID']), $rs['assetID']);
		$flatID = generateOptions(getMasterList('blockFloorFlatMapping bfm, blockMaster bm', 'flatID', 'concat(blockName," - ",flatNumber)', "bfm.blockID = bm.blockID and bfm.societyID=" . $_SESSION['societyID']), $rs['flatID']);
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Amenity - Access Fees</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="frmedit" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3 ">
									<label>Amenity:</label>
									<select name="assetID" id="assetID" class="form-control custom-select mr-sm-2 bindbox" data-live-search="true" data-target-list="bookingTimeSlotID" data-target-url="ajax/timeslot.php" required>
										<option value="">Select Amenity</option>
										<?php echo $assetID; ?>
									</select>
								</div>
								<div class="form-group col-sm-3 ">
									<label>Residence Number:</label>
									<select name="flatID" id="flatID" class="form-control custom-select mr-sm-2" data-live-search="true" required>
										<option value="">Select Residence</option>
										<?php echo $flatID; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Amenity Access Fees:</label>
										<input type="number" name="amount" class="form-control" placeholder="" value="<?php echo $rs['amount']; ?>">
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label></label>
										<input type="hidden" name="assetMappingID" value="<?php echo $rs['assetMappingID']; ?>">
										<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
										<button type="submit" class="btn btn-success">Apply Fees</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function add()
	{
		global $frmMsgDialog;
		//$sql = pro_db_query("select assetMappingID from assetMemberMapping where assetID = " . $_POST['assetID'] . " and flatID = " . $_POST['flatID'] . "");
		$sql = pro_db_query("select flatID from blockFloorFlatMapping where societyID = " . $_SESSION['societyID'] . " and status = 1");

		while ($rowMember = $sql->fetch_assoc()) {
			$flatIDS[] = $rowMember["flatID"];
		}

		for ($i = 0; $i < count($flatIDS); $i++) {
			$formdata = array(
				"assetID" => $_POST['assetID'],
				"societyID" => $_SESSION['societyID'],
				"flatID" => $flatIDS[$i],
				"allowMembership" => 1,
				"amount" => $_POST['amount'],
				"createdate" => date('Y-m-d H:i:s'),
				"modifieddate" => date('Y-m-d H:i:s'),
				"remote_ip" => $_SERVER['REMOTE_ADDR'],
				"status" => 1
			);
			pro_db_perform('assetMemberMapping', $formdata);
			$assetMappingID = pro_db_insert_id();

			//dashboard log for assetmapping
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "assetmembermappingmaster";
			$dashboardlogdata['subAction'] = "addassetmembermapping";
			$dashboardlogdata['referenceID'] = $assetMappingID;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);
		}

		foreach ($_POST['flatID'] as $key => $value) {
			$updsql = pro_db_query("
					update assetMemberMapping set
					allowMembership = 0
					where flatID = '" . $value . "'
				");
		}
		if ($assetMappingID > 0) {
			$msg = '<p class="bg-success p-3">Assets To Member Mapping Detail is saved successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Assets To Member Mapping Detail is not saved!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "assetMappingID=" . $_POST['assetMappingID'];
		$formdata = $_POST;

		if (pro_db_perform('assetMemberMapping', $formdata, 'update', $whr)) {

			//dashboard log for assetmapping
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "assetmembermappingmaster";
			$dashboardlogdata['subAction'] = "editassetmembermapping";
			$dashboardlogdata['referenceID'] = $whr;
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Access fees is applied to Member successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Issue while applying access fees to Member...</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		$delsql = "Delete from assetMemberMapping where assetMappingID = " . (int)$_GET['assetMappingID'];
		if (pro_db_query($delsql)) {

			//dashboard log for assetmapping
			$dashboardlogdata = array();
			$dashboardlogdata['societyID'] = $_SESSION['societyID'];
			$dashboardlogdata['memberID'] = $_SESSION['memberID'];
			$dashboardlogdata['contorller'] = "complexmasters";
			$dashboardlogdata['action'] = "assetmembermappingmaster";
			$dashboardlogdata['subAction'] = "deleteassetmembermapping";
			$dashboardlogdata['referenceID'] = $_GET['assetMappingID'];
			$dashboardlogdata['status'] = 1;
			$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
			$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
			pro_db_perform('dashboardLogMaster', $dashboardlogdata);

			$msg = '<p class="bg-success p-3">Assets To Member Mapping Detail is Deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Assets To Member Mapping Detail is not Deleted!!!!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function applyFees()
	{
		global $frmMsgDialog;
		$assetMappingID = (int)$_GET['assetMappingID'];
		$assetID = (int)$_GET['assetID'];
		$flatID = (int)$_GET['flatID'];
		$amount = (int)$_GET['amount'];

		//Mapping
		$queryMapping = pro_db_query("select assetMappingID from assetMemberMapping where status = 1 and assetMappingID= " . $assetMappingID);
		$totalMapping = pro_db_num_rows($queryMapping);
		if ($totalMapping > 0) {
			$updateSql = "update assetMemberMapping set amount = " . $amount . " where assetMappingID = " . $assetMappingID;

			if (pro_db_query($updateSql)) {
				//dashboard log for assetmapping
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "assetmembermappingmaster";
				$dashboardlogdata['subAction'] = "updatefees";
				$dashboardlogdata['referenceID'] = $assetMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$msg = '<p class="bg-success p-3">Member is restricted for this amenity successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Issue while restricting this member for this amenity...</p>';
			}
		} else {
			//Insert new entry
			$formdata = array(
				"assetID" => $assetID,
				"societyID" => $_SESSION['societyID'],
				"flatID" => $flatID,
				"allowMembership" => 1,
				"amount" => $amount,
				"createdate" => date('Y-m-d H:i:s'),
				"modifieddate" => date('Y-m-d H:i:s'),
				"remote_ip" => $_SERVER['REMOTE_ADDR'],
				"status" => 1
			);

			if (pro_db_perform('assetMemberMapping', $formdata)) {

				$assetMappingID = pro_db_insert_id();
				//dashboard log for assetmapping
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "assetmembermappingmaster";
				$dashboardlogdata['subAction'] = "addfees";
				$dashboardlogdata['referenceID'] = $assetMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$msg = '<p class="bg-success p-3">Member is restricted for this amenity successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Issue while restricting this member for this amenity...</p>';
			}
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		// echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function restrictAccess()
	{
		global $frmMsgDialog;
		$assetMappingID = (int)$_GET['assetMappingID'];
		$assetID = (int)$_GET['assetID'];
		$flatID = (int)$_GET['flatID'];

		//Mapping
		$queryMapping = pro_db_query("select assetMappingID from assetMemberMapping where status = 1 and assetMappingID= " . $assetMappingID);
		$totalMapping = pro_db_num_rows($queryMapping);
		if ($totalMapping > 0) {
			$updateSql = "update assetMemberMapping set allowMembership = 0 where assetMappingID = " . $assetMappingID;

			if (pro_db_query($updateSql)) {
				//dashboard log for assetmapping
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "assetmembermappingmaster";
				$dashboardlogdata['subAction'] = "updaterestrictAccess";
				$dashboardlogdata['referenceID'] = $assetMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$msg = '<p class="bg-success p-3">Member is restricted for this amenity successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Issue while restricting this member for this amenity...</p>';
			}
		} else {
			//Insert new entry
			$formdata = array(
				"assetID" => $assetID,
				"societyID" => $_SESSION['societyID'],
				"flatID" => $flatID,
				"allowMembership" => 0,
				"amount" => 0,
				"createdate" => date('Y-m-d H:i:s'),
				"modifieddate" => date('Y-m-d H:i:s'),
				"remote_ip" => $_SERVER['REMOTE_ADDR'],
				"status" => 1
			);

			if (pro_db_perform('assetMemberMapping', $formdata)) {

				$assetMappingID = pro_db_insert_id();
				//dashboard log for assetmapping
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "assetmembermappingmaster";
				$dashboardlogdata['subAction'] = "addrestrictAccess";
				$dashboardlogdata['referenceID'] = $assetMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$msg = '<p class="bg-success p-3">Member is restricted for this amenity successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Issue while restricting this member for this amenity...</p>';
			}
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function allowAccess()
	{
		global $frmMsgDialog;
		$assetMappingID = (int)$_GET['assetMappingID'];
		$assetID = (int)$_GET['assetID'];
		$flatID = (int)$_GET['flatID'];

		//Mapping
		$queryMapping = pro_db_query("select assetMappingID from assetMemberMapping where status = 1 and assetMappingID= " . $assetMappingID);
		$totalMapping = pro_db_num_rows($queryMapping);
		if ($totalMapping > 0) {
			$updateSql = "update assetMemberMapping set allowMembership = 1 where assetMappingID = " . $assetMappingID;

			if (pro_db_query($updateSql)) {
				//dashboard log for assetmapping
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "assetmembermappingmaster";
				$dashboardlogdata['subAction'] = "updateallowaccess";
				$dashboardlogdata['referenceID'] = $assetMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$msg = '<p class="bg-success p-3">Member is allowed for this amenity successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Issue while allowing this member for this amenity...</p>';
			}
		} else {
			//Insert new entry
			$formdata = array(
				"assetID" => $assetID,
				"societyID" => $_SESSION['societyID'],
				"flatID" => $flatID,
				"allowMembership" => 1,
				"amount" => 0,
				"createdate" => date('Y-m-d H:i:s'),
				"modifieddate" => date('Y-m-d H:i:s'),
				"remote_ip" => $_SERVER['REMOTE_ADDR'],
				"status" => 1
			);

			if (pro_db_perform('assetMemberMapping', $formdata)) {

				$assetMappingID = pro_db_insert_id();
				//dashboard log for assetmapping
				$dashboardlogdata = array();
				$dashboardlogdata['societyID'] = $_SESSION['societyID'];
				$dashboardlogdata['memberID'] = $_SESSION['memberID'];
				$dashboardlogdata['contorller'] = "complexmasters";
				$dashboardlogdata['action'] = "assetmembermappingmaster";
				$dashboardlogdata['subAction'] = "addallowaccess";
				$dashboardlogdata['referenceID'] = $assetMappingID;
				$dashboardlogdata['status'] = 1;
				$dashboardlogdata['createdate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['modifieddate'] = date('Y-m-d H:i:s');
				$dashboardlogdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
				pro_db_perform('dashboardLogMaster', $dashboardlogdata);

				$msg = '<p class="bg-success p-3">Member is allowed for this amenity successfully...</p>';
			} else {
				$msg = '<p class="bg-danger p-3">Issue while allowing this member for this amenity...</p>';
			}
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";

		$arrAmenitiesID = array();
		$arrAmenitiesTitle = array();
		$queryAmenities = pro_db_query("SELECT assetID, assetTitle from amenityMaster where status = 1 and societyID = " . $_SESSION['societyID'] . " order by assetTitle");
		while ($resAmenities = pro_db_fetch_array($queryAmenities)) {
			$arrAmenitiesID[] = $resAmenities["assetID"];
			$arrAmenitiesTitle[] = $resAmenities["assetTitle"];
		}
		$selectedAmenityID = $arrAmenitiesID[0];
		$selectedAmenityTitle = $arrAmenitiesTitle[0];
	?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Members - Amenities Management</h4>
			</div>
			<!-- <div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-secondary float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Restrict Members</a></div> -->
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="card-body">
							<div class="dropdown">
								<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<?php echo $selectedAmenityTitle ?>
								</button>
								<div class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenuButton">
									<?php
									for ($i = 0; $i < count($arrAmenitiesID); $i++) {
										echo "<button type='button' class='submenu amenityType dropdown-item' 
										data-amenitytitle='" . $arrAmenitiesTitle[$i] . "'
										data-amenityid='" . $arrAmenitiesID[$i] . "'> " . $arrAmenitiesTitle[$i] . " </button>";
									}
									?>
								</div>
							</div>

							<hr>
							<div class="table-responsive">
								<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="assetmembermappingmasterList" width="100%">
									<thead>
										<tr>
											<th width="10%">Residence</th>
											<th width="10%">Image</th>
											<th width="30%">Resident</th>
											<th width="20%">Amenity</th>
											<th width="10%">Amount</th>
											<th width="10%">Membership</th>
											<th width="10%">Action</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr>
											<th width="10%">Residence</th>
											<th width="10%">Image</th>
											<th width="30%">Resident</th>
											<th width="20%">Amenity</th>
											<th width="10%">Amount</th>
											<th width="10%">Membership</th>
											<th width="10%">Action</th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<script>
				var amenityID = "<?php echo $selectedAmenityID; ?>";
				var amenityTitle = "<?php echo $selectedAmenityTitle; ?>";
				var listURL = 'helperfunc/assetmembermappingmasterList.php?amenityID=' + amenityID;
				if ($("#assetmembermappingmasterList").length > 0) {
					var table = $('#assetmembermappingmasterList').dataTable({
						"ajax": listURL,
						"deferRender": true,
						"iDisplayLength": 50,
						"stateSave": true,
						"order": []
					});
				}
				$(document).on('click', 'button.amenityType', function(e) {
					amenityID = $(this).data('amenityid');
					amenityTitle = $(this).data('amenitytitle');
					document.getElementById('dropdownMenuButton').innerHTML = amenityTitle;
					var listURL = "helperfunc/assetmembermappingmasterList.php?amenityID=" + amenityID;
					table.api().ajax.url(listURL).load();
					table.fnDraw();
				});
				$('.table').editable({
					selector: 'a.eallowMembership',
					params: {
						"tblName": "assetMemberMapping"
					},
					source: [{
						value: '1',
						text: 'Allow'
					}, {
						value: '0',
						text: 'Restrict'
					}]
				});
			</script>
	<?php
	}
}
	?>