<?php
function get_page_name($pageID)
{
	$pageqry = pro_db_query("select * from page_master where page_id = '" . $pageID . "'");
	$pageresult = pro_db_fetch_array($pageqry);
	return $pageresult['page_title'];
}
function get_latest_pages($limit = '5')
{
	$latestpages = pro_db_query("select * from page_master limit 0,$limit");
	$latestpages_string = "";
	while ($latestpagesrs = pro_db_fetch_array($latestpages)) {
		$pre_page = "index.php?pgid=" . $latestpagesrs['page_id'];
		$latestpages_string .= '
				
				<strong><a href=' . HTTP_SERVER . WS_ROOT . $pre_page . ' target="_blank" class="main">' . $latestpagesrs['page_title'] . '</a></strong><br />
				';
	}
	return $latestpages_string;
}
/*BOF SEO function for page*/
function get_pageseo_url($pageid)
{
	$seoqry = "Select page_slug from page_master where page_id = '" . $pageid . "'";
	$seoexecute = pro_db_query($seoqry);
	if (pro_db_num_rows($seoexecute) > 0) {
		$seorow = pro_db_fetch_array($seoexecute);
		//echo $seourl = $seorow['page_name'].".html";
		$seourl = 'pages/' . $seorow['page_slug'] . '/';
		return $seourl;
	} else {
		return false;
	}
}
/*EOF SEO function for page*/
function has_sub_pages($pageid)
{
	$qrypage = pro_db_query("select page_id from page_master where parent_id = '" . $pageid . "'");
	if (pro_db_num_rows($qrypage) > 0) {
		return 1;
	} else {
		return 0;
	}
}
function find_page_id($pagename)
{
	$pqry = pro_db_query("select * from page_master where page_title = '" . $pagename . "' ");
	if (pro_db_num_rows($pqry) > 0) {
		$pres = pro_db_fetch_array($pqry);
		return $pres['page_id'];
	} else {
		return 0;
	}
}

function fill_main_page()
{
	$toppages = pro_db_query("Select page_id,page_title,parent_id from page_master where parent_id=0");
	$pagedropdown = "";
	while ($toprs = pro_db_fetch_array($toppages)) {
		$pagechqry = pro_db_query("select parent_id from page_master where page_id='" . $_GET['pgID'] . "'");
		while ($pagech = pro_db_fetch_array($pagechqry)) {
			if ($pagech['parent_id'] == $toprs['page_id']) {
				$selected = "selected";
			} else {
				$selected = "";
			}
			if (has_sub_pages($toprs['page_id'])) {
				$pagedropdown .= '<option value="' . $toprs['page_id'] . '" ' . $selected . '>&nbsp;&nbsp;' . $toprs['page_title'] . '</option>';
				$secondlevel = pro_db_query("Select * from page_master where parent_id='" . $toprs['page_id'] . "'");
				while ($level2rs = pro_db_fetch_array($secondlevel)) {
					if ($pagech['parent_id'] == $level2rs['page_id']) {
						$selected = "selected";
					} else {
						$selected = "";
					}
					if (has_sub_pages($level2rs['page_id']) == 1) {
						$pagedropdown .= '<option value="' . $level2rs['page_id'] . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;|__' . $level2rs['page_title'] . '</option>';
						$thirdlevel = pro_db_query("Select * from page_master where parent_id='" . $level2rs['page_id'] . "'");
						while ($level3rs = pro_db_fetch_array($thirdlevel)) {
							if ($pagech['parent_id'] == $level3rs['page_id']) {
								$selected = "selected";
							} else {
								$selected = "";
							}
							if (has_sub_pages($level3rs['page_id']) == 1) {
								$pagedropdown .= '<option value="' . $level3rs['page_id'] . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__' . $level3rs['page_title'] . '</option>';
								$fourthlevel = pro_db_query("Select * from page_master where parent_id='" . $level3rs['page_id'] . "'");
								while ($level4rs = pro_db_fetch_array($fourthlevel)) {
									if ($pagech['parent_id'] == $level4rs['page_id']) {
										$selected = "selected";
									} else {
										$selected = "";
									}
									if (has_sub_pages($level4rs['page_id']) == 1) {
										$pagedropdown .= '<option value="' . $level4rs['page_id'] . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__' . $level4rs['page_title'] . '</option>';
									}
								} // 4th level loop over
							}
						} // 3rd level loop over
					}
				} // 2nd level loop over
			} else {
				$pagedropdown .= '<option value="' . $toprs['page_id'] . '" ' . $selected . '>&nbsp;&nbsp;' . $toprs['page_title'] . '</option>';
			}
		}
	} // Top Level Loop
	return $pagedropdown;
}

