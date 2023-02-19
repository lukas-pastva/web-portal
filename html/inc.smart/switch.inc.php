<?php

function smartgetSwitchFilter(){
    $result = smart_mysql_query("SELECT * FROM smartenumswitch");
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<a href="?a=smartlogswitch&id='.$row['id'].'&timespanType='.$_REQUEST['timespanType'].'"'.($_REQUEST['id']==$row['id']?' class="bold"':'').'>'.$row['name'].'</a>&nbsp;|&nbsp;';
        }
    }
}

function smartUpdateSchedulable(){
    $id = escapeQuotes($_REQUEST['id']);
    $value = escapeQuotes($_REQUEST['value']);
    $schedule = escapeQuotes($_REQUEST['schedule']);
    $type = escapeQuotes($_REQUEST['type']);
    $table = escapeQuotes($_REQUEST['table']);

    if(strlen($id)==0)return;

    if(strlen($value)>0) smart_mysql_query('UPDATE '.$table.' set value = \''.$value.'\'  where id = \''.$id.'\'' );
    if(strlen($schedule)>0)smart_mysql_query($sql = 'UPDATE '.$table.' set schedule = \''.$schedule.'\' where id = \''.$id.'\'' );
    if(strlen($type)>0)  smart_mysql_query('UPDATE '.$table.' set type = \''.$type.'\' where id = \''.$id.'\'' );

}

function smartSchedulableScheduleRemove(){
    $table = escapeQuotes($_REQUEST['table']);
    smart_mysql_query('DELETE from '.$table.' WHERE id = "'.escapeQuotes($_REQUEST['id']).'"');
}

function smartSwitchSensorAdd(){
    $table = escapeQuotes($_REQUEST['table']);
    smart_mysql_query('UPDATE smartenumswitch SET treshold = "'.escapeQuotes($_REQUEST['treshold']).'", smartenumsensorid = "'.escapeQuotes($_REQUEST['enumsensorid']).'" where id = "'.escapeQuotes($_REQUEST['id']).'" ');
}

function smartSwitchEmailScheduleAdd(){
    $table = escapeQuotes($_REQUEST['table']);
    smart_mysql_query('UPDATE '.$table.' SET email = "'.escapeQuotes($_REQUEST['email']).'" where id = "'.escapeQuotes($_REQUEST['id']).'"');

}

function smartSchedulableSchedule(){
    $table = escapeQuotes($_REQUEST['table']);
    $stop = escapeQuotes($_REQUEST['stop']);
    smart_mysql_query('INSERT into '.$table.' ('.str_ireplace('schedule', '', $table).'id, start'.($stop?', stop':'').') VALUES ("'.escapeQuotes($_REQUEST['id']).'", "'.escapeQuotes($_REQUEST['start']).'"'.($stop?' ,"'.$stop.'"':'').') ' );
}

