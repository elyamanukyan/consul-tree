<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
if (empty($_SESSION["authenticated"]) || $_SESSION["authenticated"] != 'true') {
    header('Location: login.php');
}

$userRights = (string)$_SESSION['rights'];
$calledUrl = "$_SERVER[REQUEST_URI]";

if (strpos($calledUrl, 'backend') == false) {
    $calledLoc = '';
    $backendStatus = 'backend/';
} else {
    $calledLoc = '../';
    $backendStatus = '';
}

$autoText = $_SESSION["auto"] ? "automatically" : "";;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title id="pageTitle"></title>
    <link rel="shortcut icon" type="image/png" href="<?php echo $calledLoc; ?>lib/favicon.png"/>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $calledLoc; ?>lib/themes/default/style.min.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $calledLoc; ?>lib/css/tree.css"/>
    <script src="<?php echo $calledLoc; ?>lib/js/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <script src="<?php echo $calledLoc; ?>lib/js/jstree.js"></script>
</head>
<body>
<nav class="navbar navbar-light bg-light fixed-top navbar-expand-sm">
    <div class="container">
        <a class="navbar-brand" href="#" id="consulTitleID"></a>
        <form class="form-inline"
              action="<?php echo $backendStatus; ?>logout.php">
            <div class="form-group">
                <label for="consulUrlSelectorId">Consul locations</label>
            </div>
            <div class="col-auto">
                <select class="form-control" id="consulUrlSelectorId" title="Consul-Urls"></select>
                <button class="btn btn-dark" id="resetLocationBtnId" data-toggle="tooltip" data-placement="bottom" title="Reset consul location settings"><span class="fa fa-refresh" aria-hidden="true"></span></button>
                <button class="btn btn-danger" data-toggle="tooltip" data-placement="bottom"
                        title="Logged in <?php echo $autoText; ?> as <?php echo $_SESSION['username']; ?>">Logout</button>
            </div>
        </form>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col padded-right-middle">
                <div class="form-group">

                <label class="sr-only" for="searchInputId">Search</label>
                <input id="searchInputId" value="" class="form-control search-box"
                       placeholder="Search" style="margin:0 auto 1em auto; "> <span id="searchClear" class="fa fa-search"></span>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <button type="button" id="importExportBtnId" class="btn btn-primary writeACL"
                            disabled data-toggle="modal" data-target="#importExportModalId">
                    Import</button>
                <button type="button" class="btn btn-warning readACL" disabled id="enableExportBtnId">
                    Enable Export</button>
                <button type="button" class="btn btn-info hidden readACL" disabled id="disableManualExport">
                    Disable Export</button>
                <button type="button" class="btn btn-success hidden readACL" disabled id="exportSelection">
                    Export Selection</button>
                <button type="button" class="btn btn-primary writeACL" disabled id="createRootBtnId">
                    Create Folder / Key</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div id="ConsulTree" class="card bg-light card-body mb-3"></div>
        </div>
        <div class="col">
            <div class="border-left" id="generalValueAreaID" style="position: fixed; width: 568px; padding-left: 15px">
                <div id="keyValueFieldsid" class="card bg-light mb-3">
                    <div class="card-header">sdfsdf</div>
                    <div class="card-body"> <span id="createElementText" style="color: #737373" class="readACL">Select a key to get its value<span class="writeACL">, right-click on the tree to create an element</span>.</span>
                        <div class="form-group update-control">
                            <textarea class="form-control update-control hidden" id="cKeyValue" rows="8" readonly title="Value"></textarea>
                        </div>
                        <button type="button" disabled id="valueUpdateBtnId" class="btn btn-primary update-control hidden writeACL">Update</button> <span class="update-control hidden writeACL" style="color: #737373">&#xA0;&#xA0;To create an element, right-click on the tree.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="createNodeModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Folder / Key</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <h5 class="control-label">Folder / Key Name : <i>To create a folder, end the
                                key with /</i></h5>
                        <input class="form-control" id="keyInputId" value="" title="Folder / Key Name">
                        <input type="hidden" class="form-control" id="pathInputId" value="">
                    </div>
                    <div class="form-group">
                        <h5 class="control-label">Path : </h5>
                        <textarea class="form-control" id="pathDescribeID" readonly title="Path"></textarea>
                    </div>
                </form>
                <h5 class="control-label inputKeyValueClass">Value : </h5>
                <textarea class="form-control inputKeyValueClass" id="inputKeyValueId" title="Value"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" id="createKeyBtnId" class="btn btn-info">Yes</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="importExportModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Consul Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>Browse JSON file : </label>
                        <input type="file" id="jsonInputFile">
                    </div>
                    <button type="button" id="importConsulBtnId" class="btn btn-info">Import</button>
                    <span style="color: #737373">&nbsp;|&nbsp;Only applicable if the JSON file was exported from the Consul-tree.</span>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="noTreeModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header modal-header-warning">
                <h5 class="modal-title">No Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <span><strong>No Data</strong> was found on Consul, either <strong>Create a folder at the root position</strong>, or <strong>Import</strong> an existing Tree form a previous export</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="noConnectionModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header modal-header-danger">
                <h5 class="modal-title">No Connection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <span>Check the connection between the <strong>Consul-Tree</strong> and <strong
                            id="consulUrlId"></strong> and then try again.</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="renameModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Node</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <h5 class="control-label">Current node name: </h5>
                    <input id="oldNodePathId" value="" class="form-control" title="Current node name" readonly>
                </div>
                <div class="form-group">
                    <h5 class="control-label">New node name: </h5>
                    <input id="newNodePathId" value="" title="New node name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="renameConfirmBtnId" data-type="rename" disabled class="btn btn-info">Rename</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="overwriteModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Overwrite values</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <span>
                    Do you want to <strong>Overwrite</strong> the existing key values ?
                </span>
            </div>
            <div class="modal-footer">
                <button type="button" data-answer=1 class="btn btn-success overwriteBtn">Yes</button>
                <button type="button" data-answer=0 class="btn btn-danger overwriteBtn">No</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="connectingModalId" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center">Establishing connection with <strong id="consulFullUrlId"></strong></p>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="processingMdlID" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header modal-header-primary" style="border-bottom: 0">
                <h5 class="modal-title text-center">Processing your request, please wait...</h5>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="loadingTreeMdlID" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0">
                <h5 class="modal-title text-center">Validating Tree structure, please wait...</h5>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<p class="hidden" id="userRights"></p>
