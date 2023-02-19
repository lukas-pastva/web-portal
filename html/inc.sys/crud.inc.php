<?php

function sysInsertGo()
{
    $date = null;
    if (isset($_REQUEST['timestamp']) && $_REQUEST['timestamp'] == 'current') {
        $_REQUEST['timestamp'] = date('Y-m-d_H-i-s', time());
        $date = $_REQUEST['timestamp'];
    } elseif (isset($_REQUEST['timestamp'])) {
        $date = date('Y-m-d_H-i-s', strtotime($_REQUEST['timestamp']));
    }
    if (isset($_FILES["file"])) {
        $filename = $date . '-' . $_FILES["file"]["name"];
    }
    if (count($_FILES) > 0 && ($_FILES["file"]["size"] < 1024 * 1024 * 10)) {
        $fullFilename = UPLOADS . '/' . escapeQuotes($_REQUEST['subdir']) . '/' . $filename;
        logDebug('Saving file:' . move_uploaded_file($_FILES["file"]["tmp_name"], $fullFilename));
    }
    if (isset($_REQUEST['fileUrl']) && strlen($_REQUEST['fileUrl']) > 0) {
        $filename = $date . '-restore.zip';
        $fullFilename = UPLOADS . '/' . escapeQuotes($_REQUEST['subdir']) . '/' . $filename;
        logDebug('Saving file: ' . $filename . ' and size: ' . file_put_contents($fullFilename, base64_decode($_REQUEST['fileUrl'])));
    }

    $row = escapeFromWebToDb($_REQUEST);

    //if id does not exist
    $id = getUniqueId();
    if (!array_key_exists('id', $row) || strlen($row['id']) < 1) {
        $row['id'] = $id;
    }

    $t = escapeQuotes($_REQUEST['origin']);
    $sql = "INSERT into `$t` (";

    foreach ($row as $key => $value) {
        if ($key == 'fileUrl') {
            $sql .= '`file`,';
        } else {
            $sql .= "`$key`,";
        }
    }
    $sql = substr($sql, 0, -1);
    $sql .= ') values (';

    foreach ($row as $key => $value) {
        if ($key == 'file' || $key == 'fileUrl') {
            $sql .= "'$filename',";
        } else if ($key == 'password') {
            $sql .= "'" . openssl_digest($value, 'sha512') . "',";
        } else {
            $sql .= "'$value',";
        }
    }

    $sql = substr($sql, 0, -1) . ')';
    logDebug('sysInsertGo: ' . $sql);
    smart_mysql_query($sql);
    echo $id;
}

function sysUpdateGo()
{
    $row = escapeFromWebToDb($_REQUEST);
    $id = strlen($_REQUEST['uid']) > 0 ? $_REQUEST['uid'] : $row['id'];
    $t = escapeQuotes($_REQUEST['origin']);
    $sql = '
	UPDATE ' . $t . ' SET';

    foreach ($row as $key => $value) {

        $value = str_ireplace('\\', '\\\\', $value);
        $value = str_ireplace('"', '\\"', $value);

        $sql .= '
		`' . $key . '`="' . $value . '",';
    }
    $sql = substr($sql, 0, -1);
    $sql .= '
	 WHERE id = "' . $id . '" ';
    //debug($sql);
    logDebug('sysUpdateGo: ' . $sql);
    smart_mysql_query($sql);
    echo $id;
}

function sysDeleteGo()
{

    smart_mysql_query($sql = "DELETE from " . escapeQuotes($_REQUEST['origin']) . " WHERE id = '" . escapeQuotes($_REQUEST['uid']) . "' ");

    //debug($sql);
    logDebug('sysDeleteGo: ' . $sql);
}

function abstractInsert($module, $attributes, $values)
{
    $row = getUniqueId();
    foreach ($attributes as $key => $attribute) {
        smart_mysql_query($sql = 'INSERT INTO `sysModuleValue` (`sysModuleId`, `sysModuleAttributeId`, `value`, `row`) VALUES ("' . $module . '", "' . $module . '-' . $attribute . '", "' . $values[$key] . '", "' . $row . '") ');
    }
}

function abstractDelete($module, $attribute, $value)
{
    smart_mysql_query($sql = 'DELETE FROM `sysModuleValue` WHERE `sysModuleId` = "' . $module . '" AND `' . $attribute . '` = "' . $value . '"');
}