function smartdisplaySwitches($location)
{
    sysPrintBlockHeader('12', 'Switches');

	$locationId = ($location);
	$schedule['stop'] = 0;
	$schedule['5 min'] = 5;
	$schedule['30 min'] = 30;
	$schedule['1 hod'] = 60;
	$schedule['2 hod'] = 120;
	$schedule['4 hod'] = 240;
	$schedule['6 hod'] = 360;
	$schedule['12 hod'] = 720;
	echo '
	<script><!--
	$(function () {
		$(".start").click(function() {
			attr = "?a=smartUpdateSchedulable&table=enumswitch&id="+$(this).attr(\'id\').substring(6)+"&value="+$(this).val();
			ajax(attr);
		});		
		$(".schedule").change(function() {		
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&id="+$(this).attr(\'id\').substring(9)+"&schedule="+$(this).val();
			ajax(attr);   
		});		
		$(".add").click(function() {		
			var attr = "?a=smartsmartSchedulableSchedule&table=enumswitchschedule&id="+$(this).attr(\'id\')+"&start="+$(\'#start\'+$(this).attr(\'id\')).val()+($(\'#stop\'+$(this).attr(\'id\')).length?"&stop="+$(\'#stop\'+$(this).attr(\'id\')).val():"");
			ajax(attr);					
		});	
		$(".add-sensor").click(function() {		
			var attr = "?a=smartSwitchSensorAdd&table=enumswitch&treshold="+$(\'#treshold-\'+$(this).attr(\'id\')).val()+"&enumsensorid="+$(\'#enumsensorid-\'+$(this).attr(\'id\')).val()+"&id="+$(this).attr(\'id\');
			ajax(attr);
		});	
		$(".remove").click(function() {	
			var attr = "?a=smartsmartSchedulableScheduleRemove&table=enumswitchschedule&id="+$(this).attr(\'id\');
			ajax(attr);
		});		
		$(".manual").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&type=0&value=0&schedule=0&id="+$(this).attr(\'id\');
			ajax(attr);
		});	
		$(".sensor").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&type=3&value=0&schedule=0&id="+$(this).attr(\'id\');
			ajax(attr);
		});
		$(".sun").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&type=4&value=0&schedule=0&id="+$(this).attr(\'id\');
			ajax(attr);
		});
		$(".treshold-on").click(function() {
			var attr = "?a=smartsetSwitchTreshold&value=1&id="+$(this).attr(\'id\');
			ajax(attr);
		});
		$(".treshold-off").click(function() {
			var attr = "?a=smartsetSwitchTreshold&value=0&id="+$(this).attr(\'id\');
			ajax(attr);
		});
		$(".sun").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&type=4&value=0&schedule=0&id="+$(this).attr(\'id\');
			ajax(attr);
		});
		$(".timer").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&type=1&value=1&schedule=1&id="+$(this).attr(\'id\');
			ajax(attr);
		});		
		$(".auto").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumswitch&type=2&value=0&schedule&id="+$(this).attr(\'id\').substring(5);
			ajax(attr);
		});			
		$(".add-email").click(function() {		
			var id = $(this).attr(\'id\');
			var attr = "?a=smartSwitchEmailScheduleAdd&table=enumswitch&id="+id+"&email="+urlEncode($("#email"+id).val());
			ajax(attr);
		});	
		$(".expand").click(function() {
			$(this).parent().children(".hidden").slideToggle("fast");
			$(this).slideToggle("fast");
		});			
	});
	--></script>';

	$result = smart_mysql_query($sql = 'SELECT * from smartenumswitch where smartenumlocationid = "' . $locationId . '"');

	echo '
	<table id="switches" class="table table-bordered table-hover">
	 <tr>
	  <th>enumswitch</th>
	  <th>manualne</th>
	  <th>časovač</th>
	  <th>automaticky</th>
	  <th>podľa hodnoty senzora</th>
	  <th>podľa slnka</th> 
	 </tr>';
	if ($result->num_rows > 0) {
		while ($item = $result->fetch_assoc()) {
			echo '
			<tr>
			 <td title="id: ' . $item['id'] . ', sysUpdated: ' . $item['sysUpdated'] . '">
			 	' . $item['name'] . ($item['fullname'] ? ' / ' . $item['fullname'] : '') . ' 
			 	<span ' . ($item['value'] == 1 ? 'class="green" title="zapnuty"' : 'class="red" title="vypnuty"') . '></span>
			 </td>
			 <td ' . ($item['type'] == 0 ? 'class="active"' : '') . '>			  
			  <button ' . ($item['type'] == 0 ? 'disabled="disabled"' : '') . ' class="manual" id="' . $item['id'] . '">Aktivuj</button>
 		      <button ' . ($item['type'] != 0 ? 'class="hidden"' : '') . ' class="start" value="' . ($item['value'] == 0 ? '1' : '0') . '" id="start-' . $item['id'] . '">' . ($item['value'] == 0 ? 'Zapni' : 'Vypni') . '</button>
			 </td>
			 <td ' . ($item['type'] == 1 ? 'class="active"' : '') . '>
			  <button ' . ($item['type'] == 1 ? 'disabled="disabled"' : '') . ' class="timer" id="' . $item['id'] . '">Aktivuj</button>
			  <select ' . ($item['type'] != 1 ? 'disabled="disabled"' : '') . ' class="schedule custom-select" name="schedule" id="schedule-' . $item['id'] . '" >' . "\r\n";
			foreach ($schedule as $key => $scheduleItem) {
				echo '<option value="' . $scheduleItem . '" ' . ($scheduleItem == $item['schedule'] ? 'selected="selected"' : '') . '>' . $key . '</option>' . "\r\n";
			}

			$remaining = '00:00';
			if ($item['type'] == 1) {
				$remainingSql = smart_mysql_query('SELECT * FROM smartenumswitch where id = "' . $item['id'] . '"');
				$remainingRes = $remainingSql->fetch_assoc();

				$remaining = round(((strtotime($remainingRes['sysUpdated']) + ($remainingRes['schedule'] * 60)) - time()) / 60) . ' min';
			}
			echo '
			    </select>
			    ' . ($item['type'] == 1 ? '<br />zostáva:' . $remaining : '') . ' 
			   </div>
			 </td>
			 <td ' . ($item['type'] == 2 ? 'class="active"' : '') . '>
			  <button ' . ($item['type'] == 2 ? 'disabled="disabled"' : '') . ' class="auto" id="auto-' . $item['id'] . '">Aktivuj</button><br />';
			$itemschedule = smart_mysql_query('SELECT * FROM enumswitchschedule where enumswitchid = "' . $item['id'] . '"');
			if ($itemschedule->num_rows > 0) {
				while ($ssRow = $itemschedule->fetch_assoc()) {
					$isActtive = (strtotime($ssRow['start']) <= time() && strtotime($ssRow['stop']) >= time());
					echo ($isActtive ? '<b>' : '');
					echo 'Start: ' . $ssRow['start'] . ($ssRow['stop'] ? ', stop:' . $ssRow['stop'] : '') . '<button class="remove" id="' . $ssRow['id'] . '" class="small">sysDelete</button><br />';
					echo ($isActtive ? '</b>' : '');
				}
			}
			echo '<button class="expand">pridaj</button>
				  <div class="hidden">
				  start:<input type="text" name="start" size="4" id="start' . $item['id'] . '" value="20:00" /> 
				  stop:<input type="text" name="stop" size="4" id="stop' . $item['id'] . '" value="22:00" /> 
				  <input type="button" value="OK" class="add" id="' . $item['id'] . '" />
				  </div>
				 </div>				
			 </td>
			 ' . ($schedulePerHour ? '<td>Oddelené bodkočiarkou:<br /><input type="text" name="email" size="15" id="email' . $item['id'] . '" value="' . $item['email'] . '" /><input type="button" value="OK" class="add-email" id="' . $item['id'] . '" /></td>' : '');
			if (! $schedulePerHour) {
				echo '
				<td ' . ($item['type'] == 3 ? 'class="active"' : '') . '>
			 		<button ' . ($item['type'] == 3 ? 'disabled="disabled"' : '') . ' class="sensor" id="' . $item['id'] . '">Aktivuj</button><br />';

				if ($item['type'] == 3) {
					$sensor = smart_mysql_query($sql = 'SELECT * FROM enumsensor where enumlocationid="' . $locationId . '" order by name');
					if ($sensor->num_rows > 0) {
						echo 'Sensor: <select name="enumsensorid" id="enumsensorid-' . $item['id'] . '" class="custom-select"><option></option>';
						while ($sensorValue = $sensor->fetch_assoc()) {
							echo '<option value="' . $sensorValue['id'] . '" ' . ($item['enumsensorid'] == $sensorValue['id'] ? 'selected="selected"' : '') . '>' . $sensorValue['name'] . '</option>';
						}
						echo '</select><br />';
						echo 'treshold: <input type="text" value="' . $item['treshold'] . '" id="treshold-' . $item['id'] . '" class="smaller short" />';
						echo '<button class="add-sensor" id="' . $item['id'] . '">OK</button>';
					}
				} else {
					echo '<br />Sensor: ' . $item['enumsensorid'] . ', treshold: ' . $item['treshold'] . '<br />';
				}
			}
			echo '</td>
			<td ' . ($item['type'] == 4 ? 'class="active"' : '') . '>
			 <button ' . ($item['type'] == 4 ? 'disabled="disabled"' : '') . ' class="sun" id="' . $item['id'] . '">Aktivuj</button><br /><br />
			 <button class="treshold-on ' . ($item['type'] == 4 ? '' : ' hidden') . ($item['tresholdstate'] == 1 ? ' selected' : '') . '" id="' . $item['id'] . '">Zapni za svetla</button> 
			 <button class="treshold-off ' . ($item['type'] == 4 ? '' : ' hidden') . ($item['tresholdstate'] == 0 ? ' selected' : '') . '" id="' . $item['id'] . '">Zapni za tmy</button>
			</td>
		    </tr>';
		}
	}
	echo '</table>';
	echoTableAutomation('switches');
    sysPrintBlockFooter();
}

