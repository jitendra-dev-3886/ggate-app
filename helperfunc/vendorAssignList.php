<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {

    $result = array('aaData' => array());
    $queryString = pro_db_query("select complexID, complexName, complexLogo, complexAddress, complexCity, status from complexMaster where status = 1");
    while ($res = pro_db_fetch_array($queryString)) {
        $complexID = $res['complexID'];

        if ($res['complexLogo'] == null || empty($res['complexLogo'])) {
            $res['complexLogo'] = "https://cdn.ggate.app/ggateweb/assets/images/logo-1.svg";
        }
        $complexLogo = '<td><img src="' . $res['complexLogo'] . '"style="height : 80px ; width : 200px;" class="img-fluid"></td>';
        $complexName = '<td>' . ucfirst($res['complexName']) . '</td>';
        $complexAddress = '<td>' . $res['complexAddress'] . '</td>';
        $complexCity = '<td>' . $res['complexCity'] . '</td>';

        $queryRelation = pro_db_query("select * from vendorComplexMapping where status = 1 and complexID = " . $complexID . " 
                                        and vendorID = " . $_REQUEST['vendorID']);
        $isAlreadyAdded = false;
        while ($resRelation = pro_db_fetch_array($queryRelation)) {
            $isAlreadyAdded = $resRelation['status'] == 1;
        }
        $checkedValue = $complexID . "_1";
        $uncheckedValue = $complexID . "_0";

        if ($isAlreadyAdded) {
            $isChecked = '<td><input type="checkbox" name="complexselection[]" value=' . $checkedValue . ' checked /></td>';
        } else {
            $isChecked = '<td><input type="checkbox" name="complexselection[]" value=' . $checkedValue . ' /></td>';
        }
        $result['aaData'][] = array("$complexLogo", "$complexName", "$complexAddress", "$complexCity", "$isChecked");
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