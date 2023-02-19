<?php

function getShiftForSensor($id, $value, $shift = null)
{
    if ($shift == null) {
        $row = sqlGetRow("SELECT shift FROM smartenumsensor where id = '" . $id . "'");
        $shift = $row['shift'];
    }

    $return = 'return ' . sprintf($shift, $value) . ';';
    return $return;
}

function smartEchoSensor($action, $timespanType){
    $timespanTypes = array(
        1 => '2 days',
        2 => '3 days',
        3 => '1 week',
        4 => '1 month',
        5 => '1 year'
    );

    echo getCurrentValue(escapeQuotes(substr($action, 7))) . '<br />';

    sysDisplayChart(substr($action, 7), $timespanType);

    foreach ($timespanTypes as $key => $timespanTypeItem) {
        echo '<a href="?a=' . $action . '&timespanType=' . $key . '"' . ($span == $key ? ' class="bold"' : '') . '>' . $timespanTypeItem . '</a>&nbsp;|&nbsp;';
    }
}

function getShiftForSensorByName($name, $value)
{
    $row = sqlGetRow("SELECT shift FROM smartenumsensor where name = '" . $name . "'");
    $shift = $row['shift'];

    return 'return ' . sprintf($shift, $value) . ';';
}

function getCurrentValue($id, $shift = null)
{
    $result = smart_mysql_query("SELECT * FROM smartsensordata WHERE enumsensorid = '" . $id . "' order by timestamp desc LIMIT 1");
    $row = $result->fetch_assoc();
    if ($shift == null) {
        $val = eval(getShiftForSensor($id, $row['val']));
    } else {
        $val = eval(getShiftForSensor($id, getKey($row,'val'), $shift));
    }

    $isActual = false;
    $nowMinusHour = strtotime(date('Y-m-d H') . ':00:00', time()) - 3600;
    if (($nowMinusHour - strtotime(getKey($row,"timestamp"))) < 0) {
        $isActual = true;
    }

    return '<span ' . ($isActual ? 'class="bold"' : '') . ' title="' . getKey($row,'timestamp') . '" >' . ($val . ' ' . getKey($row, 'unit')) . '</span>';
}

function getMaxValueSql($timestampFrom, $timestampFromType, $type)
{
    return sqlGetRow("SELECT max(val) as val, timestamp FROM smartsensordata th left join smartenumsensor s on s.id = smartenumsensorid where timestamp > DATE_SUB(NOW(),INTERVAL " . $timestampFrom . " " . $timestampFromType . ") and s.name = '" . $type . "'");
}

function getMinValueSql($timestampFrom, $timestampFromType, $type)
{
    $row = sqlGetRow($sql = "SELECT min(val) as val, timestamp FROM smartsensordata th left join smartenumsensor s on s.id = smartenumsensorid where timestamp > DATE_SUB(NOW(),INTERVAL " . $timestampFrom . " " . $timestampFromType . ") and s.name = '" . $type . "'");
    return $row['val'];
}

function getMaxMinValue($timestampFrom, $timestampFromType, $type, $max = false)
{
    $minMax = $max ? getMaxValueSql($timestampFrom, $timestampFromType, $type) : getMinValueSql($timestampFrom, $timestampFromType, $type);

    $val = eval(getShiftForSensorByName($type, $minMax));
    return '<b>' . ($max ? 'max' : 'min') . ':</b>' . $val;
}
