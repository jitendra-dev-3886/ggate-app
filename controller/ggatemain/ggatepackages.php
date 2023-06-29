<?php
class ggatepackages
{
    protected $redirectUrl;
    protected $controller;
    protected $action;
    protected $addformaction;
    protected $addformmoduleaction;
    protected $editformaction;
    protected $mediaType;

    public function __construct($controller = null, $action = null, $redirectUrl = null)
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->redirectUrl = $redirectUrl;
        $this->addformaction = $this->redirectUrl . "&subaction=add";
        $this->addformmoduleaction = $this->redirectUrl . "&subaction=assignModules";
        $this->editformaction = $this->redirectUrl . "&subaction=edit";

        if (IS_PRODUCTION == 1) {
            $this->mediaType = "masters";
        } else {
            $this->mediaType = "masters-dev";
        }
    }

    public function addForm()
    {
        $status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"));
        // $selModules = getMasterList("permissionMaster", "moduleTitle", "moduleTitle", 'userID = ' . $userID);
        $selModules = getMasterList("permissionMaster", "moduleTitle", "moduleTitle", 'userID = 0');
        $parentID = generateOptions(getMasterList('packageMaster', 'packageID', 'packageName', "status = 1"));
?>
        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>Add Package Details</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Package Name:</label>
                                    <input type="text" name="packageName" class="form-control" required placeholder="Package Name">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Package Short Name:</label>
                                    <input type="text" name="packageNickName" class="form-control" placeholder="Package Nick Name">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Packages Included:</label>
                                    <select name="parentID[]" id="parentID" class="form-control custom-select mr-sm-2" multiple required>
                                        <?php echo $parentID; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Package Price:</label>
                                    <input type="text" name="packagePrice" class="form-control" required placeholder="Package Price">
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Status:</label>
                                    <select name="status" class="form-control custom-select mr-sm-2">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('option').mousedown(function(e) {
                e.preventDefault();
                $(this).prop('selected', !$(this).prop('selected'));
                return false;
            });
        </script>

        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>Manage Permission</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="addForm" id="addForm" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" aria-label="Checkbox for following text input">
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" aria-label="Text input with checkbox">
                                </div>

                                <div class="form-group col-sm-12">
                                    <label class="col-xs-6">Manage Permission</label>
                                    <hr>
                                </div>
                                <div class="col-sm-6 offset-sm-1">
                                    <div class="custom-control custom-switch">
                                        <input class="custom-control-input" type="checkbox" id="selectall" value="false">
                                        <label class="custom-control-label" for="selectall">
                                            Select All
                                        </label>
                                    </div>
                                    <hr>
                                    <?php
                                    $i = 0;
                                    $moduleSql = pro_db_query("select * from moduleMaster where status = 1 and parentID = 0
                                                            and isGGATEService = 0 order by sortorder");
                                    $pSelected = "";
                                    $cSelected = "";
                                    while ($mprs = pro_db_fetch_array($moduleSql)) {
                                        $i++;
                                        if (in_array($mprs['moduleFile'], $selModules)) {
                                            $pSelected = " checked";
                                        } else {
                                            $pSelected = "";
                                        }
                                        echo '
										<div class="custom-control custom-switch">
											<input class="custom-control-input case" type="checkbox" name="modules[]" value="' . $mprs['moduleFile'] . '" id="modules_' . $i . '" ' . $pSelected . '>
											<label class="custom-control-label" for="modules_' . $i . '"><strong>' . ucfirst($mprs['moduleTitle']) . '</strong></label>
										</div>';
                                        $moduleCSql = pro_db_query("select * from moduleMaster where status = 1 and parentID = " . $mprs['moduleID'] . "
                                                                    and isGGATEService = 0 order by sortorder");
                                        if (pro_db_num_rows($moduleCSql) > 0) {
                                            while ($mcrs = pro_db_fetch_array($moduleCSql)) {
                                                $i++;
                                                if (in_array($mcrs['moduleFile'], $selModules)) {
                                                    $cSelected = " checked";
                                                } else {
                                                    $cSelected = "";
                                                }
                                                echo '
												<div class="custom-control custom-switch offset-sm-1">
													<input class="custom-control-input case" type="checkbox" name="modules[]" value="' . $mcrs['moduleFile'] . '"  id="modules_' . $i . '" ' . $cSelected . '>
													<label class="custom-control-label" for="modules_' . $i . '">' . ucfirst($mcrs['moduleTitle']) . '</label>
												</div>';
                                            }
                                        }
                                    }
                                    if (in_array('CPC', $selModules)) {
                                        $pSelected = " checked";
                                    } else {
                                        $pSelected = "";
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-sm-12 offset-sm-1">
                                    <hr>
                                    <div class="form-group">
                                        <label></label>
                                        <input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
                                        <input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
                                        <button type="submit" class="btn btn-success">Save</button>&nbsp;&nbsp;<button type="reset" class="btn btn-default back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>&subaction=listData">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    public function editForm()
    {
        $qry = pro_db_query("select * from packageMaster where packageID = " . (int)$_REQUEST['packageID']);
        $rs = pro_db_fetch_array($qry);
        $status = generateStaticOptions(array("1" => "Enable", "126" => "Disable"), $rs['status']);
        //$parentID = generateOptions(getMasterList('packageMaster', 'packageID', 'packageName', "status = 1"));
    ?>
        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>Edit Package Details</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Package Name:</label>
                                    <input type="text" name="packageName" class="form-control" value="<?php echo $rs['packageName']; ?>" placeholder="Package Name" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Package Short Name:</label>
                                    <input type="text" name="packageNickName" class="form-control" value="<?php echo $rs['packageNickName']; ?>" placeholder="Package Nick Name">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Packages Included:</label>
                                    <select name="parentID[]" id="parentID" class="form-control custom-select mr-sm-2" multiple required>
                                        <?php
                                        $arr_parent = explode(",", $rs['parentID']);
                                        $packagesql = pro_db_query("select packageID, packageName from packageMaster where status = 1");
                                        while ($packagers = pro_db_fetch_array($packagesql)) {
                                            if (in_array($packagers['packageID'], $arr_parent)) {
                                                print '<option value="' . $packagers['packageID'] . '" selected>&nbsp;&nbsp;&nbsp;&nbsp;' . $packagers['packageName'] . '</option>';
                                            } else {
                                                print '<option value="' . $packagers['packageID'] . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $packagers['packageName'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Package Price:</label>
                                    <input type="text" name="packagePrice" class="form-control" value="<?php echo $rs['packagePrice']; ?>" placeholder="Package Price" required>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Status:</label>
                                    <select name="status" class="form-control custom-select mr-sm-2">
                                        <?php echo $status; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <input type="hidden" name="packageID" value="<?php echo (int)$rs['packageID']; ?>">
                                    <button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('option').mousedown(function(e) {
                e.preventDefault();
                $(this).prop('selected', !$(this).prop('selected'));
                return false;
            });
        </script>
    <?php
    }

    public function add()
    {
        global $frmMsgDialog;
        $formdata['packageName'] = $_POST['packageName'];
        $formdata['packageNickName'] = $_POST['packageNickName'];
        $formdata['packagePrice'] = $_POST['packagePrice'];
        $formdata['createdate'] = date('Y-m-d H:i:s');
        $formdata['modifieddate'] = date('Y-m-d H:i:s');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

        $packagesList = implode(', ', $_POST['parentID']);
        $formdata['parentID'] = $packagesList;

        //Manage Entry
        if (pro_db_perform('packageMaster', $formdata)) {
            $packageID = pro_db_insert_id();

            //Package Module Master entry
            $arr_parent = $_POST['parentID'];
            if (count($arr_parent) > 0) {
                foreach ($arr_parent as $key => $value) {
                    $packagemoduledata = array();
                    $packagemoduledata['packageID'] = $packageID;
                    $packagemoduledata['moduleID'] = 0;
                    $packagemoduledata['parentID'] = $value;
                    $packagemoduledata['createdate'] = date('Y-m-d H:i:s');
                    $packagemoduledata['modifieddate'] = date('Y-m-d H:i:s');
                    $packagemoduledata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

                    pro_db_perform('packageModuleMaster', $packagemoduledata);
                }
            }
            $msg = '<p class="bg-success p-3">Package Details is saved successfully...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Package Details is not saved!!!!!!</p>';
        }
        $rUrl = $this->redirectUrl . "&subaction=listData";
        echo sprintf($frmMsgDialog, $rUrl, $msg);
    }

    public function edit()
    {
        global $frmMsgDialog;
        $packageID = $_POST['packageID'];
        $formdata['packageName'] = $_POST['packageName'];
        $formdata['packageNickName'] = $_POST['packageNickName'];
        $formdata['packagePrice'] = $_POST['packagePrice'];
        $formdata['createdate'] = date('Y-m-d H:i:s');

        //Default Params
        $formdata['modifieddate'] = date('Y-m-d H:i:s');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
        $packagesList = implode(', ', $_POST['parentID']);
        $formdata['parentID'] = $packagesList;

        if (pro_db_perform('packageMaster', $formdata, 'update', "packageID = " . $packageID)) {

            //Package Module Master entry
            pro_db_query("delete from packageModuleMaster where moduleID = 0 and parentID > 0 and packageID = " . $_POST['packageID']);
            $arr_parent = $_POST['parentID'];
            if (count($arr_parent) > 0) {
                foreach ($arr_parent as $key => $value) {
                    $packagemoduledata = array();
                    $packagemoduledata['packageID'] =  $_POST['packageID'];
                    $packagemoduledata['moduleID'] = 0;
                    $packagemoduledata['parentID'] = $value;
                    $packagemoduledata['createdate'] = date('Y-m-d H:i:s');
                    $packagemoduledata['modifieddate'] = date('Y-m-d H:i:s');
                    $packagemoduledata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
                    pro_db_perform('packageModuleMaster', $packagemoduledata);
                }
            }
            $msg = '<p class="bg-success p-3">Package Details are updated...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Package Details are not updated!!!</p>';
        }
        $rUrl = $this->redirectUrl . "&subaction=listData";
        echo sprintf($frmMsgDialog, $rUrl, $msg);
    }

    public function listData()
    {
        $formaction = $this->redirectUrl . "&subaction=addForm";
    ?>
        <div class="row">
            <div class="col-sm-9 py-3 mt-2">
                <h4>GGATE Packages</h4>
            </div>
            <div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Package</a></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="ggatePackagesList" width="100%">
                                <thead>
                                    <tr>
                                        <th width="20%">Package</th>
                                        <th width="30%">Package Name</th>
                                        <th width="20%">Package Price</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="20%">Package</th>
                                        <th width="30%">Package Name</th>
                                        <th width="20%">Package Price</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var listURL = 'helperfunc/ggatePackagesList.php';
            $('#ggatePackagesList').dataTable({
                "ajax": listURL,
                "order": [],
                "deferRender": true,
                "stateSave": true,
                "iDisplayLength": 25
            });
        </script>
        <script>
            var hash = new Date().getTime();
            $('select[name="countries_id"]').on('change', function() {
                $('select[name="zone_id"]').load("ajax/states.php?hash=" + hash, {
                    id: $(this).val(),
                    ajax: 'true'
                });
            });
        </script>
    <?php
    }

    public function packageInfoDetails()
    {
        $packageID = (int)$_REQUEST['packageID'];
        $selModules = getMasterList("moduleMaster", "moduleID", "moduleTitle", 'parentID = 0');
        $parentqry = pro_db_query("select parentID, packageName from packageMaster where packageID = " . (int)$_REQUEST['packageID']);
        $parentrs = pro_db_fetch_array($parentqry);

        $modulearr = array();
        $modulesql = pro_db_query("select moduleID from packageModuleMaster where packageID in (" . $parentrs['parentID'] . "," . $_REQUEST['packageID'] . ")");
        while ($modulers = pro_db_fetch_array($modulesql)) {
            $modulearr[] = $modulers['moduleID'];
        }
    ?>
        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>
                    <?php
                    echo $parentrs["packageName"];
                    ?>
                    :- Manage Module Options
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="addForm" id="addForm" class="form-horizontal" action="<?php echo $this->addformmoduleaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6 offset-sm-1">
                                    <div class="custom-control custom-switch">
                                        <input class="custom-control-input" type="checkbox" id="selectall" checked>
                                        <label class="custom-control-label" for="selectall">
                                            Select All
                                        </label>
                                    </div>
                                    <hr>
                                    <?php
                                    $i = 0;
                                    // $moduleSql = pro_db_query("select * from moduleMaster where status = 1 and parentID = 0 and isGGATEService = 0 order by sortorder");
                                    $moduleSql = pro_db_query("select * from moduleMaster where status = 1 and parentID = 0 order by sortorder");
                                    $pSelected = "";
                                    $cSelected = "";
                                    while ($mprs = pro_db_fetch_array($moduleSql)) {
                                        $i++;
                                        if (in_array($mprs['moduleID'], $modulearr)) {
                                            $pSelected = " checked";
                                        } else {
                                            $pSelected = "";
                                        }
                                        echo '
										<div class="custom-control custom-switch">
											<input class="custom-control-input case" type="checkbox" name="modules[]" value="' . $mprs['moduleID'] . '" id="modules_' . $i . '" ' . $pSelected . '>
											<label class="custom-control-label" for="modules_' . $i . '"><strong>' . ucfirst($mprs['moduleTitle']) . '</strong></label>
										</div>';
                                        // $moduleCSql = pro_db_query("select * from moduleMaster where status = 1 and parentID = " . $mprs['moduleID'] . " and isGGATEService = 0 order by sortorder");
                                        $moduleCSql = pro_db_query("select * from moduleMaster where status = 1 and parentID = " . $mprs['moduleID'] . " order by sortorder");
                                        if (pro_db_num_rows($moduleCSql) > 0) {
                                            while ($mcrs = pro_db_fetch_array($moduleCSql)) {
                                                $i++;
                                                if (in_array($mcrs['moduleID'], $modulearr)) {
                                                    $cSelected = " checked";
                                                } else {
                                                    $cSelected = "";
                                                }
                                                echo '
												<div class="custom-control custom-switch offset-sm-1">
													<input class="custom-control-input case" type="checkbox" name="modules[]" value="' . $mcrs['moduleID'] . '"  id="modules_' . $i . '" ' . $cSelected . '>
													<label class="custom-control-label" for="modules_' . $i . '">' . ucfirst($mcrs['moduleTitle']) . '</label>
												</div>';
                                            }
                                        }
                                    }
                                    if (in_array('CPC', $selModules)) {
                                        $pSelected = " checked";
                                    } else {
                                        $pSelected = "";
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-sm-12">
                                    <hr>
                                    <div class="form-group">
                                        <label></label>
                                        <input type="hidden" name="societyID" value="<?php echo $_SESSION['societyID']; ?>">
                                        <input type="hidden" name="packageID" value="<?php echo $_REQUEST['packageID']; ?>">
                                        <input type="hidden" name="memberID" value="<?php echo $_SESSION['memberID']; ?>">
                                        <input type="hidden" name="parentID" value="<?php echo $parentrs['parentID']; ?>">
                                        <button type="submit" class="btn btn-success">Assign</button>&nbsp;&nbsp;<button type="reset" class="btn btn-default back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>&subaction=listData">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    public function packageDisable()
    {
        global $frmMsgDialog;
        $delsql = "Update packageMaster set status = 126 where packageID = " . (int)$_REQUEST['packageID'];
        if (pro_db_query($delsql)) {
            $msg = '<p class="bg-success p-3">Package is disabled...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Unable to disable Package!!!</p>';
        }
        $rUrl = $this->redirectUrl . "&subaction=listData";
        echo sprintf($frmMsgDialog, $rUrl, $msg);
    }

    public function assignModules()
    {
        global $frmMsgDialog;
        // $formdata['createdate'] = date('Y-m-d');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];
        $formdata['packageID'] = (int)$_POST['packageID'];
        $formdata['status'] = 1;
        $error = 0;
        /* First Delete all Permissions */
        $delQry = pro_db_query("delete from packageModuleMaster where packageID = " . (int)$_POST['packageID']);
        $arr_parent = explode(",", $_POST['parentID']);

        if (count($arr_parent) > 0) {
            foreach ($arr_parent as $key => $value) {
                $formdata['moduleID'] = 0;
                $formdata['parentID'] = $value;
                if (pro_db_perform('packageModuleMaster', $formdata)) {
                } else {
                    $error = 1;
                }
            }
        }

        $arrModules = array();
        $modulesql = pro_db_query("select moduleID from packageModuleMaster where packageID in (" . $_POST['parentID'] . ")");
        while ($modulers = pro_db_fetch_array($modulesql)) {
            $arrModules[] = $modulers['moduleID'];
        }

        $modules = $_POST['modules'];
        foreach ($modules as $key => $value) {
            if (!in_array($value, $arrModules)) {
                $formdata['moduleID'] = $value;
                $formdata['parentID'] = 0;
                if (pro_db_perform('packageModuleMaster', $formdata)) {
                } else {
                    $error = 1;
                }
            }
        }

        if ($error > 0) {
            $msg = '<p class="bg-danger">Package Details are not saved!!!!!!</p>';
        } else {
            $msg = '<p class="bg-success">Package Details are saved successfully...</p>';
        }
        $rUrl = $this->redirectUrl . "&subaction=listData";
        echo sprintf($frmMsgDialog, $rUrl, $msg);
    }
}
?>