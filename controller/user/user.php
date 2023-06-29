<?php
class user
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
		$this->resetformaction = $this->redirectUrl . "&subaction=resetPwd";
	}

	public function addForm()
	{
		$status = generateStaticOptions(array("E" => "Enable", "D" => "Disable"));
		$groups = generateOptions(getMasterArray('groupMaster', 'groupID', 'groupName'));
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Add Admin User</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="userform" id="userform" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Full Name:</label>
									<div>
										<input type="text" name="userName" class="form-control" required>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>User Email:</label>
									<div>
										<input type="email" name="userEmail" class="form-control" required>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>User Mobile:</label>
									<div>
										<input type="tel" name="userMobile" class="form-control" required>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Status:</label>
									<div>
										<select name="status" class="form-control custom-select mr-sm-2">
											<?php echo $status; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-3">
									<label>Login ID:</label>
									<div>
										<input type="text" name="loginID" id="loginID" class="form-control" required>
										<div class="help-block with-errors"></div>
									</div>
								</div>
								<div class="form-group col-sm-3">
									<label>Password:</label>
									<div>
										<input type="text" name="userPwd" id="userPwd" class="form-control">
									</div>
								</div>

							</div>
							<div class="form-group">
								<label class="col-xs-3"></label>
								<div class="col-xs-5">
									<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
									<input type="hidden" name="groupID" value="6">
									<button type="submit" class="btn btn-success" id="saveUser">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-default back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>&subaction=listData">Cancel</button>
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

	public function editForm()
	{
		$sql = "select * from loginMaster where userID = '" . $_REQUEST['userID'] . "'";
		$qry = pro_db_query($sql);

		if (pro_db_num_rows($qry) > 0) {
			$rs = pro_db_fetch_array($qry);
			$status = generateStaticOptions(array("E" => "Enable", "D" => "Disable"), $rs['status']);
			$groups = generateOptions(getMasterArray('groupMaster', 'groupID', 'groupName'), $rs['groupID']);
		?>
			<div class="row">
				<div class="col-sm-12 py-3 mt-2">
					<h4>Edit Users</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<form role="form" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="col-xs-3">Full Name:</label>
										<div class="col-xs-7">
											<input type="text" name="userName" class="form-control" value="<?php echo $rs['userName']; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-3">User Email:</label>
										<div class="col-xs-7">
											<input type="text" name="userEmail" class="form-control" value="<?php echo $rs['userEmail']; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-3">User Mobile:</label>
										<div class="col-xs-7">
											<input type="text" name="userMobile" class="form-control" value="<?php echo $rs['userMobile']; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-3">Login ID:</label>
										<div class="col-xs-7">
											<input type="text" name="loginID" id="loginID" class="form-control" value="<?php echo $rs['loginID']; ?>" readonly>
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-3">Status:</label>
										<div class="col-xs-7">
											<select name="status" class="form-control">
												<?php echo $status; ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-3"></label>
										<div class="col-xs-7">
											<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
											<input type="hidden" name="groupID" value="6">
											<input type="hidden" name="userID" value="<?php echo $rs['userID']; ?>">
											<button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-default back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>&subaction=listData">Cancel</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
		} else {
			echo "No User Found...";
		}
	}

	public function resetPwdForm()
	{
		$sql = pro_db_query("select * from loginMaster where userID = '" . $_REQUEST['userID'] . "'");

		if (pro_db_num_rows($sql) > 0) {
			$rs = pro_db_fetch_array($sql);
		?>
			<div class="row">
				<div class="col-sm-12 py-3 mt-2">
					<h4>Reset Password of user "<?php echo $rs['userName']; ?>"</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<form role="form" name="resetform" id="resetform" class="form-horizontal" action="<?php echo $this->resetformaction; ?>" method="post">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="col-xs-3">New Password:</label>
										<div class="col-xs-5">
											<input type="text" name="newPwd" id="newPwd" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label class="col-xs-3"></label>
										<div class="col-xs-5">
											<input type="hidden" name="userID" value="<?php echo $rs['userID']; ?>">
											<input type="hidden" name="loginID" value="<?php echo $rs['loginID']; ?>">
											<button type="submit" class="btn btn-success">Reset Password</button>&nbsp;&nbsp;<button type="reset" class="btn btn-default back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>&subaction=listData">Cancel</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
		} else {
			echo "No User Found...";
		}
	}

	public function add()
	{
		global $frmMsgDialog;
		$formdata = $_POST;
		$formdata['userPwd'] = hash('sha256', $formdata['loginID'] . $formdata['userPwd']);

		if (pro_db_perform('loginMaster', $formdata)) {
			$msg = '<p class="bg-success">User is created successfully...</p>';
		} else {
			$msg = '<p class="bg-danger">User is not created!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function edit()
	{
		global $frmMsgDialog;
		$whr = "";
		$whr = "userID=" . $_POST['userID'];
		$formdata = $_POST;

		if (pro_db_perform('loginMaster', $formdata, 'update', $whr)) {
			$msg = '<p class="bg-success">User information is updated successfully...</p>';
		} else {
			$msg = '<p class="bg-danger">User information is not updated!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function resetPwd()
	{
		global $frmMsgDialog;
		$whr = "userID=" . $_POST['userID'];
		$formdata = $_POST;
		$userPwd = hash('sha256', $formdata['loginID'] . $formdata['newPwd']);
		if (pro_db_query("update loginMaster set userPwd = '" . $userPwd . "' where userID = '" . $formdata['userID'] . "'")) {
			$msg = '<p class="bg-success">Password is changed successfully...</p>';
		} else {
			$msg = '<p class="bg-danger">Password is not changed successfully!!!</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function delete()
	{
		global $frmMsgDialog;
		pro_db_query("update memberMaster set adminType = 0 where memberID = " . (int)$_REQUEST['memberID']);
		$delsql = "Delete from loginMaster where userID = " . (int)$_REQUEST['userID'];
		if (pro_db_query($delsql)) {
			$msg = '<p class="bg-success p-3">Admin Detail deleted successfully...</p>';
		} else {
			$msg = '<p class="bg-danger p-3">Admin Detail Not deleted successfully</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";

		?><div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Admin Users</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Create User</a></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered dataTable" id="loginList">
							<thead>
								<tr>
									<th>User Group</th>
									<th>Login ID</th>
									<th>Full Name</th>
									<th>User Mobile</th>
									<th>User Email</th>
									<th>User Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
							<tfoot>
								<tr>
									<th>User Group</th>
									<th>Login ID</th>
									<th>Full Name</th>
									<th>User Mobile</th>
									<th>User Email</th>
									<th>User Status</th>
									<th>Action</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<script>
			var listURL = "helperfunc/loginList.php";
			var vtable = $('#loginList').dataTable({
				"processing": true,
				"serverSide": false,
				"ajax": {
					url: listURL, // json datasource
					type: "post", // type of method  , by default would be get
					error: function() { // error handling code
						$("#loginList_processing").css("display", "none");
					}
				},
				"order": [
					[0, "asc"]
				],
				"deferRender": true,
				"iDisplayLength": 25
			});

			$('.table').editable({
				selector: 'a.estatus',
				params: {
					"tblName": "loginMaster"
				},
				source: [{
					value: 'E',
					text: 'Active'
				}, {
					value: 'D',
					text: 'Inactive'
				}]
			});
		</script>
<?php
	}
}
?>