<p class="hidden" id="selectedNodeID"></p>
<p class="hidden" id="gotNodeValue"></p>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="pageFooter">
    <div class="container">
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <span class="navbar-text">Consul-tree v6.7</span>
            </ul>
            <a class="navbar-text" href="https://github.com/vagharsh/consul-tree" target="_blank">GitHub Project</a>
        </div>
    </div>
</nav>

<script src="<?php echo $calledLoc; ?>lib/js/functions.js"></script>
<script src="<?php echo $calledLoc; ?>lib/js/triggers.js"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('#pageTitle').text("Consul Tree | " + window.location.hostname);
        $.getJSON("<?php echo $calledLoc; ?>config/config.json", function (consul) {
            if (consul) if (consul.length !== 0) {
                var selectedConsulJson, tree,
                    userRights = "<?php echo $userRights; ?>",
                    backendStatus = "<?php echo $backendStatus; ?>",
                    consulTreeDivID = $('#ConsulTree'),
                    leftPos = consulTreeDivID.outerWidth() + consulTreeDivID.offset().left,
                    backendPath = returnBackend(backendStatus);

                localStorage['consulConfig'] = JSON.stringify(consul);
                localStorage['backendPath'] = backendPath;
                $('#generalValueAreaID').css("left", leftPos + 14 + "px");

                getConsulLocations();
                checkRights(userRights);
                selectedConsulJson = getSetConfig();

                tree = {
                    'contextmenu': {'items': customMenu},
                    'check_callback': true,
                    'plugins': ['contextmenu', 'types', 'state', 'search', 'wholerow'],
                    'core': {
                        "multiple": false,
                        "animation": 0,
                        "check_callback": true,
                        "themes": {"stripes": true},
                        'data': []
                    }
                };

                if (localStorage['treeBackup']) {
                    localStorage['jstree'] = localStorage['treeBackup'];
                    localStorage.removeItem('treeBackup');
                }

                getTree(tree, selectedConsulJson.url + "?keys");
            }
        });
    });
</script>
</body>
</html>