function display_menu_items($item_array, $selected_item = '', $parent_id = 0, $parent_depth = 0)
{
	$carry_rolegoal = null;
	$display = null;
	$rolegoal = null;
	foreach ($item_array as $item_id => $item_information) {
		if ($item_information['parent_id'] == $parent_id) {
			$link_text = "";
			$sep = "";
			for ($i = 0; ($i < $parent_depth); $i++) {
				$sep .= '-&nbsp;';
			}
			if ($item_information['page_title'] != '') {
				if (isset($selected_item) && $selected_item == $item_information['page_id']) {

					$link_text .= '<option selected = "selected" value="' . $item_information['page_id'] . '">' . $sep . $item_information['page_title'];
				} else {
					$link_text .= '<option value="' . $item_information['page_id'] . '">' . $sep . $item_information['page_title'];
				}
			}
			$link_text .= '</option>';
			$display .=   $link_text;
			$display .= display_menu_items($item_array, $selected_item, $item_id, ($parent_depth + 1));
			$carry_rolegoal = $rolegoal;
		}
		$rolegoal = $carry_rolegoal;
	}
	return $display;
}

function listSubPage($pageID, $sep)
{
	$subpage = "";
	$rows = 0;
	$ssqlsel = "select * from page_master where parent_id = '" . $pageID . "' order by sortorder";
	$sressql = pro_db_query($ssqlsel);
	while ($srow = pro_db_fetch_array($sressql)) {
		$rows++;
		if (($rows / 2) == floor($rows / 2)) {
			$cssclass =  'even';
		} else {
			$cssclass =  'odd';
		}
		if ($srow['status'] == 'D') {
			$pgstatus = 'Draft';
		} else {
			$pgstatus = 'Published';
		}
		$pk = "page_id:" . $srow['page_id'];
		$edlink = '<a href="index.php?page_id=' . $srow['page_id'] . '&controller=pages&action=pages&subaction=editForm" title="Editing Page ' . $srow['page_title'] . '" class="btn btn-success"><i class="fa fa-edit"></i></a>';
		$dlink = '<a href="javascript:void(0)" title="Delete" class="btn btn-danger" id="' . $srow['page_id'] . '" onClick="return checkchild(this.id);" ><i class="fa fa-times"></i></a>';
		$preview = '<a href="' . HTTP_SERVER . WS_ROOT . get_pageseo_url($srow['page_id']) . '" target=_blank title="Edit" class="btn btn-info"><i class="fa fa-eye"></i></a>';
		$sortorder = '<a href="#" class="esortorder" data-type="text" data-name="sortorder" data-pk="page_id:' . $srow['page_id'] . '" data-url="ajax/ajaxUpd.php" data-title="Change Sort Order">' . $srow['sortorder'] . '</a>';
		$subpage .= '
				<tr class="' . $cssclass . '">
					<td>' . $srow['page_id'] . '</td>
					<td><strong><i>' . get_page_name($srow['parent_id']) . '</i></strong></td>
					<td><i>' . $sep . $srow['page_title'] . '</i></td>
					<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $pgstatus . '</a></td>
					<td>' . $sortorder . '</td>
					<td>' . $edlink . '&nbsp;|&nbsp;' . $dlink . '</td>
					<td>' . $preview . '</td>
				</tr>';
		if (has_sub_pages($srow['page_id'])) {
			$subpage .= listSubPage($srow['page_id'], '--->');
		}
	}
	return $subpage;
}
?>