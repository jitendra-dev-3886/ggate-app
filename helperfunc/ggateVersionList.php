<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
    $statusArray = array("0" => "Pending", "1" => "Active");
    $appTypeArray = array("0" => "Dashboard", "1" => "User App", "2" => "Security App");
    $forceUpdateArray = array("1" => "Yes", "0" => "No");
    $maintenanceArray = array("1" => "Yes", "0" => "No");
    $inStoreReviewArray = array("1" => "Yes", "0" => "No");
    $isCurrentArray = array("1" => "Yes", "0" => "No");

    $queryString = pro_db_query(
        "select * from ggateVersion order by appType, isCurrent desc, versionName desc"
    );

    while ($res = pro_db_fetch_array($queryString)) {
        $pk = "versionID:" . $res['versionID'];

        $versionName = '<td>' . $res['versionName'] . '</td>';

        if (isset($res['appType'])) {
            if ($res['appType'] == 0) {
                $appType = '<td><span class="badge badge-danger"">' . $appTypeArray[$res['appType']] . '</span></td>';
            } else if ($res['appType'] == '1') {
                $appType = '<td><span class="badge badge-success">' . $appTypeArray[$res['appType']] . '</span></td>';
            } else {
                $appType = '<td><span class="estatus badge badge-warning">' .   $appTypeArray[$res['appType']]  . '</span></td>';
            }
        } else {
            $appType = '<td><span class="estatus badge badge-info">' .   $appTypeArray[$res['appType']]  . '</span></td>';
        }
        if (isset($res['isCurrent'])) {
            if ($res['isCurrent'] == 1) {
                $isCurrent = '<td><span class="badge badge-info"">' . $isCurrentArray[$res['isCurrent']] . '</span></td>';
            } else {
                $isCurrent = '<td><span class="badge badge-danger">' . $isCurrentArray[$res['isCurrent']] . '</span></td>';
            }
        } else {
            $isCurrent = '<td><span class="estatus badge badge-info">' .  "Not Available" . '</span></td>';
        }

        if (isset($res['forceUpdate'])) {
            if ($res['forceUpdate'] == 1) {
                $forceUpdate = '<td><a href="#" class="eforceUpdate badge badge-info" data-type="select" data-name="forceUpdate" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="forceUpdate">' . $forceUpdateArray[$res['forceUpdate']] . '</a></td>';
            } else {
                $forceUpdate = '<td><a href="#" class="eforceUpdate badge badge-danger" data-type="select" data-name="forceUpdate" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="forceUpdate">' . $forceUpdateArray[$res['forceUpdate']] . '</a></td>';
            }
        } else {
            $forceUpdate = '<td><a href="#" class="eforceUpdate badge badge-secondary" data-type="select" data-name="forceUpdate" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="forceUpdate">' . "Not Available" . '</a></td>';
        }
        if (isset($res['maintenance'])) {
            if ($res['maintenance'] == 1) {
                $maintenance = '<td><a href="#" class="emaintenance badge badge-info" data-type="select" data-name="maintenance" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="maintenance">' . $maintenanceArray[$res['maintenance']] . '</a></td>';
            } else {
                $maintenance = '<td><a href="#" class="emaintenance badge badge-danger" data-type="select" data-name="maintenance" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="maintenance">' . $maintenanceArray[$res['maintenance']] . '</a></td>';
            }
        } else {
            $maintenance = '<td><a href="#" class="emaintenance badge badge-secondary" data-type="select" data-name="maintenance" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="maintenance">' . "Not Available" . '</a></td>';
        }
        if (isset($res['inStoreReview'])) {
            if ($res['inStoreReview'] == 1) {
                $inStoreReview = '<td><a href="#" class="einStoreReview badge badge-info" data-type="select" data-name="inStoreReview" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="inStoreReview">' . $inStoreReviewArray[$res['inStoreReview']] . '</a></td>';
            } else {
                $inStoreReview = '<td><a href="#" class="einStoreReview badge badge-danger" data-type="select" data-name="inStoreReview" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="inStoreReview">' . $inStoreReviewArray[$res['inStoreReview']] . '</a></td>';
            }
        } else {
            $inStoreReview = '<td><a href="#" class="einStoreReview badge badge-secondary" data-type="select" data-name="inStoreReview" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="inStoreReview">' . "Not Available" . '</a></td>';
        }

        if (isset($res['status'])) {
            if ($res['status'] == 1) {
                $status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
            } else {
                $status = '<td><a href="#" class="estatus badge badge-danger" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' . $statusArray[$res['status']] . '</a></td>';
            }
        } else {
            $status = '<td><a href="#" class="estatus badge badge-info" data-type="select" data-name="status" data-pk="' . $pk . '" data-url="ajax/ajaxUpd.php" data-title="Change Status">' .  "Not Available" . '</a></td>';
        }

        $action = '<td></td>';
        if ($res['appType'] != 0) {
            $action = '<td><a href="index.php?controller=ggatemain&action=ggateversions&subaction=editForm&versionID=' . $res['versionID'] . '" title="Edit" ><i class="fe-edit text-warning"></i></a></td>';
        }
        $result['aaData'][] = array(
            "$versionName", "$appType", "$isCurrent", "$forceUpdate", "$maintenance", "$inStoreReview", "$status", "$action"
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