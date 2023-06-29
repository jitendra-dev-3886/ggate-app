<?php
function pro_SeoSlug($strToSlug)
{
	$seoSlug = strtolower($strToSlug);
	$seoSlug = trim($seoSlug);
	$seoSlug = preg_replace("`[.*]`U", "", $seoSlug);
	$seoSlug = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $seoSlug);
	$seoSlug = htmlentities($seoSlug, ENT_COMPAT, 'utf-8');
	$seoSlug = preg_replace("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\1", $seoSlug);
	$seoSlug = preg_replace(array("`[^a-z0-9]`i", "`[-]+`"), "-", $seoSlug);
	return $seoSlug;
}
function insSeoLnk($eleID, $module, $seoSlug, $parentID = 0)
{
	$seourl = $module . "/" . $seoSlug . "/";
	$seo_link_sql = pro_db_query(
		" Insert into seoLinks set 
		module_name = '" . $module . "',
		seo_slug = '" . $seoSlug . "',
		seo_url = '" . $seourl . "',
		module_id = '" . $eleID . "',
		parent_id = '" . $parentID . "',
		user_id = '" . $_SESSION['userID'] . "',
		createdate = now(),
		modifieddate = now(),
		remote_ip = '" . $_SERVER['REMOTE_ADDR'] . "'"
	);
	return true;
}
function updSeoLnk($eleID, $module, $seoSlug, $parentID = 0)
{
	$chkseo = pro_db_query("Select * from seoLinks where module_id = '" . $eleID . "' and module_name = '" . $module . "' ");
	$seourl = $module . "/" . $seoSlug . "/";
	if (pro_db_num_rows($chkseo) > 0) {
		$seo_link_sql = pro_db_query("update seoLinks set 
			seo_slug = '" . $seoSlug . "',
			seo_url = '" . $seourl . "',
			parent_id = '" . $parentID . "',
			user_id='" . $_SESSION['userID'] . "',
			modifieddate = now(),
			remote_ip='" . $_SERVER['REMOTE_ADDR'] . "' 
			where module_id = '" . $eleID . "' and module_name = '" . $module . "' ");
	} else {
		$seo_link_sql = pro_db_query(" Insert into seoLinks set 
			module_name = '" . $module . "',
			seo_slug = '" . $seoSlug . "',
			seo_url = '" . $seourl . "',
			module_id = '" . $eleID . "',
			parent_id = '" . $parentID . "',
			user_id = '" . $_SESSION['userID'] . "',
			createdate = now(),
			modifieddate = now(),
			remote_ip = '" . $_SERVER['REMOTE_ADDR'] . "'");
	}
	return true;
}
function delSeoLnk($eleID, $module)
{
	$seo_link_sql = pro_db_query(" Delete from seoLinks where module_id = '" . $eleID . "' and module_name = '" . $module . "' ");
	return true;
}
?>