function smartsetSwitchTreshold()
{
	smart_mysql_query('sysUpdate smartenumswitch set tresholdstate = "' . escapeQuotes($_REQUEST['value']) . '" where id = "' . escapeQuotes($_REQUEST['id']) . '"');
}

function smartdisplaySwitchLog()
{
	$timestampFrom = '24';
	$timestampFromType = 'HOUR';
	$timestampFormat = 'M-d H:i';
	$timestampFormatSql = '%Y-%m-%d %k';
	$mod = 1;
	if ($_REQUEST['timespanType'] < 3) {
		$timestampFrom = ($_REQUEST['timespanType'] + 1) * 24;
		$timestampFromType = 'HOUR';
		$timestampFormat = 'M-d H:i';
		$timestampFormatSql = '%Y-%m-%d %k:%i';
		$mod = 1;
	}
	if ($_REQUEST['timespanType'] == 3) {
		$timestampFrom = 7 * 24;
		$timestampFromType = 'HOUR';
		$timestampFormat = 'M-d H:i';
		$timestampFormatSql = '%Y-%m-%d %k:%i';
		$mod = 1;
	}
	if ($_REQUEST['timespanType'] == 4) {
		$timestampFrom = '1';
		$timestampFromType = 'WEEK';
		$timestampFormat = 'Y-M-d';
		$timestampFormatSql = '%Y-%m-%d';
		$mod = 6;
	}
	if ($_REQUEST['timespanType'] == 5) {
		$timestampFrom = '1';
		$timestampFromType = 'MONTH';
		$timestampFormat = 'Y-M-d';
		$timestampFormatSql = '%Y-%m-%d';
		$mod = 120;
	}

/*
	$result = smart_mysql_query("SELECT * from smartenumswitch s left join smartlogswitch l on s.id = l.smartenumswitchid where l.timestamp > DATE_SUB(NOW(),INTERVAL " . $timestampFrom . " " . $timestampFromType . ") and s.id = " . $_REQUEST['id'] . " order by timestamp asc");

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$date = new DateTime($row["timestamp"]);
			$dateStr = $date->format($timestampFormat);
			echo "['" . $dateStr . "'," . $row["value"] . "],";
		}
	}
*/

}

