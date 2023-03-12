<?php

function sysLogAccessGo($user)
{

    $sql = 'insert into sysLogAccess 
    (ip, action, server, request, user) 
    values (
            "' . $_SERVER['REMOTE_ADDR'] . '", 
            "' . $_REQUEST['a'] . '", 
            "' . escapeQuotes(print_r($_SERVER, true)) . '", 
            "' . escapeQuotes(print_r($_REQUEST, true)) . '", 
            "' . escapeQuotes($user) . '")';

    smart_mysql_query($sql);
}

function escapeQuotes($val)
{
    $val = str_ireplace("'", "&apos;", substr($val, 0, 4096));
    $val = str_ireplace('"', '&quot;', $val);
    //$val = str_ireplace('`', '&apos;', $val);-
    return $val;
}

function logDebug($text, $priority = 4)
{
    global $connId;

    $debugLevel = getSysConfig('DEBUG_LEVEL');

    if (strpos($debugLevel, 'JS') > 0) {
        echo '<script><!--
                $(window).load(function() {
                    logConsole("', debug(escapeQuotes($text)), '");
                });
            --></script>';
    }

    if (strpos($debugLevel, 'DB') > 0) {
        $connId->query($sql = 'INSERT into sysLogDebug (text, priority) VALUES ("' . escapeQuotes($text) . '", "' . $priority . '")');
    }
}

function debug($var = null, $return = false)
{
    if (!$return) {
        echo '
        xxx:
        ';
    }
    print_r($var, $return);
}

function getSysConfig($name)
{
    $result = smart_mysql_query('SELECT * FROM sysConfig where name = "' . $name . '"');
    $row = $result->fetch_assoc();
    return $row['text'];
}


function sysManageData()
{
    echoBlockHeader();
    echo '<a class="btn btn-secondary" href="?a=sysDbCleanup&key=yes" target="_blank">DB cleanup </a><br /><br />';
    echo '<a class="btn btn-secondary" href="?a=sysDbCompleteOneFileBackup&key=yes" target="_blank">Download backup</a><br /><br />';
    echo '<a class="btn btn-secondary" href="?a=sysDbBackupAndPushToNexus&key=yes" target="_blank">Backup to Nexus</a><br /><br />';

    echoBlockFooter();

    echoBlockHeader();
    echo '<table id="table-dynamic" class="table table-bordered table-hover"><thead><tr><th>Type</th><th>Count</th><th>Action</th></tr></thead><tbody>';
    foreach (sqlGetRows("SELECT * from sysModule") as $table) {
        if (tableExists($table['id'])) {
            echo '<tr>
                <td>' . $table['id'] . '</td>
                <td>' . sysModuleGetNrOfRows($table['id']) . '</td>
                <td>
                    <a class="btn btn-secondary export-json btn-xs" id="' . $table['id'] . '" style="margin-right: 5px" target="_blank" href="?a=sysManageDataExportJson&id=' . $table['id'] . '">Export</a>
                    <a class="btn btn-secondary export-sql btn-xs" id="' . $table['id'] . '" style="margin-right: 5px" target="_blank" href="?a=sysManageDataExportSql&id=' . $table['id'] . '">SQL</a>
                    <a class="btn btn-secondary truncate btn-xs" id="' . $table['id'] . '" target="_blank" href="?a=sysManageDataTruncateConfirm&id=' . $table['id'] . '">Truncate</a>
                </td>
               </tr>';
        }
    }
    echo '</tbody></table>';
    echoTableAutomation('table-dynamic');
    echoBlockFooter();

    $tables = sqlGetRows("SELECT * from sysModule");
    echoBlockHeader();
    echo '<table id="table-abstract" class="table table-bordered table-hover"><thead><tr><th>Type</th><th>Count</th><th>Action</th></tr></thead><tbody>';
    foreach ($tables as $table) {
        if (!tableExists($table['id'])) {
            echo '<tr>
                <td>' . $table['id'] . '</td>
                <td>' . sysModuleGetNrOfRows($table['id']) . '</td>
                <td>
                    <!-- <a class="btn btn-secondary export-json btn-xs" id="' . $table['id'] . '" style="margin-right: 5px" target="_blank" href="?a=sysManageDataExportJson&id=' . $table['id'] . '">Export</a> -->
                    <!-- <a class="btn btn-secondary export-sql btn-xs" id="' . $table['id'] . '" style="margin-right: 5px" target="_blank" href="?a=sysManageDataExportSql&id=' . $table['id'] . '">SQL</a> -->
                    <a class="btn btn-secondary truncate btn-xs" id="' . $table['id'] . '" target="_blank" href="?a=sysManageDataTruncateConfirm&id=' . $table['id'] . '">Truncate</a>
                </td>
               </tr>';
        }
    }
    echo '</tbody></table>';
    echoTableAutomation('table-abstract');
    echoBlockFooter();


    /*echo '<tr><th colspan="3">Sensors</th></tr>';
    $result = smart_mysql_query("SELECT * FROM smartenumsensor order by name asc");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $resultCnt = smart_mysql_query("SELECT count(*) as cnt FROM smartsensordata where smartenumsensorid = '" . $row['id'] . "' ");
            $rowCnt = $resultCnt->fetch_assoc();
            echo '<tr><td>' . $row['name'] . '</td><td>' . $rowCnt['cnt'] . '</td><td><button class="btn btn-secondary remove" id="remove-' . $row['id'] . '" >Truncate</button></td></tr>';
        }
    }*/

    sysPrintBlockFooter();
}

