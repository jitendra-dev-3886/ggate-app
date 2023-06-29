<?php
class ggatemodules
{
    protected $redirectUrl;
    protected $controller;
    protected $action;
    protected $addformaction;
    protected $editformaction;
    protected $mediaType;

    public function __construct($controller = null, $action = null, $redirectUrl = null)
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->redirectUrl = $redirectUrl;
        $this->addformaction = $this->redirectUrl . "&subaction=add";
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
        $arrModules = getMasterList('moduleMaster', 'moduleID', 'moduleTitle', 'parentID = 0', 'sortorder');
        if (isset($arrModules[0]) && $arrModules[0] == "No Value Defined..") {
            $arrModules["0"] = "No Parent";
        }
        $parentModule = generateOptions($arrModules);
?>
        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>Add GGATE Module</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Parent Module:</label>
                                    <select name="parentID" class="form-control custom-select mr-sm-2" id="parentID">
                                        <option value="" hidden>Select Parent</option>
                                        <?php echo $parentModule; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Module Title:</label>
                                    <input type="text" name="moduleTitle" class="form-control" placeholder="Module Title" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Module File:</label>
                                    <input type="text" name="moduleFile" class="form-control" placeholder="Module File" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Module Icon:</label>
                                    <input type="text" name="moduleIcon" class="form-control" placeholder="Module Icon">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Is Application Service:</label>
                                    <select name="isAppService" class="form-control custom-select mr-sm-2">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Service Key:</label>
                                    <input type="text" name="appServiceKey" class="form-control" placeholder="App Service Key">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Service Name:</label>
                                    <input type="text" name="appServiceName" class="form-control" placeholder="App Service Key">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Service Icon:</label>
                                    <input type="file" name="appServiceIcon" id="appServiceIcon" class="form-control appServiceIcon">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Is GGATE Service:</label>
                                    <select name="isGGATEService" class="form-control custom-select mr-sm-2">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Is Office Service:</label>
                                    <select name="isOfficeService" class="form-control custom-select mr-sm-2">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Sort Order:</label>
                                    <input type="number" name="sortorder" class="form-control" placeholder="Sort Order" required>
                                </div>
                                <div class="form-group col-sm-3">
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
    <?php
    }

    public function editForm()
    {
        $qry = pro_db_query("select mm.* from moduleMaster mm where mm.moduleID = " . (int)$_REQUEST['moduleID']);
        $rs = pro_db_fetch_array($qry);
        $status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);

        $parentModule = generateOptions(getMasterList('moduleMaster', 'moduleID', 'moduleTitle', 'parentID = 0', 'sortorder'), $rs['parentID']);
        $isGGATEService = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['isGGATEService']);
        $isOfficeService = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['isOfficeService']);
        $isAppService = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['isAppService']);
    ?>
        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>Edit Module Details</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="frmedit" class="form-horizontal" action="<?php echo $this->editformaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Parent Module:</label>
                                    <select name="parentID" class="form-control custom-select mr-sm-2" id="parentID">
                                        <option value="" hidden>Select Parent</option>
                                        <?php echo $parentModule; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Module Title:</label>
                                    <input type="text" name="moduleTitle" class="form-control" placeholder="Module Title" value="<?php echo $rs['moduleTitle']; ?>" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Module File:</label>
                                    <input type="text" name="moduleFile" class="form-control" value="<?php echo $rs['moduleFile']; ?>" placeholder="Module File" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Module Icon:</label>
                                    <input type="text" name="moduleIcon" class="form-control" value="<?php echo $rs['moduleIcon']; ?>" placeholder="Module Icon">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Is Application Service:</label>
                                    <select name="isAppService" class="form-control custom-select mr-sm-2">
                                       <?php echo $isAppService;?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Service Key:</label>
                                    <input type="text" name="appServiceKey" class="form-control" value="<?php echo $rs['appServiceKey']; ?>" placeholder="App Service Key">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Service Name:</label>
                                    <input type="text" name="appServiceName" class="form-control" value="<?php echo $rs['appServiceName']; ?>" placeholder="App Service Key">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Service Icon:</label>
                                    <input type="file" name="appServiceIcon" id="appServiceIcon" class="form-control appServiceIcon">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Is GGATE Service:</label>
                                    <select name="isGGATEService" class="form-control custom-select mr-sm-2">
                                          <?php echo $isGGATEService;?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Is Office Service:</label>
                                    <select name="isOfficeService" class="form-control custom-select mr-sm-2">
                                         <?php echo $isOfficeService;?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Sort Order:</label>
                                    <input type="number" name="sortorder" class="form-control" value="<?php echo $rs['sortorder']; ?>" placeholder="Sort Order" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>Status:</label>
                                    <select name="status" class="form-control custom-select mr-sm-2">
                                        <?php echo $status; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <input type="hidden" name="moduleID" value="<?php echo (int)$rs['moduleID']; ?>">
                                    <input type="hidden" name="packageModuleID" value="<?php echo (int)$rs['packageModuleID']; ?>">
                                    <button type="submit" class="btn btn-success">Update</button>&nbsp;&nbsp;<button type="reset" class="btn btn-secondary back" name="Cancel" data-url="<?php echo $this->redirectUrl; ?>">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // For Datetime Calendar
            $('.enrolledDate').flatpickr({
                enableTime: false,
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            $('.validUptoDate').flatpickr({
                enableTime: false,
                dateFormat: "Y-m-d",
                minDate: "today"
            });
        </script>
    <?php
    }

    public function add()
    {
        global $frmMsgDialog;
        $formdata['moduleTitle'] = $_POST['moduleTitle'];
        $formdata['moduleFile'] = $_POST['moduleFile'];
        $formdata['parentID'] = $_POST['parentID'];
        $formdata['moduleIcon'] = $_POST['moduleIcon'];
        $formdata['isAppService'] = $_POST['isAppService'];
        $formdata['appServiceKey'] = $_POST['appServiceKey'];
        $formdata['appServiceName'] = $_POST['appServiceName'];
        $formdata['isGGATEService'] = $_POST['isGGATEService'];
        $formdata['sortorder'] = $_POST['sortorder'];
        $formdata['status'] = $_POST['status'];
        $formdata['username'] = $_SESSION['username'];
        $formdata['createdate'] = date('Y-m-d H:i:s');
        $formdata['modifieddate'] = date('Y-m-d H:i:s');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

        //Manage Entry
        if (pro_db_perform('moduleMaster', $formdata)) {
            $moduleID = pro_db_insert_id();
            //App Service Icon
            if (!empty($_FILES["appServiceIcon"]["name"])) {
                if ($_FILES["appServiceIcon"]["error"] == 0) {
                    $appServiceIcon = $_FILES["appServiceIcon"]["name"];
                    $logoImage = explode(".", $appServiceIcon);
                    $logoExtension = end($logoImage);

                    $appServiceIconRawData = file_get_contents($_FILES['appServiceIcon']['tmp_name']);
                    $logoObjectName = "ico_module_" . $moduleID . "." . $logoExtension;
                    $appServiceIconName = $this->mediaType . "/" . $logoObjectName;

                    //Upload a file to the bucket.
                    if (gcsUploadFile(GCLOUD_BUCKET, $appServiceIconRawData, $appServiceIconName)) {
                        $finalImageName = GCLOUD_CDN_URL . $appServiceIconName;
                        //Update App Icon into packageModuleMaster
                        $arrImageData = array();
                        $arrImageData['appServiceIcon'] = $finalImageName;
                        pro_db_perform('moduleMaster', $arrImageData, 'update', "moduleID = " . $moduleID);
                    }
                }
            }
            $msg = '<p class="bg-success p-3">Module Details is saved successfully...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Module Details is not saved!!!!!!</p>';
        }
        $rUrl = $this->redirectUrl . "&subaction=listData";
        echo sprintf($frmMsgDialog, $rUrl, $msg);
    }

    public function edit()
    {
        global $frmMsgDialog;
        $moduleID = $_POST['moduleID'];

        $formdata['moduleTitle'] = $_POST['moduleTitle'];
        $formdata['moduleFile'] = $_POST['moduleFile'];
        $formdata['parentID'] = $_POST['parentID'];
        $formdata['moduleIcon'] = $_POST['moduleIcon'];
        $formdata['isAppService'] = $_POST['isAppService'];
        $formdata['appServiceKey'] = $_POST['appServiceKey'];
        $formdata['appServiceName'] = $_POST['appServiceName'];
        $formdata['isGGATEService'] = $_POST['isGGATEService'];
        $formdata['sortorder'] = $_POST['sortorder'];
        $formdata['status'] = $_POST['status'];
        $formdata['username'] = $_SESSION['username'];
        $formdata['createdate'] = date('Y-m-d H:i:s');
        $formdata['modifieddate'] = date('Y-m-d H:i:s');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

        //App Service Logo
        if ($_FILES["appServiceIcon"]['size'] == 0 && !empty($_FILES["appServiceIcon"]["name"])) {
            if ($_FILES["appServiceIcon"]["error"] == 0) {
                $appServiceIcon = $_FILES["appServiceIcon"]["name"];
                $logoImage = explode(".", $appServiceIcon);
                $logoExtension = end($logoImage);

                $appServiceIconRawData = file_get_contents($_FILES['appServiceIcon']['tmp_name']);
                $logoObjectName = "ico_module_" . $moduleID . "." . $logoExtension;
                $appServiceIconName = $this->mediaType . "/" . $logoObjectName;

                //Upload a file to the bucket.
                if (gcsUploadFile(GCLOUD_BUCKET, $appServiceIconRawData, $appServiceIconName)) {
                    $finalImageName = GCLOUD_CDN_URL . $appServiceIconName;
                    $formdata['appServiceIcon'] = $finalImageName;
                } else {
                    unset($formdata['appServiceIcon']);
                }
            } else {
                unset($formdata['appServiceIcon']);
            }
        } else {
            unset($formdata['appServiceIcon']);
        }

        if (pro_db_perform('moduleMaster', $formdata, 'update', "moduleID = " . $moduleID)) {
            $msg = '<p class="bg-success p-3">Module Details are updated...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Module Details are not updated!!!</p>';
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
                <h4>GGATE Modules</h4>
            </div>
            <div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Module</a></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="ggateModuleList" width="100%">
                                <thead>
                                    <tr>
                                        <th align="left" width="20%">Module Name</th>
                                        <th align="left" width="20%">Module File</th>
                                        <th align="left" width="10%">GGATE Service</th>
                                        <th align="left" width="10%">App Service</th>
                                        <th align="left" width="15%">App Service Key</th>
                                        <th align="left" width="5%">Parent</th>
                                        <th align="left" width="10%">Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th align="left" width="20%">Module Name</th>
                                        <th align="left" width="20%">Module File</th>
                                        <th align="left" width="10%">GGATE Service</th>
                                        <th align="left" width="10%">App Service</th>
                                        <th align="left" width="15%">App Service Key</th>
                                        <th align="left" width="5%">Parent</th>
                                        <th align="left" width="10%">Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var listURL = 'helperfunc/ggateModuleList.php';
            $('#ggateModuleList').dataTable({
                "ajax": listURL,
                "order": [],
                "deferRender": true,
                "stateSave": true,
                "iDisplayLength": 25
            });
            $('.table').editable({
                selector: 'a.estatus,a.esortorder',
                params: {
                    "tblName": "moduleMaster"
                },
                source: [{
                    value: '1',
                    text: 'Active'
                }, {
                    value: '0',
                    text: 'Inactive'
                }]
            });
            $('.table').editable({
                selector: 'a.eisGGATEService',
                params: {
                    "tblName": "moduleMaster"
                },
                source: [{
                    value: '1',
                    text: 'GGATE'
                }, {
                    value: '0',
                    text: 'Complex'
                }]
            });
            $('.table').editable({
                selector: 'a.eisAppService',
                params: {
                    "tblName": "moduleMaster"
                },
                source: [{
                    value: '1',
                    text: 'Yes'
                }, {
                    value: '0',
                    text: 'No'
                }]
            });
        </script>
<?php
    }
}
?>