function smartlogswitch(){
    $timespanTypes = Array(
        1 => '2 days',
        2 => '3 days',
        3 => '1 week',
        4 => '1 month',
        5 => '1 year'
    );

    smartgetSwitchFilter();
    smartdisplaySwitchLog();
    foreach($timespanTypes as $key => $timespanTypeItem){
        echo '<a href="?a=smartlogswitch&id='.$_REQUEST['id'].'&timespanType='.$key.'"'.($_REQUEST['timespanType']==$key?' class="bold"':'').'>'.$timespanTypeItem.'</a>&nbsp;|&nbsp;';
    }
}

function cronSwitches($row){
    // vynulujem schedule na vypinacoch
    $result = smart_mysql_query("SELECT *, UNIX_TIMESTAMP(sysUpdated) as epoch FROM enumswitch where schedule <> 0");
    while ($row = $result->fetch_assoc()) {
        if ((time() - $row['epoch']) > $row['schedule'] * 60) {
            smart_mysql_query("UPDATE smartenumswitch set value='0', schedule = '0', type = '0' where id='" . $row['id'] . "'");
        }
    }

    // ale este musim pozriet kazdy vypinac co ma naschedulovany start alebo stop ak je aktualna hotina tak to musim vykonat
    $rows = sqlGetRows("SELECT * from smartenumswitch s join smartenumswitchschedule l on s.id = l.enumswitchid where type = 2");
    $switchesStarted = Array();
    foreach ($rows as $row) {
        if (strtotime($row['start']) <= time() && strtotime($row['stop']) >= time()) {
            // zapnem switch ak ma cas na schedule
            // ale pozor nezapnem ho ak ma last sysUpdated
            smart_mysql_query("UPDATE smartenumswitch set value='1', schedule = '0' where id='" . $row['smartenumswitchid'] . "' and isReset = 0 ");
            smart_mysql_query("UPDATE smartenumswitch set value='1', schedule = '0' where id='" . $row['smartenumswitchid'] . "' and isReset = 1 and (UNIX_TIMESTAMP(now()) - UNIX_TIMESTAMP(sysUpdated))>600 ");
            $switchesStarted[$row['enumswitchid']] = true;
        } else {
            if (! $switchesStarted[$row['smartenumswitchid']]) {
                smart_mysql_query("UPDATE smartenumswitch set value='0', schedule = '0' where id='" . $row['smartenumswitchid'] . "'");
            }
        }
    }
}

