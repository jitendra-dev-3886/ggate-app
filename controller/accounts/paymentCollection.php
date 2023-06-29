<?php
class paymentCollection
{
	protected $redirectUrl;
	protected $controller;
	protected $action;
	protected $cloudStorage;
	protected $mediaType;
	protected $staffMediaType;

	public function __construct($controller = null, $action = null, $redirectUrl = null)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->redirectUrl = $redirectUrl;
		if (IS_PRODUCTION == 1) {
			$this->mediaType = "staff";
			$this->staffMediaType = "staffIdentity";
		} else {
			$this->mediaType = "staff-dev";
			$this->staffMediaType = "staffIdentity-dev";
		}
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";

		$invoiceDate = "";
		if(isset($_POST['paymentCollectionType']) && $_POST['paymentCollectionType'] >= 9){
			$paymentCollectionType = generateStaticOptions(array("9" => "Developement Fees", "10" => "Maintenance", "11" => "Amenities Booking", "12" => "Event Booking", "13" => "Electricity Bill", "14" => "Water Bill", "20" => "Other"),$_POST['paymentCollectionType']);
		}else{
			$paymentCollectionType = generateStaticOptions(array("9" => "Developement Fees", "10" => "Maintenance", "11" => "Amenities Booking", "12" => "Event Booking", "13" => "Electricity Bill", "14" => "Water Bill", "20" => "Other"));
		}
	?>
		  <div class="well quickForm" id="searchForm">
			<form role="form" id="listForm" action="<?php echo $this->redirectUrl . '&subaction=listData'; ?>" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="form-group col-sm-3">
						<label>Payment Collection Type</label>
						<select name="paymentCollectionType" id="paymentCollectionType" class="form-control custom-select mr-sm-2">
							<?php echo $paymentCollectionType; ?>
						</select>
					</div>
					<div class="form-group col-sm-2">
						<label>Date:</label>
						<input type="text" name="invoiceDate" class="form-control eventTodayDateTime" placeholder="Select Date" value="<?php if(isset($_POST['invoiceDate'])) { echo $_POST['invoiceDate']; }?>">
					</div>
					<div class="form-group col-sm-4">
						<label>&nbsp;</label><br>
						<input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
						<button type="submit" class="btn btn-success">Submit</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Reset" data-url="<?php echo $this->redirectUrl; ?>">Reset</button>
					</div>
				</div>
			</form>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="paymentCollectionList" width="100%">
								<thead>
									<tr>
										<th>Resident</th>
										<th>Residence</th>
										<th>Contact</th>
										<th>Invoice Date </th>
										<th>Invoice Amount&nbsp;(&#8377;)</th>
										<th>Paid Amount&nbsp;(&#8377;)</th>
										<th>Due Amount &nbsp;(&#8377;)</th>
										<th>Collection Type</th>
										<th>Payment Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Resident</th>
										<th>Residence</th>
										<th>Contact</th>
										<th>Invoice Date</th>
										<th>Invoice Amount &nbsp;(&#8377;)</th>
										<th>Paid Amount &nbsp;(&#8377;)</th>
										<th>Due Amount &nbsp;(&#8377;)</th>
										<th>Collection Type</th>
										<th>Payment Status</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			var searchVars = $('#listForm').serialize();
			console.log(searchVars);
			var listURL = "helperfunc/paymentCollectionList.php?" + searchVars;
			var table = $('#paymentCollectionList').dataTable({
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
			// For Datetime Calendar
			$('.eventTodayDateTime').flatpickr({
				enableTime: false,
				dateFormat: "Y-m-d"
			});
			$(document).ready( function () {
			    $('#paymentCollectionList').DataTable();
			} );

		</script>
	<?php
	}
}
	?>