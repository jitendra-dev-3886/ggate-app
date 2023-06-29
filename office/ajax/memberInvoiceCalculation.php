<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {

    $memberID = (int)$_REQUEST['memberID'];
    $residentID = (int)$_REQUEST['residentID'];

    if ($residentID > 0) {
        $sql = pro_db_query("select bffm.flatMaintenanceAmt, sas.invoiceType from blockFloorFlatMapping bffm
                            left join complexAccountSettings sas on bffm.societyID = sas.societyID
                            where bffm.status = 1 and bffm.memberID = " . $memberID . " and bffm.flatID = " . $residentID);
    } else {
        $sql = pro_db_query("select bffm.flatMaintenanceAmt, sas.invoiceType from blockFloorFlatMapping bffm
                            left join complexAccountSettings sas on bffm.societyID = sas.societyID
                            where bffm.status = 1 and bffm.isPrimary = 1 and bffm.memberID = " . $memberID);
    }
    if (pro_db_num_rows($sql) > 0) {
        while ($rs = pro_db_fetch_array($sql)) {
            $maintenanceAmount = $rs['flatMaintenanceAmt'];
            $invoiceType = $rs['invoiceType'];
            switch ($invoiceType) {
                case 1:
                    $finalAmount = $maintenanceAmount * 3;
                    break;

                case 2:
                    $finalAmount = $maintenanceAmount * 6;
                    break;

                case 3:
                    $finalAmount = $maintenanceAmount * 12;
                    break;

                default:
                    $finalAmount = $maintenanceAmount;
                    break;
            }
            print $finalAmount;
        }
    }
} else {
?>
    <script>
        location.href = "login.php";
    </script>
<?php
}
?>