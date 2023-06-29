<?php
class complaints
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
		$complaintDate = "";
?>
		<div class="well quickForm" id="searchForm">
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=listData'; ?>" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label>Date:</label>
							<input type="text" name="complaintDate" class="form-control eventTodayDateTime" placeholder="" value="
							<?php if (isset($_POST['complaintDate'])) {
								$complaintDate = $_POST['complaintDate'];
								echo $complaintDate;
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
							<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table table-striped table-bordered dataTable" id="complaintReportsList">
								<thead>
									<tr>
										<!-- <th>Residence</th> -->
										<th>Office</th>
										<th width="10%">Complainant</th>
										<th width="15%">Type</th>
										<th width="25%">Complaint Remarks</th>
										<th width="15%">Complaint Date</th>
										<th width="15%">Approx. Resolve Date</th>
										<th width="15%">Resolved Date</th>
										<th width="10%">Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<!-- <th>Residence</th> -->
										<th>Office</th>
										<th width="10%">Complainant</th>
										<th width="15%">Type</th>
										<th width="25%">Complaint Remarks</th>
										<th width="15%">Complaint Date</th>
										<th width="15%">Approx. Resolve Date</th>
										<th width="15%">Resolved Date</th>
										<th width="10%">Status</th>
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
			var listURL = "helperfunc/complaintReportsList.php?" + searchVars;
			var table = $('#complaintReportsList').dataTable({
				dom: 'Bfrtip',
				"ajax": {
					url: listURL, // json datasource
					type: "post", // type of method  , by default would be get
					error: function() { // error handling code
						$("#complaintReportsList_processing").css("display", "none");
					}
				},
				"stateSave": true,
				"order": [],
				"deferRender": true,
				"iDisplayLength": 25
			});
		</script>
<?php
	}
}
?>
