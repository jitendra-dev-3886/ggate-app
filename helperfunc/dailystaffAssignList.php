<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {

    $result = array('aaData' => array());
    $queryString = pro_db_query("SELECT mem.memberID, mem.memberName, mem.memberImage, mem.memberMobile, bm.blockName, bfm.officeNumber, om.officeName, bfm.officeID 
								from memberMaster mem
								join blockFloorOfficeMapping bfm on mem.memberID = bfm.memberID 
								and bfm.status = 1 and bfm.isPrimary = 1 and bfm.complexID = " . $_SESSION['complexID'] . "
                                join officeMaster om on om.officeID = bfm.officeID
								join blockMaster bm on bfm.blockID = bm.blockID 
								where mem.status = 1");
    while ($res = pro_db_fetch_array($queryString)) {
        $flatNumber = '<td>' . $res['blockName'] . ' - ' . $res['officeNumber'] . '</td>';

        if ($res['memberImage'] == null || empty($res['memberImage'])) {
            $res['memberImage'] = "https://cdn.ggate.app/icons/ico_visitor.png";
        }
        $officeID = $res['officeID'];
        $memberImage = '<td><img src="' . $res['memberImage'] . '"style="height : 50px ; width : 50px; border-radius:100%;" class="img-fluid"></td>';
        $memberName = '<td>' . ucfirst($res['memberName']) . '</td>';
        $memberMobile = '<td>' . $res['memberMobile'] . '</td>';

        $queryRelation = pro_db_query("SELECT * from dailyStaffRelation where status = 1 and officeID = " . $officeID . " 
                                        and staffID = " . $_REQUEST['dailyStaffID']);
        $isAlreadyAdded = false;
        while ($resRelation = pro_db_fetch_array($queryRelation)) {
            $isAlreadyAdded = $resRelation['status'] == 1;
        }

        $checkedValue = $officeID . "_1";
        $uncheckedValue = $officeID . "_0";

        if ($isAlreadyAdded) {
            $isChecked = '<td>
                        <input type="checkbox" name="officeselection[]" value=' . $checkedValue . ' checked />
                        
                        </td>';
        } else {
            $isChecked = '<td>
                        <input type="checkbox" name="officeselection[]" value=' . $checkedValue . ' />
                        
                        </td>';
        }
        $result['aaData'][] = array("$flatNumber", "$memberImage", "$memberName", "$memberMobile", "$isChecked");
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