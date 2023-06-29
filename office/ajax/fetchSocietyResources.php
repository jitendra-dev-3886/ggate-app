<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";
if (defined('ADMIN_ALLOWED') == true) {
    $isComplexResource = $_REQUEST['isComplexResource'];
    $sql = pro_db_query("select staffTypeID, staffTypeTitle from staffTypeMaster where status = 1 and isComplexResource = " . $isComplexResource);
    if (pro_db_num_rows($sql) > 0) {
        $staffType = "";
        $brs = pro_db_fetch_arrays($sql);
        for ($i = 0; $i < count($brs); $i++) {
            $staffType .= '<option value="' . $brs[$i]['staffTypeID'] . '">' . $brs[$i]['staffTypeTitle'] . '</option>';
        }
    }
    print $staffType;
} else {
?>
    <script>
        location.href = "login.php";
    </script>
<?php
}
?>
