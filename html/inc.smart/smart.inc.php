<?php


function smartCalendarView(){
    sysPrintBlockHeader(6, 'Calendar');

    $result = smart_mysql_query("SELECT * FROM smartNamesday where  DATE(`timestamp`) = DATE_ADD(CURDATE(), INTERVAL -1 YEAR )");
    $row = $result->fetch_assoc();
    echo '<br />Today is: <b>' . date('l, d. F Y') . '</b>'.((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)=='sk')?',  names day for Slovakia: <b>' . $row['name'] . '</b>':'').'';

    sysPrintBlockFooter();
}

function smartPostTrackingView()
{
    sysPrintBlockHeader(6, 'Post tracking');
    echo '
	<script><!--
		$(function () {
			$("#PostInsert").click(function() {
				var name = $("#smartPostTrackingNewName").val();
				var description = $("#smartPostTrackingNewDescr").val(); 
				var attr = "?a=smartPostTrackingInsert&attr-name="+name+"&attr-description="+description;
				ajax(attr);
			});	
			$(".PostDelete").click(function() {
				var id = $(this).attr("id");
				var attr = "?a=smartPostTrackingDelete&id="+id;
				ajax(attr, true);
			});	
		});		
		--></script>
	<table id="example2" class="table table-bordered table-hover"> 
	<tr><td>Nr: <input id="smartPostTrackingNewName" size="8"/>&nbsp;&nbsp;&nbsp; Name: <input id="smartPostTrackingNewDescr" size="12"/>&nbsp;&nbsp;<button class="btn btn-secondary" id="PostInsert">new</button></td></tr>';

    $items = sysModuleGetRows(true, 'smartPostTracking');
    foreach ($items as $item) {
        $url = 'https://api.posta.sk/private/search?q=' . $item['name'] . '&m=tnt';
        $data = getUrlContent($url);
        $data = explode('"en":"', $data);

        echo '<tr><td><button class=" btn btn-secondary btn-xs PostDelete" id="' . $item['id'] . '">X</button> <span class="smallest">' . $item['name'] . '</span> / <a href="https://tandt.posta.sk/zasielky/' . $item['name'] . '" target="_blank">' . $item['description'] . '</a> / ';
        $first = true;
        foreach ($data as $dataItem) {
            if ($first) {
                $first = false;
                continue;
            }
            echo substr($dataItem, 0, strpos($dataItem, '"', 0)) . ' | ';
        }

        echo '</td></tr>';
    }
    echo '</table>';
    sysPrintBlockFooter();
}

function smartPostTrackingInsert()
{
    $name = escapeQuotes($_REQUEST['attr-name']);
    $description = escapeQuotes($_REQUEST['attr-description']);
    if(strlen($name)>0 && strlen($description)>0) {
        abstractInsert('smartPostTracking', Array('name', 'description'), Array($name, $description));
    }
}

function smartPostTrackingDelete()
{
    abstractDelete('smartPostTracking', 'row', escapeQuotes($_REQUEST['id']));
}

function smartDodko(){
    sysPrintBlockHeader(6, 'Together!');

    $start = strtotime('2019-08-27 11:31:00');
    $diff = time()-$start;
    $years = floor($diff/60/60/24/365);
    $days = floor(($diff-($years*365*24*60*60))/60/60/24);
    $hours = floor(($diff-($years*365*24*60*60)-($days*60*60*24))/60/60);
    $minutes = floor(($diff-($years*365*24*60*60)-($days*60*60*24)-($hours*60*60))/60);
    $seconds = floor(($diff-($years*365*24*60*60)-($days*60*60*24)-($hours*60*60)-($minutes*60)));

    echo ($years). ' year, ';
    echo ($days).' days, ';
    echo ($hours).' hours, ';
    echo ($minutes).' minutes, ';
    echo ($seconds).' seconds';

    echo '.';

    sysPrintBlockFooter();
}

function sysCelebrationCheck(){
    $oldValue = sysModuleGetRows(true, 'sysCelebration', $_REQUEST['row'] );
    $oldValue = array_pop($oldValue);
    $timestamp = strtotime($oldValue['timestamp'])+365*24*60*60;

    smart_mysql_query($sql = 'UPDATE sysModuleValue set value =  "'.date('Y-m-d H:i:s', $timestamp).'"
    where sysModuleAttributeId = "sysCelebration-timestamp" and row = "'.escapeQuotes($_REQUEST['row']).'" ');
    //debug($sql);
    header("Location: ./?a=sysHome");
    exit();
}

function getIsDayByLocation($location, $timestamp = null){

    $locationData = sqlGetRow('SELECT * FROM enumlocation where name ="'.$location.'"');

    return isDay($locationData['sunrise'], $locationData['sunset'], $timestamp)?1:0;
}

function isDay($sunrise, $sunset, $timestamp = null){
    if($timestamp == null){
        $timestamp = time();
    }else{
        $timestampNew = strtotime(date('Y-m-d').' '.date('H:i:s', $timestamp));
        $timestamp = $timestampNew;
    }

    return (($timestamp > strtotime($sunrise)) && ($timestamp < strtotime($sunset)));
}

function cronStationsOfflineTime(){
    $stations = sqlGetRows('SELECT *, period_diff(now(), sysUpdated) as period from smartenumstation where period_diff(now(), sysUpdated) > 3600 and status = 1 order by location asc');
    foreach ($stations as $station) {
        logDebug('Station ' . $station['location'] . '/' . $station['name'] . ' is offline ' . round($station['period'] / 60 / 60/24) . ' days', 1);
    }
}


function syssmartinsertAsynchCall()
{
    $type = escapeQuotes($_REQUEST['type']);
    $value = escapeQuotes($_REQUEST['id']);
    $go = true;
    if ($onlyIfNotExist) {
        $count = sqlGetRow("SELECT count(*) as count FROM sysAsynchCall WHERE type = '" . $type . "' and value='" . $value . "' and active = 1");
        if ($count['count'] != '0') {
            // $go = false;
        }
    }

    if ($go) {
        smart_mysql_query("INSERT INTO sysAsynchCall (type, value) VALUES ('" . $type . "', '" . $value . "')");
    }

    $newId = sqlGetRow('SELECT id from sysAsynchCall order by id desc limit 0,1');

    echo $newId['id'] . ' ';
}
