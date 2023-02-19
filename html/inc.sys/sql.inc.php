<?php

function smart_mysql_query($sql)
{
    global $connId;
    return $connId->query($sql);
}

function sqlGetRow($sql)
{
    $result = smart_mysql_query($sql);
    //debug($sql);
    $row = $result->fetch_assoc();
    return $row;
}

function sysModuleGetNrOfRows($moduleId)
{
    $section = sysSectionGet($moduleId);
    $count = 0;
    $sql = '';
    if (is_array($section) && $section['type'] == 'abstract') {
        $sql = "select count(*) as count from (SELECT distinct row FROM sysModuleValue WHERE sysModuleId = '" . $section['id'] . "' GROUP BY row) as t;";
    } else if (is_array($section) && $section['type'] == 'dynamic') {
        $sql = "SELECT count(*) as count FROM " . $section['id'];
    }
    if (strlen($sql) > 0) {
        $result = smart_mysql_query($sql);
        $row = $result->fetch_assoc();
        $count = $row['count'] > 0 ? $row['count'] : 0;
    }

    return $count;
}

function sqlGetRows($sql)
{
    $return = array();

    $result = smart_mysql_query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getTempTypes()
{
    $tempTypes = array();
    $result = smart_mysql_query("SELECT * FROM smartenumsensor order by id asc");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tempTypes[$row['id']] = $row['name'];
        }
    }
    return $tempTypes;
}

function escapeFromDbToWeb($row)
{

    foreach ($row as $key => $entry) {

        $row[$key] = str_ireplace("%5C", "\\", $row[$key]);
        $row[$key] = str_ireplace("\\'", "'", $row[$key]);
    }
    return $row;
}

function escapeFromWebToDb($row)
{

    $newArr = array();
    foreach ($row as $key => $entry) {
        if (is_numeric(strpos($key, 'attr-'))) {

            $key = str_ireplace('attr-', '', $key);

            $newArr[$key] = $entry;

            //skipping this since base64 is not working for utf-8
            //$newArr[$key] = base64_decode(str_ireplace(' ','+',$newArr[$key]));

        }
    }
    return $newArr;
}

function tableExists($table)
{
    $result = smart_mysql_query("SHOW TABLES");
    while ($row = $result->fetch_assoc()) {
        if (array_shift($row) == $table) return true;
    }
    return false;
}


function sysModuleAttributesGet($section)
{
    $res = smart_mysql_query($sql = "SELECT a.name, a.sysModuleId, t.name as type, a.db, a.def, a.isChart, a.math, a.id FROM sysModuleAttribute a LEFT JOIN sysModuleAttributeType t on a.sysModuleAttributeTypeId = t.id WHERE a.sysModuleId = '" . $section . "' ORDER by `order` ASC");

    $attrs = array();
    while ($row = $res->fetch_assoc()) {
        $attrs[$row['db']] = $row;
    }
    return $attrs;
}

function sysModuleAttributeGet($id)
{
    $res = smart_mysql_query($sql = "SELECT a.name, a.sysModuleId, t.name as type, a.db, a.def, a.isChart, a.math, a.id FROM sysModuleAttribute a LEFT JOIN sysModuleAttributeType t on a.sysModuleAttributeTypeId = t.id WHERE a.id = '" . $id . "'");

    return $res->fetch_assoc();
}

function sysDbCleanup()
{

    if ($_REQUEST['key'] != 'yes') die;

    $attributes = sqlGetRows("SELECT id FROM `sysModuleAttribute` WHERE 1=1");

    $attributesStr = '';
    if (count($attributes) > 0) {
        foreach ($attributes as $attribute) {
            $attributesStr .= "'" . $attribute['id'] . "', ";
        }
        if (strlen($attributesStr) > 2) {
            $attributesStr = substr($attributesStr, 0, -2);

            $rows = sqlGetRows($sql = 'SELECT * FROM sysModuleValue WHERE `sysModuleAttributeId` not in (' . $attributesStr . ')');
            if (count($rows) > 0) {
                //debug($sql);
                //echo '<br /><br />';
                foreach ($rows as $row) {
                    debug($row);
                    echo '<br />';
                }
                echo '<br/><br/><br/><hr/><hr/><hr/>DELETE FROM sysModuleValue WHERE `sysModuleAttributeId` not in (' . $attributesStr . ')';
            } else {
                echo 'Nothing to cleanup.';
            }
        }
    }
}

