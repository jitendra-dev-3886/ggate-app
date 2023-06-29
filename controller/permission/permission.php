<?php
class permission
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
	}

	/* Add Form */
	public function addForm()
	{
		echo $userID = (int)$_POST['userID'];
		$modules = getMasterArray("module_master", "module_file", "module_title", "module_complex=1");
		$selModules = getMasterList("permissionMaster", "moduleName", "moduleName", 'userID = ' . $userID);
		$userSql = pro_db_query("Select l.loginID, g.groupName from loginMaster l join groupMaster g using(groupID) where l.userID = " . $userID);
		$userrs = pro_db_fetch_array($userSql);
?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Permission</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" name="addForm" id="addForm" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="col-xs-6">Manage Permission for <u class="text-danger"><?php echo $userrs['loginID'] . " (" . $userrs['groupName'] . ")"; ?></u></label><input type="hidden" name="userID" value="<?php echo $userID; ?>">
									</div>
									<hr>
								</div>
								<div class="col-sm-6 offset-sm-1">

									<div class="custom-control custom-switch">
										<input class="custom-control-input" type="checkbox" id="selectall" value="false">
										<label class="custom-control-label" for="selectall">
											Select All
										</label>
									</div>
									<hr>
									<?php
									$i = 0;
									$moduleSql = pro_db_query("select * from module_master where status = 'E' and module_parent = 0 and module_complex = 1 order by sortorder");
									$pSelected = "";
									$cSelected = "";
									while ($mprs = pro_db_fetch_array($moduleSql)) {
										$i++;
										if (in_array($mprs['module_file'], $selModules)) {
											$pSelected = " checked";
										} else {
											$pSelected = "";
										}
										echo '
										<div class="custom-control custom-switch">
											<input class="custom-control-input case" type="checkbox" name="modules[]" value="' . $mprs['module_file'] . '" id="modules_' . $i . '" ' . $pSelected . '>
											<label class="custom-control-label" for="modules_' . $i . '"><strong>' . ucfirst($mprs['module_title']) . '</strong></label>
										</div>';
										$moduleCSql = pro_db_query("select * from module_master where status = 'E' and module_parent = " . $mprs['module_id'] . " and module_complex = 1 order by sortorder");
										if (pro_db_num_rows($moduleCSql) > 0) {
											while ($mcrs = pro_db_fetch_array($moduleCSql)) {
												$i++;
												if (in_array($mcrs['module_file'], $selModules)) {
													$cSelected = " checked";
												} else {
													$cSelected = "";
												}
												echo '
												<div class="custom-control custom-switch offset-sm-1">
													<input class="custom-control-input case" type="checkbox" name="modules[]" value="' . $mcrs['module_file'] . '"  id="modules_' . $i . '" ' . $cSelected . '>
													<label class="custom-control-label" for="modules_' . $i . '">' . ucfirst($mcrs['module_title']) . '</label>
												</div>';
											}
										}
									}
									if (in_array('CPC', $selModules)) {
										$pSelected = " checked";
									} else {
										$pSelected = "";
									}
									?>
								</div>
								<div class="col-sm-6">
									<label>&nbsp;</label>
								</div>
								<div class="col-sm-12 offset-sm-1">
									<hr>
									<div class="form-group">
										<label></label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
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
	}

	/* Add Function */
	public function add()
	{
		global $frmMsgDialog;
		$modules = $_POST['modules'];
		$formdata['username'] = $_SESSION['username'];
		$formdata['createdate'] = date('Y-m-d');
		$formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$error = 0;
		/* First Delete all Permissions */
		$delQry = pro_db_query("delete from permissionMaster where userID = " . (int)$_POST['userID']);
		foreach ($modules as $key => $value) {
			$formdata['moduleName'] = $value;
			$formdata['userID'] = (int)$_POST['userID'];
			if (pro_db_perform('permissionMaster', $formdata)) {
			} else {
				$error = 1;
			}
		}

		if ($error > 0) {
			$msg = '<p class="bg-danger">Permission is not saved!!!!!!</p>';
		} else {
			$msg = '<p class="bg-success">Permission is saved successfully...</p>';
		}
		$rUrl = $this->redirectUrl . "&subaction=listData";
		echo sprintf($frmMsgDialog, $rUrl, $msg);
	}

	/* List Data Function */
	public function listData()
	{
		$formaction = $this->redirectUrl . "&subaction=addForm";
		$groups = generateOptions(getMasterList("loginMaster", "userID", "concat(loginID, ' - ', userName)", "memberID > 0 and groupID > 4 and complexID =" . $_SESSION['complexID']));
	?>
		<div class="row">
			<div class="col-sm-12 py-3 mt-2">
				<h4>Manage Group Permissions</h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">
						<form role="form" id="listForm" action="<?php echo $formaction; ?>" method="post">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Select User:</label>
										<select name="userID" class="form-control custom-select mr-sm-2" required>
											<option value="">Select User</option>
											<?php echo $groups; ?>
										</select>
									</div>
								</div>
								<div class="col-sm-9">
									<div class="form-group">
										<label>&nbsp;</label>
										<input type="hidden" name="complexID" value="<?php echo $_SESSION['complexID']; ?>">
										<input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
										<br><button type="submit" class="btn btn-success">List Permissions</button>
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
}
?>
