<?php
class ggateversions
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
        $appType = generateStaticOptions(array("0" => "Dashboard", "1" => "User App", "2" => "Security App"));
        $forceUpdate = generateStaticOptions(array("1" => "Yes", "0" => "No"));
        $maintenance = generateStaticOptions(array("1" => "Yes", "0" => "No"));
        $inStoreReview = generateStaticOptions(array("1" => "Yes", "0" => "No"));
        $isCurrent = generateStaticOptions(array("1" => "Yes", "0" => "No"));
?>
        <div class="row">
            <div class="col-sm-12 py-3 mt-2">
                <h4>Add GGATE Version</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form role="form" name="frmAdd" class="form-horizontal" action="<?php echo $this->addformaction; ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Version Name:</label>
                                    <input type="text" name="versionName" class="form-control" placeholder="Version Name" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Type:</label>
                                    <select name="appType" class="form-control custom-select mr-sm-2">
                                        <?php echo $appType; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>In-Store Review:</label>
                                    <select name="inStoreReview" class="form-control custom-select mr-sm-2">
                                        <?php echo $inStoreReview; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Current Version:</label>
                                    <select name="isCurrent" class="form-control custom-select mr-sm-2">
                                        <?php echo $isCurrent; ?>
                                    </select>
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
                                    <label>App Update Message:</label>
                                    <textarea name="appUpdateMessage" class="form-control aeditor" rows="3" onkeyup="if(this.value.length > 0) document.getElementById('start_button').disabled = false; else document.getElementById('start_button').disabled = true;"></textarea>
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
        $qry = pro_db_query("select * from ggateVersion  where versionID = " . (int)$_REQUEST['versionID']);
        $rs = pro_db_fetch_array($qry);
        $status = generateStaticOptions(array("1" => "Enable", "0" => "Disable"), $rs['status']);
        $appType = generateStaticOptions(array("0" => "Dashboard", "1" => "User App", "2" => "Security App"), $rs['appType']);
        $forceUpdate = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['forceUpdate']);
        $maintenance = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['maintenance']);
        $inStoreReview = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['inStoreReview']);
        $isCurrent = generateStaticOptions(array("1" => "Yes", "0" => "No"), $rs['isCurrent']);
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
                                    <label>Version Name:</label>
                                    <input type="text" name="versionName" class="form-control" value="<?php echo $rs['versionName']; ?>" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label>App Type:</label>
                                    <select name="appType" class="form-control custom-select mr-sm-2">
                                        <?php echo $appType; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>In-Store Review:</label>
                                    <select name="inStoreReview" class="form-control custom-select mr-sm-2">
                                        <?php echo $inStoreReview; ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label>Current Version:</label>
                                    <select name="isCurrent" class="form-control custom-select mr-sm-2">
                                        <?php echo $isCurrent; ?>
                                    </select>
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
                                    <label>App Update Message:</label>
                                    <textarea name="appUpdateMessage" class="form-control aeditor" rows="3" onkeyup="if(this.value.length > 0) document.getElementById('start_button').disabled = false; else document.getElementById('start_button').disabled = true;"><?php echo $rs['appUpdateMessage']; ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <input type="hidden" name="versionID" value="<?php echo (int)$rs['versionID']; ?>">
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
        $formdata = $_POST;
        $formdata['createdate'] = date('Y-m-d H:i:s');
        $formdata['modifieddate'] = date('Y-m-d H:i:s');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

        if ($_POST['isCurrent'] == 1) {
            pro_db_query("update ggateVersion set isCurrent = 0");
        }

        //Manage Entry
        if (pro_db_perform('ggateVersion', $formdata)) {
            $versionID = pro_db_insert_id();
            $msg = '<p class="bg-success p-3">Version Details is saved successfully...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Version Details is not saved!!!!!!</p>';
        }
        $rUrl = $this->redirectUrl . "&subaction=listData";
        echo sprintf($frmMsgDialog, $rUrl, $msg);
    }

    public function edit()
    {
        global $frmMsgDialog;
        $versionID = $_POST['versionID'];

        $formdata = $_POST;
        $formdata['createdate'] = date('Y-m-d H:i:s');
        $formdata['modifieddate'] = date('Y-m-d H:i:s');
        $formdata['remote_ip'] = $_SERVER['REMOTE_ADDR'];

        if ($_POST['isCurrent'] == 1) {
            pro_db_query("update ggateVersion set isCurrent = 0 where appType = 1");
        }

        if (pro_db_perform('ggateVersion', $formdata, 'update', "versionID = " . $versionID)) {
            $msg = '<p class="bg-success p-3">Version Details are updated...</p>';
        } else {
            $msg = '<p class="bg-danger p-3">Version Details are not updated!!!</p>';
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
                <h4>GGATE Versions</h4>
            </div>
            <div class="col-sm-3 py-3 mt-2"><a href="<?php echo $formaction; ?>" class="btn btn-info float-right"><i class="fe-plus"></i>&nbsp;&nbsp;Add Version</a></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table cellpadding="1" cellspacing="2" border="0" class="table table-striped table-bordered dataTable" id="ggateVersionList" width="100%">
                                <thead>
                                    <tr>
                                        <th align="left" width="20%">Version Name</th>
                                        <th align="left" width="20%">App Type</th>
                                        <th align="left" width="10%">Currrent</th>
                                        <th align="left" width="10%">Force Update</th>
                                        <th align="left" width="10%">Maintenance</th>
                                        <th align="left" width="10%">In Review</th>
                                        <th align="left" width="10%">Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th align="left" width="20%">Version Name</th>
                                        <th align="left" width="20%">App Type</th>
                                        <th align="left" width="10%">Currrent</th>
                                        <th align="left" width="10%">Force Update</th>
                                        <th align="left" width="10%">Maintenance</th>
                                        <th align="left" width="10%">In Review</th>
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
            var listURL = 'helperfunc/ggateVersionList.php';
            $('#ggateVersionList').dataTable({
                "ajax": listURL,
                "order": [],
                "deferRender": true,
                "stateSave": true,
                "iDisplayLength": 25
            });
            $('.table').editable({
                selector: 'a.estatus',
                params: {
                    "tblName": "ggateVersion"
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
                selector: 'a.emaintenance,a.eforceUpdate,a.einStoreReview',
                params: {
                    "tblName": "ggateVersion"
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