function sysDbFullBackup($key = false)
{
    if (!((isset($_REQUEST['key']) && $_REQUEST['key'] == 'yes') || ($key == 'yes'))) die;

    $data = sysDbCompleteOneFileBackup('yes', true);

    $file = UPLOADS . '/sysBackup/full-' . date('Y-m-d_H-i-s') . '.sql';
    file_put_contents($file, $data);
    echo 'File: ' . $file . ' was saved with data size: ' . strlen($data) . '<br /> ';

}

function sysDbBackupAndPushToNexus($key = false)
{
    if (!((isset($_REQUEST['key']) && $_REQUEST['key'] == 'yes') || ($key == 'yes'))) die;

    sysDbFullBackup('yes');
    sysDbPushToNexus('yes');

}

function sysDbCompleteOneFileBackup($key = false, $return = false)
{
    global $connId;
    if (!((isset($_REQUEST['key']) && $_REQUEST['key'] == 'yes') || ($key == 'yes'))) die;

    $content = 'SET NAMES \'utf8\';';
    $queryTables = $connId->query('SHOW TABLES');

    while ($table = $queryTables->fetch_row()) {
        $table = $table[0];

        $res = $connId->query('SHOW CREATE TABLE ' . $table);
        $createTableStr = $res->fetch_row();
        $content .= "\n\n" . $createTableStr[1] . ";\n\n";

        //get all data form table
        $result = $connId->query('SELECT * FROM ' . $table);
        $fields_amount = $result->field_count;
        $rowNr = 1;
        while ($row = $result->fetch_row()) {

            if (($rowNr == 1) || (($rowNr % 500) == 0)) {
                $content .= "\nINSERT INTO " . $table . " VALUES \n(";
            } else {
                $content .= "\n(";
            }

            for ($j = 0; $j < $fields_amount; $j++) {
                $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                if (isset($row[$j])) {
                    $content .= '"' . $row[$j] . '"';
                } else {
                    $content .= '""';
                }
                if ($j < ($fields_amount - 1)) {
                    $content .= ',';
                }
            }

            if (($rowNr % 499) == 0) {
                $content .= ");";
            } else {
                $content .= "),";
            }
            $rowNr++;
        }
        $content = substr($content, 0, -1) . ';';
    }

    if ($return) return $content;

    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: Binary');
    header('Content-disposition: attachment; filename="full-' . date('Y-m-d_H-i-s') . '.sql"');
    echo $content;
    exit;

}

function sysDbPushToNexus($key = false)
{
    if (!((isset($_REQUEST['key']) && $_REQUEST['key'] == 'yes') || ($key == 'yes'))) die;

    $dir = UPLOADS . '/sysBackup/';
    $files = scandir($dir, SCANDIR_SORT_DESCENDING);
    $fileName = $files[0];

    if ($fileName == '..') {
        echo "No backup file.";
        return;
    }

    if (zipProtectFile($dir, $fileName, $_ENV['MYSQL_PASSWORD'])) {
        $userAndPassword = $_ENV['USER_NEXUS'] . ":" . $_ENV['PASS_NEXUS'];
        $nexusUrlCurrent = "http://sys-nexus:8081/nexus/repository/raw/" . $_ENV['PROJECT'] . "/backup-db/" . $fileName . ".zip";
        $nexusUrlLatest = "http://sys-nexus:8081/nexus/repository/raw/" . $_ENV['PROJECT'] . "/backup-db/latest.zip";
        $fileToUpload = $dir . $fileName;
        $fileToUploadZip = $fileToUpload . '.zip';

        //push current
        $curlReturn = callCurl($nexusUrlCurrent, null, $userAndPassword, $fileToUploadZip);
        logDebug(print_r($curlReturn, true));
        echo '<br />Adding: ' . $fileToUploadZip . ' into: ' . $nexusUrlCurrent;
        sleep(10);
        //push latest
        $curlReturn = callCurl($nexusUrlLatest, null, $userAndPassword, $fileToUploadZip);
        logDebug(print_r($curlReturn, true));
        echo '<br />Adding: ' . $fileToUploadZip . ' into: ' . $nexusUrlLatest;

        echo '<br />Removing file: ' . $fileToUpload;
        unlink($fileToUpload);
        echo '<br />Removing file: ' . $fileToUploadZip;
        unlink($fileToUploadZip);
    } else {
        echo "<br />Cannot ZIP file";
    }

}

function zipProtectFile($dir, $fileName, $password)
{
    $zip = new ZipArchive;
    $res = $zip->open($dir . '/' . $fileName . '.zip', ZipArchive::CREATE);
    if ($res === TRUE) {
        $zip->addFromString($fileName, file_get_contents($dir . '/' . $fileName));
        $zip->setEncryptionName($fileName, ZipArchive::EM_AES_256, $password);
        $zip->close();
        return true;
    } else {
        return false;
    }
}