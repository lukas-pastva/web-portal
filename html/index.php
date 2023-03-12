<?php

include_once('db.inc.php');
if (!array_key_exists('a', $_REQUEST) && ($_REQUEST['a'] == '')) {
    header('Location: ?a=sysHome');
    die;
}

$action = escapeQuotes($_REQUEST['a']);
$sectionId = array_key_exists('origin', $_REQUEST) ? escapeQuotes($_REQUEST['origin']) : null;
$id = array_key_exists('uid', $_REQUEST) ? escapeQuotes($_REQUEST['uid']) : null;

if ($action == 'api') {
    sysApiCall(escapeQuotes($_REQUEST['apiKey']), escapeQuotes($_REQUEST['action']));
    exit();
}
if ($action == 'cronXXX') {
    echo "\nCalling cron\n";
    sysCron();
    exit();
}

if ($action == 'logout') {
    logout();
}

if ($action == 'login') {
    doLogin(escapeQuotes($_REQUEST['user']), escapeQuotes($_REQUEST['pass']), false);
    die;
}

if (!checkUserLoggedIn()) {
    exit();
}

if ($action == 'sysDetail' && sysCanUserRead($sectionId)) {
    sysDetailGo($sectionId, $id);
} else if ($action == 'sysDetailInvoice' && sysCanUserRead($sectionId)) {
    sysDetailGo($sectionId, $id);
} else if ($action == 'sysInsert' && sysCanUserRead($sectionId) && sysSectionGetType($sectionId) == 'dynamic') {

    sysInsertGo();
} else if ($action == 'sysUpdate' && sysCanUserRead($sectionId) && sysSectionGetType($sectionId) == 'dynamic') {
    sysUpdateGo();
} else if ($action == 'sysDelete' && sysCanUserRead($sectionId) && sysSectionGetType($sectionId) == 'dynamic') {
    sysDeleteGo();
    header("Location: ./?a=" . $_REQUEST['origin']);
    exit();
} else if ($action == 'sysInsert' && sysCanUserRead($sectionId) && sysSectionGetType($sectionId) == 'abstract') {
    $row = escapeFromWebToDb($_REQUEST);
    $section = sysSectionGet($sectionId);
    $attrs = sysModuleAttributesGet($sectionId);
    sysInsertGoAbstract($row, $section, $attrs);
} else if ($action == 'sysUpdate' && sysCanUserRead($sectionId) && sysSectionGetType($sectionId) == 'abstract') {
    $row = escapeFromWebToDb($_REQUEST);
    $section = sysSectionGet($sectionId);
    $attrs = sysModuleAttributesGet($sectionId);
    sysUpdateGoAbstract($row, $section, $attrs, escapeQuotes($_REQUEST['uid']));
} else if ($action == 'sysDelete' && sysCanUserRead($sectionId) && sysSectionGetType($sectionId) == 'abstract') {

    $section = sysSectionGet($sectionId);
    sysDeleteGoAbstract(escapeQuotes($_REQUEST['uid']), $section);
    header("Location: ./?a=" . $_REQUEST['origin']);
    exit();
} // if the menu exists and is dynamic section
else if (array_key_exists($action, sysSectionsGet()) && sysSectionGetType($action) != 'script') {
    sysDisplayList($action);
} // if the menu exists and is script
else if (array_key_exists($action, sysSectionsGet()) && sysSectionGetType($action) == 'script' && function_exists($action)) {

    sysPrintHeader();
    call_user_func($action);
    sysPrintFooter();

// if function exists and user has rights to the root, call the function
} else if (function_exists($action) && isRequestActionAllowed($action)) {
    call_user_func($action);
} else {
    sysPrintHeader();
    sysPrintFooter();
}

// SMART
if (substr($action, 0, 7) == 'sensor-') {
    smartEchoSensor($action, escapeQuotes($_REQUEST['timespanType']));
}
if (substr($action, 0, 10) == 'viewswitch') {
    smartdisplaySwitches(substr($action, -2));
}
if (substr($action, 0, 7) == 'camview') {
    smartCameraDisplay(substr($action, 7));
}
if (substr($action, 0, 13) == 'camasynchview') {
    smartCameraDisplayAsynch(substr($action, 13));
}
if (substr($action, 0, 11) == 'camsavedvid') {
    smartCameraDisplaySavedVids(substr($action, 11));
}
if (substr($action, 0, 8) == 'camsaved') {
    displayCamSavedImages(substr($action, 8));
}
if (substr($action, 0, 8) == 'camsched') {
    camAutoSaving(substr($action, 8));
}
if (substr($action, 0, 14) == 'camasynchsched') {
    camAutoSavingAsynch(substr($action, 14));
}
if (substr($action, 0, 9) == 'camstation') {
    smartCameraCamMachine(substr($action, 9));
}
// SMART END

ob_end_flush();