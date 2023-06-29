<?php
class clubactivityreports
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
	}

	public function listData()
	{
		$clubID = "";
		if (isset($_POST['clubID'])) {
			$clubID = (int)$_POST['clubID'];
		}
		$clubID = generateOptions(getMasterList('clubMaster', 'clubID', 'clubTitle', "complexID=" . $_SESSION['complexID']), $clubID);
?>
		<div class="well quickForm" id="searchForm">
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=listData'; ?>" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label>Select Item:</label>
							<select name="clubID" class="form-control custom-select mr-sm-2" required>
								<?php echo $clubID; ?>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>Date:</label>
							<input type="text" name="inDateTime" class="form-control eventTodayDateTime" placeholder="" value="<?php if (isset($_POST['inDateTime'])) {
																																	$inDateTime = $_POST['inDateTime'];
																																	echo $inDateTime;
																																} else {
																																	echo date('Y-m-d');
																																} ?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>&nbsp;</label><br>
							<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
							<input type="hidden" name="memberID" value="<?php echo (int)$_SESSION['memberID']; ?>">
							<button type="submit" class="btn btn-success">Submit</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Reset" data-url="<?php echo $this->redirectUrl; ?>">Reset</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table table-striped table-bordered dataTable" id="clubactivityReportsList">
								<thead>
									<tr>
										<th width="8%">Image</th>
										<th>Resident</th>
										<th width="10%">Residence</th>
										<th>Club </th>
										<th width="10%">In Time</th>
										<th width="10%">Out Time</th>
										<th width="5%">Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th width="8%">Image</th>
										<th>Resident</th>
										<th width="10%">Residence</th>
										<th>Club </th>
										<th width="10%">In Time</th>
										<th width="10%">Out Time</th>
										<th width="5%">Status</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			// For Datetime Calendar
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d"
			});

			var searchVars = $('#listForm').serialize();
			var listURL = "helperfunc/clubactivityReportsList.php?" + searchVars;
			var table = $('#clubactivityReportsList').dataTable({
				dom: 'Bfrtip',
				"ajax": {
					url: listURL, // json datasource
					type: "post", // type of method  , by default would be get
					error: function() { // error handling code
						$("#clubactivityReportsList_processing").css("display", "none");
					}
				},
				"stateSave": true,
				"order": [],
				"deferRender": true,
				"iDisplayLength": 50
			});
		</script>
<?php
	}
}
?>
