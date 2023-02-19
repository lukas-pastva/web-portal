<?php


function sysSectionGetType($id){
    $row = sqlGetRow("SELECT type FROM sysSection WHERE id = '$id'");
    return $row['type'];
}

function sysSectionGetName($id){
    $result = smart_mysql_query($sql = "SELECT name FROM sysSection where id ='$id'");
    $name = '';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
    }
    return strlen($name)>0?$name:$id;
}

function sysSectionGet($id){
    $result = smart_mysql_query("SELECT * FROM sysSection where id ='$id'");
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
}

function getSectionParent($sectionName){
    $result = smart_mysql_query("SELECT p.id FROM sysSection p left join sysSection s on p.id=s.sysSectionId where s.id ='$sectionName'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        return sysSectionGet($row['id']);
    }
    return $sectionName;
}

function sysSectionsGet($parent = false)
{
    $menuItems = Array();
    $sql = "SELECT s.id as id, s.name as name, s.icon as icon 
                FROM sysSection s join sysUserSection us on s.id = us.sysSectionId 
                join sysUser u on u.id = us.sysUserid 
                where u.id = '" . escapeQuotes($_COOKIE['cookieUsername']) . "'
                " . ($parent ? 'and s.sysSectionId = "'.$parent.'"' : '') . "
                order by s.ordering";
    $result = smart_mysql_query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $menuItems[$row['id']] = $row;
        }
    }

    return $menuItems;
}
 
function isSubmenuSelected($id, $current){
    $result = smart_mysql_query($sql = "SELECT id FROM sysSection WHERE sysSectionId = '$id'");

    $subMenuItems = Array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($current == $row['id']) return true;
        }
    }
    return false;
}