function sysInsertGoAbstract($row, $section, $attrs)
{
    /*$date = null;
    if ($_REQUEST['attr-timestamp'] == 'current') {
        $_REQUEST['attr-timestamp'] = date('Y-m-d_H-i-s', time());
        $date = $_REQUEST['attr-timestamp'];
    } else {
        $date = date('Y-m-d_H-i-s', strtotime($_REQUEST['attr-timestamp']));
    }
    $filename = $date . '-' . $_FILES["file"]["name"];
    if (count($_FILES) > 0 && ($_FILES["file"]["size"] < 1024 * 1024 * 10)) {
        $fullFilename = UPLOADS . '/' . escapeQuotes($_REQUEST['attr-subdir']) . '/' . $filename;
        logDebug('Saving file:' . move_uploaded_file($_FILES["file"]["tmp_name"], $fullFilename));
    }
    if (strlen($_REQUEST['fileUrl']) > 0) {
        $filename = $date . '-restore.zip';
        $fullFilename = UPLOADS . '/' . escapeQuotes($_REQUEST['subdir']) . '/' . $filename;
        logDebug('Saving file: ' . $filename . ' and size: ' . file_put_contents($fullFilename, base64_decode($_REQUEST['fileUrl'])));
    }*/

    $rowId = getUniqueId();
    //now insert multiple lines per each
    foreach ($row as $key => $item) {
        if (strlen($item) > 0 && $key != 'id') {
            $value = $item;
            if ($attrs[$key]['type'] == 'openssl') {
                $value = openssl_encrypt($value, "AES-128-ECB", base64_decode($_SESSION['module-' . $section['id']]));
            }
            $sql = "INSERT INTO `sysModuleValue` (`sysModuleAttributeId`, `sysModuleId`, `value`, `row`) VALUES ('" . $section['id'] . "-" . $key . "', '" . $section['id'] . "', '" . $value . "', '" . $rowId . "');";
            //debug($sql);
            smart_mysql_query($sql);
        }
    }

    logDebug('sysInsertAbstract: ' . $rowId . ' / ' . debug($row, true));
    return $rowId;
}

function sysUpdateGoAbstract($row, $section, $attrs, $id)
{
    sysDeleteGoAbstract($id, $section);
    $newId = sysInsertGoAbstract($row, $section, $attrs);

    //edit also all related entries
    $sql = "UPDATE `sysModuleValue` SET value = '$newId' WHERE value = '$id' AND sysModuleAttributeId like '%-" . $section['id'] . "Id'";
    smart_mysql_query($sql);
    logDebug('sysUpdateGoAbstract: ' . $newId . ' / ' . debug($row, true));
    echo $newId;
}

function sysDeleteGoAbstract($id, $section)
{

    smart_mysql_query($sql = "DELETE from sysModuleValue WHERE sysModuleId = '" . $section['id'] . "' AND row = '$id' ");

    //echo $sql;
    logDebug('sysDeleteAbstract: ' . $sql);
}

function sysInsertIntoAbstractTable()
{

    $data = '';

    $attrs = array('');

    $module = '';

    $password = 'base64';

    $dataArr = explode("\n", $data);
    $attrsDb = sysModuleAttributesGet($module);
    $insertData = true;


    foreach ($attrs as $key => $attr) {
        $i = 0;
        $keyFount = false;
        foreach ($attrsDb as $key => $attrDb) {
            if ($key == $attr) {
                $keyFount = true;
            } elseif (($i + 1) == count($attrsDb) && !$keyFount) {
                echo 'Cannot find attribute: ' . $attr . "\r";
                $insertData = false;
            }
            $i++;
        }
    }

    if ($insertData) {

        echo 'Inserting data' . "\n\n";

        //line per line
        $count = 0;
        foreach ($dataArr as $dataItem) {

            //since abstract, then value per value
            $lineArr = explode(',', $dataItem);
            $id = getUniqueId();
            foreach ($attrs as $key => $lineAttr) {

                //remove quotes
                $valueToInsert = str_ireplace('"', '', $lineArr[$key]);
                $attrDb = $attrsDb[$lineAttr];

                if ($attrDb['type'] == 'openssl') {
                    $valueToInsert = openssl_encrypt($valueToInsert, "AES-128-ECB", base64_decode($password));
                }
                smart_mysql_query($sql = 'INSERT INTO `sysModuleValue` (`sysModuleAttributeId`, `sysModuleId`, `value`, `row`) VALUES ("' . $attrDb['id'] . '", "' . $module . '", "' . $valueToInsert . '","' . $id . '")');
                $count++;

            }

        }

        echo 'DONE. Inserted ' . $count . ' lines';

    }


}
