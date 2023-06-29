<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
    $result = array('aaData' => array());
    $statusArray = array("0" => "Pending", "1" => "Active", "126" => "Disable");

    $queryString = pro_db_query(
        "select packageID, packageName, packageNickName, packagePrice, status from packageMaster"
    );

    while ($res = pro_db_fetch_array($queryString)) {
        $pk = "packageID:" . $res['packageID'];
        $packageNickName = '<td>' . ucfirst($res['packageNickName']) . '</td>';
        $packageName = '<td>' . ucfirst($res['packageName']) . '</td>';
        $packagePrice = '<td>₹ ' . number_format($res['packagePrice'], 2, '.', ',') . '</td>';

        if (isset($res['status'])) {
            if ($res['status'] == 1) {
                $status = '<td><span class="badge badge-info">' . $statusArray[$res['status']] . '</span></td>';
            } else {
                $status = '<td><span class="badge badge-danger">' . $statusArray[$res['status']] . '</span></td>';
            }
        } else {
            $status = '<td><span class="badge badge-secondary">' . "Not Available" . '</td>';
        }
        $action = '<td><a href="index.php?controller=ggatemain&action=ggatepackages&subaction=editForm&packageID=' . $res['packageID'] . '" title="Edit" ><i class="fe-edit text-warning"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
				        <a href="index.php?controller=ggatemain&action=ggatepackages&subaction=packageDisable&packageID=' . $res['packageID'] . '" title="Disable" ><i class="fe-trash-2 text-danger"></i></a>&nbsp;&nbsp;<span class="text-secondary">|</span>&nbsp;
				        <a href="index.php?controller=ggatemain&action=ggatepackages&subaction=packageInfoDetails&packageID=' . $res['packageID'] . '" title="Info" ><i class="fas fa-info-circle text-info"></i></a>
				    </td>';
        $result['aaData'][] = array("$packageNickName", "$packageName", "$packagePrice", "$status", "$action");
    }
    echo json_encode($result);
} else {
?>
    <script>
        location.href = "login.php";
    </script>
<?php
}
?>