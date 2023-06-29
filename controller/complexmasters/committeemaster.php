<?php
class committeemaster
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
	}

	public function listData()
	{
		if ($_SESSION['memberID'] == 0) {
			$qry = ' mem.memberMobile ';
		} else {
			$qry = "concat('******', RIGHT(mem.memberMobile, 4)) as memberMobile";
		}
		$formaction = $this->redirectUrl . "&subaction=addForm";
?>
		<div class="row">
			<div class="col-sm-9 py-3 mt-2">
				<h4>Committee List</h4>
			</div>
			<div class="col-sm-3 py-3 mt-2"></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-body">

						<div class="accordion" id="accordionExample">
							<?php
							$query = pro_db_query("
							select 0 as committeeID, 0 as complexID, 0 as memberID, 'General' as committeeName from committeeMaster union 
							select committeeID, complexID, memberID, committeeName from committeeMaster where complexID = '" . (int)$_SESSION['complexID'] . "'");
							while ($res = pro_db_fetch_array($query)) {
								$i = strval($res['committeeID']);
								$j = "collapse" . $i;
							?>

								<div id="<?php echo $i; ?>">
									<div class="mb-0">
										<button class="blockquote btn-fw btn-block text-left accordion-button" type="button" data-toggle="collapse" data-target="#<?php echo $j; ?>" aria-expanded="true" aria-controls="<?php echo $j; ?>">
											<?php echo $res['committeeName']; ?>
											<i class="fas fa-arrow-down" style="float:right;"></i>
										</button>
									</div>
									<div id="<?php echo $j; ?>" class="collapse blockquote" aria-labelledby="<?php echo $i; ?>" data-parent="#accordionExample">
										<div class="table-responsive table-bordered">
											<table cellpadding="1" cellspacing="2" class="table table-striped table-bordered dataTable" id="table_feature" width="100%">
												<thead>
													<tr>
														<th width="60">Ofice</th>
														<th width="80">Image</th>
														<th>Committee Member</th>
														<th>Mobile No.</th>
														<th width="20%">Committee Role</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$sqlQuery = "select mem.memberID, desg.designationID, desg.designationTitle, mem.memberName, " . $qry . " ,
														mem.memberEmail, mem.memberImage, concat(bm.blockName, '-', bfm.officeNumber) as flat
                                    					from designationMemberMapping desgMap
                                    					left join designationMaster desg on desgMap.designationID = desg.designationID
                                    					join memberMaster mem on desgMap.memberID = mem.memberID and desgMap.complexID = mem.complexID
                                                        join officeMemberMapping ofm on ofm.employeeID = mem.memberID
														left join blockFloorOfficeMapping bfm on (bfm.memberID = ofm.employeeID or bfm.memberID = ofm.parentID) 
														left join blockMaster bm on bfm.blockID = bm.blockID 
                                    					where desgMap.complexID = " . $_SESSION['complexID'] . " 
														and desgMap.committeeID = " . $res['committeeID'] . "
                                    					and desgMap.status = 1 group by mem.memberID order by desgMap.designationID, mem.memberName";
                                    				
													$querydetails = pro_db_query($sqlQuery);
													while ($resdetails = pro_db_fetch_array($querydetails)) {
														if ($resdetails['memberImage'] == null || empty($resdetails['memberImage'])) {
															$resdetails['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
														}
														$memberImage = $resdetails['memberImage'];
													?>
														<tr>
															<td><?php echo $resdetails["flat"] ?></td>
															<td><img src="<?php echo $memberImage ?>" style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>
															<td><?php echo $resdetails["memberName"] ?></td>
															<td><?php echo $resdetails["memberMobile"] ?></td>
															<td><?php echo $resdetails["designationTitle"] ?></td>
														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}
?>
