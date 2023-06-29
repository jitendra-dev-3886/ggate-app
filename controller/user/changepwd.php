<?php
include WS_PFBC_ROOT . "Form.php";
$form = new Form("chpwd");
$form->configure(array(
	"prevent" => array("bootstrap", "jQuery"),
	"view" => new View_SideBySide
));
$form->addElement(new Element_HTML("<legend></legend>"));
$form->addElement(new Element_Hidden("controller", "user"));
$form->addElement(new Element_Hidden("action", "changepwd"));
$form->addElement(new Element_Hidden("subaction", "changepwd"));
/* Actual Form Fields Started */
$form->addElement(new Element_Textbox("Current Password:", "currentpwd", array(
	"required" => 1,
	"placeholder" => "Current Password"
)));
$form->addElement(new Element_Password("New Password:", "newpwd", array(
	"required" => 1,
	"placeholder" => "New Password"
)));
$form->addElement(new Element_Password("Confirm New Password:", "cpwd", array(
	"required" => 1,
	"placeholder" => "Confirm New Password:"
)));
$form->addElement(new Element_HTML('<hr>'));
$form->addElement(new Element_Button);
$form->addElement(new Element_Button("Cancel", "button", array(
	"onclick" => "location.href='index.php?controller=user&action=user&subaction=listData';"
)));
$form->addElement(new Element_HTML('<hr>'));

if (isset($_REQUEST['subaction']) && $_REQUEST['subaction'] == 'changepwd') {

	$cpass = pro_db_real_escape_string($_POST['currentpwd']);
	$login = $_SESSION['loginID'];
	$chash = hash('sha256', $login . $cpass);
	$sql = "select password from user_master where loginID = '" . $_SESSION['loginID'] . "'";
	$cupwd_sql = pro_db_query($sql);

	$rs = pro_db_fetch_array($cupwd_sql);

	$current_pwd = $rs['password'];
	if ($chash != $current_pwd) {
		echo '<div class="alert alert-error"><strong>Error!</strong> Your Current Password is Wrong.</div>';
		$form->render();
	} else {
		$npass = pro_db_real_escape_string($_POST['newpwd']);
		$nhash = hash('sha256', $login . $npass);
		$pwdqry1 = "Update user_master set password = '" . $nhash . "' where loginID = '" . $_SESSION['loginID'] . "'";
		$pwdsql1 = pro_db_query($pwdqry1);
?>
		<script>
			$('#myModal').modal('show');
			$('#myModal').on('hidden', function() {
				location.href = 'logoff.php';
			});
		</script>
<?php
	}
} else {
	$form->render();
}
?>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Change Password</h3>
	</div>
	<div class="modal-body">
		<p>
		<div class="alert alert-success"><strong>Congratulation!</strong> Your password is sucessfully changed.</div>
		</p>
	</div>
</div>