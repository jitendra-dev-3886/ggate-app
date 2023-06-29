<?php
chdir('../');
include "config/config.php";
include "lib/base.php";
include "lib/general.php";

if (defined('ADMIN_ALLOWED') == true) {
    //Last Activity
    $arrEmployees = array();
    $queryString = pro_db_query("select emp.employeeID, emp.employeeName, emp.employeeCode, log.last_access, log.status
                                from complexEmployeeMaster emp
                                left join complexEmployeeLogMaster log on emp.employeeID = log.employeeID
                                where emp.complexID = " . $_REQUEST['complexID'] . "
                                order by log.last_access desc");
    //group by emp.employeeID   //==> Skip Group by logic to fetch order by desc, and prevent duplicate from php array
    while ($res = pro_db_fetch_array($queryString)) {
        if (!in_array($res['employeeID'], $arrEmployees)) {
            $arrEmployees[] = $res['employeeID'];

            $pk = "employeeID:" . $res['employeeID'];
            $employeeName = '<td>' . ucfirst($res['employeeName']) . '</td>';
            $employeeCode = '<td>' . $res['employeeCode'] . '</td>';

            if ($res['last_access'] != null) {
                $lastAccess = '<td>' . date('d M Y - h:i A', strtotime($res['last_access'])) . '</td>';
            } else {
                $lastAccess = '<td>-</td>';
            }
            if ($res['status'] == 'I') {
                $loggedInStatus = '<td><span class="badge badge-info">Logged In</span></td>';
            } else {
                $loggedInStatus = '<td><span class="badge badge-danger">Logged Out</span></td>';
            }
            $result['aaData'][] = array("$employeeName", "$employeeCode", "$lastAccess", "$loggedInStatus");
        }
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