function cronSwitchType1($row){
    // sem budem nastavovat hodnoty vypinacov v pripade vypinaca 3, teda podla hodnoty ineho senzora
    $rows = sqlGetRows("SELECT * from smartenumswitch where type = 3");
    foreach ($rows as $row) {
        // get switches that have type 3
        // fory these switches i do: get switch id and compare schedule and sysUpdate then switch!
        $switchValue = null;
        $resultSensor = smart_mysql_query('SELECT val from smartsensordata where smartenumsensorid = "' . $row['smartenumsensorid'] . '" order by timestamp desc limit 1,1');
        $rowSensor = $resultSensor->fetch_assoc();
        // get value from sensor latest
        // $eval = 'return '.$rowSensor['val'].getShiftForSensor($row['enumsensorid']).';';
        $val = eval(getShiftForSensor($row['smartenumsensorid'], $rowSensor['val']));
        if ($val > $row['treshold']) {
            $switchValue = 1;
        } else {
            $switchValue = 0;
        }

        smart_mysql_query("UPDATE enumswitch set value='" . $switchValue . "' where id='" . $row['id'] . "'");
    }
}

function cronSwitchType3($row){

    // sem budem nastavovat hodnoty vypinacovv pripade vypinaca 3, teda podla hodnoty ineho senzora
    $switches = sqlGetRows("SELECT l.sunrise, l.sunset, s.tresholdstate, s.id from smartenumswitch s left join smartenumlocation l on l.name = s.location where s.type = 4");
    foreach ($switches as $switch) {
        $isDay = isDay($switch['sunrise'], $switch['sunset']);
        // echo '$isDay:'.$isDay.' $switch[tresholdstate]:'.$switch['tresholdstate'];
        $switchValue;
        if ((($switch['tresholdstate'] == 1) && $isDay) || (($switch['tresholdstate'] == 0) && ! $isDay)) {
            $switchValue = 1;
        } else if ((($switch['tresholdstate'] == 1) && ! $isDay) || (($switch['tresholdstate'] == 0) && $isDay)) {
            $switchValue = 0;
        }

        smart_mysql_query("UPDATE smartenumswitch set value='" . $switchValue . "' where id='" . $switch['id'] . "'");
    }

}
