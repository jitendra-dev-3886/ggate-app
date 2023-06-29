<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
    $result = array('aaData' => array());
    if ($_SESSION['memberID'] == 0) {
        $whr = 'mem.memberMobile';
    } else {
        $whr = "concat('******', RIGHT(mem.memberMobile, 4)) as memberMobile";
    }
    $statusArray = array("0" => "Pending", "1" => "Accept", "2" => "Reject");
    $adminTypeArray = array("0" => "-", "1" => "Society Admin", "2" => "Building Admin", "3" => "Adhoc Admin");
    if (($_SESSION['memberID'] != 0) && ($_SESSION['groupID'] > 5)) {
        $queryString = pro_db_query("SELECT mem.memberName, " . $whr . ", bfom.status, bfom.officeMappingID, bfom.officeNumber, om.officeName from memberMaster mem
									join blockFloorOfficeMapping bfom on mem.memberID = bfom.memberID
									join blockMaster blk on bfom.blockID = blk.blockID
                                    join officeMaster om on om.officeID = bfom.officeID
									where bfom.status = 0 and bfom.complexID =" . $_SESSION['complexID'] . " and bfom.blockID = " . $_SESSION['blockID'] . "
									order by ISNULL(blk.blockName), blk.blockName, bfom.floorNo, cast(bfom.officeNumber as unsigned), mem.memberName");
    } else {
        $queryString = pro_db_query("SELECT mem.memberName, " . $whr . ",bfom.status, bfom.officeMappingID, bfom.officeNumber, om.officeName from memberMaster mem
									join blockFloorOfficeMapping bfom on mem.memberID = bfom.memberID
									join blockMaster blk on bfom.blockID = blk.blockID
                                    join officeMaster om on om.officeID = bfom.officeID
									where bfom.status = 0 and bfom.complexID =" . $_SESSION['complexID'] . "
									order by ISNULL(blk.blockName), blk.blockName, bfom.floorNo, cast(bfom.officeNumber as unsigned), mem.memberName");
    }

    while ($res = pro_db_fetch_array($queryString)) {
        $pk = "officeMappingID:" . $res['officeMappingID'];
        $memberName = '<td>' . ucfirst($res['memberName']) . '</td>';
        $officeName = '<td>' . ucfirst($res['officeName']) . '</td>';
        $memberMobile = '<td>' . $res['memberMobile'] . '</td>';
        $flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['flatNumber'] . '</td>';

        $Action = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpdNotification.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
        $result['aaData'][] = array("$officeName", "$memberName", "$memberMobile", "$Action");
    }

    // End While Loop

    echo json_encode($result);
} else {
?>
    <script>
        location.href = "login.php";
    </script>
<?php
}
?>