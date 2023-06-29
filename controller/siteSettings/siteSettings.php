<?php
class siteSettings
{
	protected $optionsData = array();
	protected $redirectUrl;
	protected $controller;
	protected $optsction;
	protected $optsddformaction;
	protected $editformaction;

	public function __construct($controller = null, $optsction = null, $redirectUrl = null)
	{
		$data = array();
		$options[] = array("Fld_Name" => "SITENAME", "optTitle" => "Site Name", "type" => "textbox", "group" => "basic", "optValue" => "");
		$options[] = array("Fld_Name" => "SITEMETATITLE", "optTitle" => "Site Meta Title", "type" => "textbox", "group" => "basic", "optValue" => "");
		$options[] = array("Fld_Name" => "SITEMETADESC", "optTitle" => "Site Description", "type" => "textbox", "group" => "basic", "optValue" => "");
		$options[] = array("Fld_Name" => "SITEMETAKEYWORDS", "optTitle" => "Site Keywords", "type" => "textbox", "group" => "basic", "optValue" => "");
		$options[] = array("Fld_Name" => "SITECOPYRIGHT", "optTitle" => "Site Copyright", "type" => "textbox", "group" => "basic", "optValue" => "");
		$options[] = array("Fld_Name" => "G_SITE_KEY", "optTitle" => "reCaptcha Site Key", "type" => "textbox", "group" => "basic", "optValue" => "");
		$options[] = array("Fld_Name" => "G_SEC_KEY", "optTitle" => "reCaptcha Security Key", "type" => "textbox", "group" => "basic", "optValue" => "");

		$options[] = array("Fld_Name" => "TOTAL_BUSINESS", "optTitle" => "Total Business", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "ASONDATE", "optTitle" => "As on Date", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "SHARE_CAPITAL", "optTitle" => "Share Capital", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "SHARE_DEPOSIT", "optTitle" => "Share Deposits", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "WORKING_CAPITAL", "optTitle" => "Working Capital", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "REVENUE_FUNDS", "optTitle" => "Reserve Funds", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "ADVANCES", "optTitle" => "Advances", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "NET_PROFIT", "optTitle" => "Net Profit", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "WHATS_TITLE", "optTitle" => "Whats New Title", "type" => "textbox", "group" => "homepage", "optValue" => "");
		$options[] = array("Fld_Name" => "WHATS_DESCR", "optTitle" => "Whats New Description", "type" => "textbox", "group" => "homepage", "optValue" => "");

		$options[] = array("Fld_Name" => "COMPANYPHONE1", "optTitle" => "Company Phone 1", "type" => "textbox", "group" => "contact", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYPHONE2", "optTitle" => "Company Phone 2", "type" => "textbox", "group" => "contact", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYFAX", "optTitle" => "Company Fax", "type" => "textbox", "group" => "contact", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYEMAIL1", "optTitle" => "Company eMail", "type" => "textbox", "group" => "contact", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYSUPPORTEMAIL", "optTitle" => "Support e Mail", "type" => "textbox", "group" => "contact", "optValue" => "");

		$options[] = array("Fld_Name" => "COMPANYANALYTICSID", "optTitle" => "Google Analytics Id", "type" => "textbox", "group" => "analytics", "optValue" => "");

		$options[] = array("Fld_Name" => "COMPANYFACEBOOK", "optTitle" => "Facebook Link", "type" => "textbox", "group" => "social", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYINSTAGRAM", "optTitle" => "Instagram Link", "type" => "textbox", "group" => "social", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYYOUTUBE", "optTitle" => "Youtube Link", "type" => "textbox", "group" => "social", "optValue" => "");
		$options[] = array("Fld_Name" => "COMPANYTWITTER", "optTitle" => "Twitter Link", "type" => "textbox", "group" => "social", "optValue" => "");

		/* Check options is already added into Db or not */
		foreach ($options as $opt) {
			$chkSql = pro_db_query("SELECT * FROM siteOptions where optTitle = '" . $opt['Fld_Name'] . "'");
			if (pro_db_num_rows($chkSql) > 0) {
				$res = pro_db_fetch_array($chkSql);
				$data[$res['optTitle']] = $res['optValue'];
			} else {
				$insOpt = pro_db_query("INSERT INTO siteOptions (optTitle,optValue) VALUES ('" . $opt['Fld_Name'] . "', '" . $opt['optValue'] . "')");
			}
		}

		$this->controller = $controller;
		$this->action = $optsction;
		$this->redirectUrl = $redirectUrl;
		$this->addformaction = $this->redirectUrl . "&subaction=add";
		$this->editformaction = $this->redirectUrl . "&subaction=edit";
		$this->optionsData = $options;
	}