function curl_get_contents($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function getforeach($text, $min, $max)
{
    $return = '';
    for ($i = $min; $i <= $max; $i++) {
        $return .= str_ireplace('1', $i, $text);
    }
    return $return;
}

function sysManageDataTruncateConfirm()
{
    echo '<a href="?a=sysManageDataTruncate&id=' . $_REQUEST['id'] . '">Click here to confirm</a>';
}

function sysManageDataTruncate()
{
    $moduleId = escapeQuotes($_REQUEST['id']);
    $section = sysSectionGet($moduleId);
    if ($section['type'] == 'abstract') {
        sqlGetRows($sql = 'DELETE FROM sysModuleValue where sysModuleId = "' . $section['id'] . '"');
    } else if ($section['type'] == 'dynamic') {
        sqlGetRows($sql = 'DELETE FROM ' . $section['id'] . ' WHERE 1=1');
    }
    echo 'Data truncated.';
}

function sysManageDataExportJson()
{
    $table = escapeQuotes($_REQUEST['id']);
    $rows = sqlGetRows("Select * from $table");

    $data = '';
    $data .= 'data-' . $table . ':{' . "\r\n";
    foreach ($rows as $row) {
        $data .= '{';
        foreach ($row as $attr => $value) {
            $data .= '\'' . str_ireplace('\'', '"', $value) . '\',';
        }
        $data .= '}' . "\r\n";
    }
    $data .= '}';

    echo $data;
}

function sysManageDataExportSql()
{
    $table = escapeQuotes($_REQUEST['id']);
    $rows = sqlGetRows("Select * from $table");

    $data = '';

    foreach ($rows as $key => $row) {
        $rowNr = getUniqueId();
        if (count($row) > 0) {
            foreach ($row as $keyItem => $rowItem) {
                if ($keyItem == 'id') continue;
                $data .= "\r\n INSERT INTO `sysModuleValue` (`sysModuleAttributeId`, `sysModuleId`, `value`, `row`) VALUES ('$table-$keyItem', '$table', '$rowItem', '$rowNr');";
            }
        }
    }
    echo $data;
}

function getKey($array, $key)
{
    if ((is_array($array)) && (array_key_exists($key, $array))) {
        return $array[$key];
    } else {
        return '';
    }
}

function isRequestActionAllowed($action)
{

    if ($action == 'sysHome' || $action == 'logout') {
        return sysCanUserRead('sysHome');
    }

    $sysSections = sysSectionsGet();

    $sysSectionToCheck = '';
    foreach ($sysSections as $key => $sysSection) {
        //search within all sections the longest string in section containing pet action
        if ($key == substr($action, 0, strlen($key)) && strlen($sysSectionToCheck) < strlen($key)) {
            $sysSectionToCheck = $key;
        }
    }

    return sysCanUserRead($sysSectionToCheck);
}

function getUniqueId()
{
    return str_ireplace('.', '', microtime(true)) . rand(1000, 9999);
}

function sysCelebrationView()
{
    sysPrintBlockHeader(6, 'Celebrations');

    $rows = sysModuleGetRows(true, 'sysCelebration');
    $rows = sysModuleOrderBy($rows, 'timestamp', false);
    $rows = abstractTop($rows, 5);

    echo '<table id="example2" class="table table-bordered table-hover list Celebrations">';


    foreach ($rows as $row) {
        echo '<tr>
                <td>' . $row['name'] . '</td>
                <td>' . $row['timestamp'] . '</td>
                <td><a class="btn btn-secondary dismiss" href="?a=sysCelebrationCheck&row=' . $row['id'] . '">Dismiss</a></td>
              </tr>';
    }
    echo '</table>';

    sysPrintBlockFooter();
}

function removeNewlines($val)
{
    return str_replace("\r\n", "", $val);
}

function underScoreToCamelCase($val)
{
    $val = strpos($val, 'id_') === 0 ? substr($val, 3) . 'Id' : $val;
    $valArr = explode("_", $val);
    if(count($valArr)>1) {
        $val = "";
        foreach ($valArr as $item) {
            $val .= ucfirst($item);
        }
        $val = lcfirst($val);
    }
    return $val;
}