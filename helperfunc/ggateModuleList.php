<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
    $statusArray = array("0" => "Pending", "1" => "Active");
    $ggateServiceArray = array("0" => "Complex", "1" => "GGATE");
    $serviceArray = array("0" => "No", "1" => "Yes");

    $queryString = pro_db_query(
        "select mm.* from moduleMaster mm order by moduleID"
    );

    while ($res = pro_db_fetch_array($queryString)) {
        $pk = "moduleID:" . $res['moduleID'];

        $moduleTitle = '<td>' . ucfirst($res['moduleTitle']) . '</td>';
        $moduleFile = '<td>' . $res['moduleFile'] . '</td>';
        $moduleIcon = '<td>' . $res['moduleIcon'] . '</td>';

        $appServiceKey = '<td>' . $res['appServiceKey'] . '</td>';
        $appServiceName = '<td>' . ucfirst($res['appServiceName']) . '</td>';

        if (isset($res['isGGATEService'])) {
            if ($res['isGGATEService'] == 1) {
                $isGGATEService = '<td><a href="#" class="eisGGATEService badge badge-info" data-type="select" data-name="isGGATEService" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="isGGATEService">' . $ggateServiceArray[$res['isGGATEService']] . '</a></td>';
            } else {
                $isGGATEService = '<td><a href="#" class="eisGGATEService badge badge-danger" data-type="select" data-name="isGGATEService" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="isGGATEService">' . $ggateServiceArray[$res['isGGATEService']] . '</a></td>';
            }
        } else {
            $isGGATEService = '<td><a href="#" class="eisGGATEService badge badge-secondary" data-type="select" data-name="isGGATEService" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="isGGATEService">' . "Not Available" . '</a></td>';
        }
        if (isset($res['isAppService'])) {
            if ($res['isAppService'] == 1) {
                $isAppService = '<td><a href="#" class="eisAppService badge badge-info" data-type="select" data-name="isAppService" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="isAppService">' . $serviceArray[$res['isAppService']] . '</a></td>';
            } else {
                $isAppService = '<td><a href="#" class="eisAppService badge badge-danger" data-type="select" data-name="isAppService" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="isAppService">' . $serviceArray[$res['isAppService']] . '</a></td>';
            }
        } else {
            $isAppService = '<td><a href="#" class="eisAppService badge badge-danger" data-type="select" data-name="isAppService" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="isAppService">' . "Not Available" . '</a></td>';
        }
        $isParent = '<td>' . ($res['parentID'] == 0 ? "Yes" : "No") . '</td>';

        if (isset($res['status'])) {
            if ($res['status'] == '1') {
                $status = '<a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
            } else {
                $status = '<a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
            }
        } else {
            $status = '<a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' .  "Not Available" . '</a></td>';
        }
        $action = '<td><a href="index.php?controller=ggatemain&action=ggatemodules&subaction=editForm&moduleID=' . $res['moduleID'] . '" title="Edit" ><i class="fe-edit text-warning"></i></a></td>';
        $result['aaData'][] = array(
            "$moduleTitle", "$moduleFile", "$isGGATEService", "$isAppService", "$appServiceKey", "$isParent", "$status", "$action"
        );
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