	function listData()
	{
		$siteOptions = array();
		$listOptionsSql = pro_db_query("SELECT * FROM siteOptions");
		while ($result = pro_db_fetch_array($listOptionsSql)) {
			$siteOptions[$result['optTitle']] = $result['optValue'];
		}
?>
		<div class="box-header with-border">
			<h3 class="box-title">Seeting</h3>
			<div class="box-tools pull-right">
				<a class="btn btn-warning" id="edit">Edit</a>&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" id="save">Save</a>
			</div>
		</div>
		<br>
		<div>
			<!-- Nav tabs -->
			<div class="col-sm-3">
				<div class="ada-tab-left well">
					<ul class="nav nav-tabs bold" role="tablist">
						<li role="presentation" class="active">
							<a href="#basic" aria-controls="basic" role="tab" data-toggle="tab">Basic Settings</a>
						</li>
						<li role="presentation">
							<a href="#homepage" aria-controls="homepage" role="tab" data-toggle="tab">Home Page Settings</a>
						</li>
						<li role="presentation">
							<a href="#contact" aria-controls="profile" role="tab" data-toggle="tab">Other Settings</a>
						</li>
						<li role="presentation">
							<a href="#social" aria-controls="social" role="tab" data-toggle="tab">Social Settings</a>
						</li>
						<li role="presentation">
							<a href="#analytics" aria-controls="analytics" role="tab" data-toggle="tab">Analaytics</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-sm-9">
				<form class="ada form form-horizontal well" id="Settings">
					<!-- Tab panes -->
					<div class="tab-content">
						<!-- Basic Settings -->
						<div role="tabpanel" class="tab-pane fade in active" id="basic">
							<h4>Basic Settings</h4>
							<?php

							foreach ($this->optionsData as $opts) {
								if (isset($opts['group']) && $opts['group'] == 'basic') {
							?>
									<div class="form-group">
										<label class="control-label col-xs-4"><?php echo $opts['optTitle'] ?>:</label>
										<div class="col-xs-8">
											<p class="help control-label" style="text-align: left !important;">
												<?php echo (($siteOptions[$opts['Fld_Name']] == '') ? $opts['optTitle'] : $siteOptions[$opts['Fld_Name']]); ?>
											</p>
											<?php
											switch ($opts['type']) {
												case 'textbox':
													echo '<input type="text" class="input form-control" value="' . $siteOptions[$opts['Fld_Name']] . '" name="' . $opts['Fld_Name'] . '">';
													break;
												case 'textarea':
													echo '<textarea name="' . $opts['Fld_Name'] . '" class="input form-control">' . $siteOptions[$opts['Fld_Name']] . '</textarea>';
													break;
											}
											?>
										</div>
									</div>
							<?php
								}
							}
							?>
							<hr>
						</div>

						<!-- Home Page Settings -->
						<div role="tabpanel" class="tab-pane fade in active" id="homepage">
							<h4>Home Page Settings</h4>
							<?php

							foreach ($this->optionsData as $opts) {
								if (isset($opts['group']) && $opts['group'] == 'homepage') {
							?>
									<div class="form-group">
										<label class="control-label col-xs-4"><?php echo $opts['optTitle'] ?>:</label>
										<div class="col-xs-8">
											<p class="help control-label" style="text-align: left !important;">
												<?php echo (($siteOptions[$opts['Fld_Name']] == '') ? $opts['optTitle'] : $siteOptions[$opts['Fld_Name']]); ?>
											</p>
											<?php
											switch ($opts['type']) {
												case 'textbox':
													echo '<input type="text" class="input form-control" value="' . $siteOptions[$opts['Fld_Name']] . '" name="' . $opts['Fld_Name'] . '">';
													break;
												case 'textarea':
													echo '<textarea name="' . $opts['Fld_Name'] . '" class="input form-control">' . $siteOptions[$opts['Fld_Name']] . '</textarea>';
													break;
											}
											?>
										</div>
									</div>
							<?php
								}
							}
							?>
							<hr>
						</div>

						<!-- Other Settings Tab -->
						<div role="tabpanel" class="tab-pane fade in active" id="contact">
							<h4>Other Settings</h4>
							<?php
							foreach ($this->optionsData as $opts) {
								if (isset($opts['group']) && $opts['group'] == 'contact' && $opts['Fld_Name'] != 'open' && $opts['Fld_Name'] != 'close') {
							?>
									<div class="form-group">
										<label class="control-label col-xs-4"><?php echo $opts['optTitle'] ?>:</label>
										<div class="col-xs-8">
											<p class="help control-label" style="text-align: left !important;">
												<?php echo (($siteOptions[$opts['Fld_Name']] == '') ? $opts['optTitle'] : $siteOptions[$opts['Fld_Name']]); ?>
											</p>
											<?php
											switch ($opts['type']) {
												case 'textbox':
													echo '<input type="text" class="input form-control" value="' . $siteOptions[$opts['Fld_Name']] . '" name="' . $opts['Fld_Name'] . '">';
													break;
												case 'textarea':
													echo '<textarea name="' . $opts['Fld_Name'] . '" class="input form-control">' . $siteOptions[$opts['Fld_Name']] . '</textarea>';
													break;
											}
											?>
										</div>
									</div>
							<?php
								}
							}
							?>
							<hr>
						</div>
						<!-- Social Settings Tab -->
						<div role="tabpanel" class="tab-pane fade in active" id="social">
							<h4>Social Settings</h4>
							<?php
							foreach ($this->optionsData as $opts) {
								if (isset($opts['group']) && $opts['group'] == 'social' && $opts['Fld_Name'] != 'open' && $opts['Fld_Name'] != 'close') {
							?>
									<div class="form-group">
										<label class="control-label col-xs-4"><?php echo $opts['optTitle'] ?>:</label>
										<div class="col-xs-8">
											<p class="help control-label" style="text-align: left !important;">
												<?php echo (($siteOptions[$opts['Fld_Name']] == '') ? $opts['optTitle'] : $siteOptions[$opts['Fld_Name']]); ?>
											</p>
											<?php
											switch ($opts['type']) {
												case 'textbox':
													echo '<input type="text" class="input form-control" value="' . $siteOptions[$opts['Fld_Name']] . '" name="' . $opts['Fld_Name'] . '">';
													break;
												case 'textarea':
													echo '<textarea name="' . $opts['Fld_Name'] . '" class="input form-control">' . $siteOptions[$opts['Fld_Name']] . '</textarea>';
													break;
											}
											?>
										</div>
									</div>
							<?php
								}
							}
							?>
							<hr>
						</div>
						<!-- Analytics Settings Tab -->
						<div role="tabpanel" class="tab-pane fade in active" id="analytics">
							<h4>Analytics Settings</h4>
							<?php
							foreach ($this->optionsData as $opts) {
								if (isset($opts['group']) && $opts['group'] == 'analytics' && $opts['Fld_Name'] != 'open' && $opts['Fld_Name'] != 'close') {
							?>
									<div class="form-group">
										<label class="control-label col-xs-4"><?php echo $opts['optTitle'] ?>:</label>
										<div class="col-xs-8">
											<p class="help control-label" style="text-align: left !important;">
												<?php echo (($siteOptions[$opts['Fld_Name']] == '') ? $opts['optTitle'] : $siteOptions[$opts['Fld_Name']]); ?>
											</p>
											<?php
											switch ($opts['type']) {
												case 'textbox':
													echo '<input type="text" class="input form-control" value="' . $siteOptions[$opts['Fld_Name']] . '" name="' . $opts['Fld_Name'] . '">';
													break;
												case 'textarea':
													echo '<textarea name="' . $opts['Fld_Name'] . '" class="input form-control">' . $siteOptions[$opts['Fld_Name']] . '</textarea>';
													break;
											}
											?>
										</div>
									</div>
							<?php
								}
							}
							?>
						</div>
					</div>
				</form>
			</div>
		</div>

		<script>
			$('.input').hide();
			var editing = false;
			$('#edit').click(function() {
				if (editing) {
					location.reload();
					editing = false;
				} else {
					$(this).removeClass('btn-warning');
					$(this).addClass('btn-danger');
					$(this).html('Cancel');
					$('.input').show();
					$('.help').hide();
					editing = true;
				}
			});

			$('#save').click(function() {
				var form = $('#Settings');
				var data = form.serialize();
				$.ajax({
					url: 'ajax/saveSettings.php',
					type: 'post',
					data: data,
					success: function(data) {
						$('#edit').trigger('click');
					}
				});
			});
		</script>
<?php
	}
}
?>