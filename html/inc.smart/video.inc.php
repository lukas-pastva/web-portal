<?php

function syssmartdeleteVideo()
{
    $file = PHOTODIR . '/vid/' . escapeQuotes($_REQUEST['location']) . '/' . escapeQuotes($_REQUEST['filename']);
    unlink($file);
}

function syssmartdeletePhoto()
{
    smart_mysql_query("DELETE from photo WHERE id = '" . ($_REQUEST['id']) . "'");

    $file = UPLOADS . '/cam/' . escapeQuotes($_REQUEST['location']) . '/' . escapeQuotes($_REQUEST['filename']);
    echo 'unlink: ' . $file;
    echo unlink($file);
}

function syssmartdeleteAllPhotoForCamera()
{
    $cam = sqlGetRow('SELECT * FROM smartEnumCam where id = "' . escapeQuotes($_REQUEST['id']) . '"');
    smart_mysql_query('DELETE from photo WHERE smartEnumCamid = "' . $cam['name'] . '" and location = "' . $cam['location'] . '"');

    // open folder and sysDelete
    $dir = UPLOADS . '/cam/' . $cam['location'];
    $files = scandir($dir);
    for ($i = 0; $i < count($files); $i ++) {
        $file = $dir . '/' . $files[$i];
        if (is_file($file)) {
            if (strpos($files[$i], $cam['location'] . '-camera-' . $cam['name'] . '-') === 0) {
                echo 'cam name:' . $cam['name'] . '  ' . $files[$i] . strpos($files[$i], $cam['location'] . '-camera-' . $cam['name'] . '-') . '<br />';
                unlink($file);
                unset($file);
                // echo 'sysDelete';
            }
        }
    }
}

function smarttruncateAllPhotoForCamera()
{
    $cam = sqlGetRow('SELECT * FROM smartEnumCam where id = "' . escapeQuotes($_REQUEST['id']) . '"');
    smart_mysql_query('DELETE from smartphoto WHERE esmartnumcamid = "' . $cam['name'] . '" and location = "' . $cam['location'] . '"');
}

function smartconfirmAllPhotoForCamera()
{
    $cam = sqlGetRow('SELECT * FROM smartEnumCam where id = "' . escapeQuotes($_REQUEST['id']) . '"');
    smart_mysql_query('UPDATE photo set checked = 1 WHERE smartEnumCamid = "' . $cam['name'] . '" and location = "' . $cam['location'] . '"');
}

function unsmartconfirmAllPhotoForCamera()
{
    $cam = sqlGetRow('SELECT * FROM smartEnumCam where id = "' . escapeQuotes($_REQUEST['id']) . '"');
    smart_mysql_query('UPDATE photo set checked = 0 WHERE smartEnumCamid = "' . $cam['name'] . '" and location = "' . $cam['location'] . '"');
}

function smartconfirmTopPhotoForCamera()
{
    $cam = sqlGetRow('SELECT * FROM smartEnumCam where id = "' . escapeQuotes($_REQUEST['id']) . '"');
    $toConfirm = PHOTO_CONFIRM - escapeQuotes($_REQUEST['lessToConfirm']);
    $result = smart_mysql_query('SELECT * from photo where location = "' . $cam['location'] . '" and smartEnumCamid = "' . $cam['name'] . '"  and checked = 0 order by timestamp desc LIMIT ' . $toConfirm, false);
    while ($row = $result->fetch_assoc()) {
        smart_mysql_query('UPDATE smartphoto set checked = 1 WHERE id = "' . $row['id'] . '" ', false);
    }
}

function smartsmartloadCameraImages()
{
	$location = escapeQuotes($_REQUEST['location']);

    set_time_limit(300);
    smart_mysql_query('DELETE from smartphoto WHERE location = "' . $location . '"');

    $files = scandir(UPLOADS . '/cam/' . $location);

    for ($i = 2; $i < count($files); $i ++) {
        $file = UPLOADS . '/cam/' . $location . '/' . $files[$i];
        $sha = md5(file_get_contents($file));

        $removeCamera = str_ireplace($location . '-camera-', '', $files[$i]);
        $cameraNr = substr($removeCamera, 0, (strpos($removeCamera, '-')));
        $timeStr = ((substr($removeCamera, (strpos($removeCamera, '-') + 1), 19)));
        $timestamp = (substr($timeStr, 0, 10) . ' ' . substr(str_ireplace('-', ':', $timeStr), 11));

        $isDay = getIsDayByLocation($location, strtotime($timestamp));

        $sql = 'INSERT INTO smartphoto (location, timestamp, filename, smartEnumCamid, filesize, checked, sha, isday)
		VALUES ("' . $location . '", "' . $timestamp . '", "' . $files[$i] . '", "' . $cameraNr . '", "' . filesize($file) . '", "1", "' . $sha . '", "' . $isDay . '")';

        smart_mysql_query($sql);
    }
}

function displayCamSavedImages($location)
{
    echo '<script>
			<!--	
			$(function () {
			
				$(".Dismiss").click(function() {
					var id = $(this).attr(\'id\').substring(7); 
					var attr = "?a=smartPhotoCheck&id="+id;					
					$(this).parent().parent().hide();	
					lessToConfirm ++;			
					$.ajax({
					  url: attr,
					  context: document.body
					}).done(function(data) {						
						logConsole(attr);
						logConsole(data);
						logConsole("done");	
					});	
				});	
			
				$(".sysDelete").click(function() {
					var attr = "?a=syssmartdeletePhoto&location=' . $location . '&filename="+$(this).attr(\'id\').substr(0,35)+"&id="+$(this).attr(\'photoid\');
					$(this).parent().parent().hide();	
					lessToConfirm ++;		
					$.ajax({
					  url: attr,
					  context: document.body
					}).done(function(data) {						
						logConsole(attr);
						//logConsole(data);
						//logConsole("done");	
					});							
				});
				
				$(".sysDelete-all").click(function() {
					var attr = "?a=syssmartdeleteAllPhotoForCamera&id="+$(this).attr(\'id\');
					ajax(attr, true);
				});
				$(".truncate-all").click(function() {
					var attr = "?a=smarttruncateAllPhotoForCamera&id="+$(this).attr(\'id\');
					ajax(attr, true);	
				});
				$(".confirm-all").click(function() {
					var attr = "?a=smartconfirmAllPhotoForCamera&id="+$(this).attr(\'id\');
					ajax(attr, true);					
				});
				$(".unconfirm-all").click(function() {
					var attr = "?a=unsmartconfirmAllPhotoForCamera&id="+$(this).attr(\'id\');
					ajax(attr, true);					
				});
				$(".confirm-top").click(function() {
					var attr = "?a=smartconfirmTopPhotoForCamera&id="+$(this).attr(\'id\')+"&lessToConfirm="+lessToConfirm;
					ajax(attr, true);					
				});
				$(".load-all").click(function() {
					var attr = "?a=smartsmartloadCameraImages&location="+$(this).attr(\'location\');
					ajax(attr, true);
				});
				$(".duplicate-10").click(function() {
					var attr = "?a=smartduplicate10camImage&id="+$(this).attr(\'id\');
					ajax(attr, true);
				});
				var lessToConfirm = 0;
			});
			-->
			</script>';

    $resultCount = smart_mysql_query("SELECT count(*) as cnt from smartphoto where location = '$location' ");
    if ($resultCount->num_rows > 0) {
        $rowCount = $resultCount->fetch_assoc();
        echo 'spolu: ' . $rowCount['cnt'] . '<br /><br />';
    }

    $resultCam = smart_mysql_query("SELECT * from smartEnumCam where location = '$location' order by name asc");

    echo '<table id="example2" class="table table-bordered table-hover"><thead><tr><th><button class="load-all" location="' . $location . '">Truncate and load all from HDD</button></th></tr></thead></table>';

    if ($resultCam->num_rows > 0) {
        $i = 0;
        while ($rowCam = $resultCam->fetch_assoc()) {
            $result = smart_mysql_query("SELECT * from smartphoto where location = '$location' and smartEnumCamid = '" . $rowCam['name'] . "' order by timestamp desc");
            echo '<table id="example2" class="table table-bordered table-hover">
			<thead><tr><th colspan="2">' . $rowCam['name'] . ' - ' . $rowCam['fullname'] . ' (' . $result->num_rows . ')</th></tr></thead>';

            if ($result->num_rows > 0) {
                echo '<tbody>
				<tr><td><br /><button class="truncate-all" id="' . $rowCam['id'] . '">Truncate all</button>&nbsp;&nbsp;&nbsp;<button class="sysDelete-all" id="' . $rowCam['id'] . '">Delete all</button>&nbsp;&nbsp;&nbsp;<button class="confirm-all" id="' . $rowCam['id'] . '">Confirm all</button>&nbsp;&nbsp;&nbsp;<button class="unconfirm-all" id="' . $rowCam['id'] . '">UnConfirm all</button>&nbsp;&nbsp;&nbsp;<button class="confirm-top" id="' . $rowCam['id'] . '">Confirm top ' . PHOTO_CONFIRM . '</button><br /><br /></td></tr>
				';
                $first = true;
                while ($row = $result->fetch_assoc()) {
                    echo '
					<tr>
						<td><a class="' . ($row['checked'] == '0' ? 'photolink' : '') . ($row['isday'] ? '' : ' bold') . '" href="files/cam/' . $location . '/' . $row['filename'] . '" src="thumbnailer.php?location=' . $location . '&file=' . $row['filename'] . '" target="_blank" title="filesize: ' . $row['filesize'] . ' bytes, sha: ' . $row['sha'] . '" >cam ' . $row['enumcamid'] . ' / ' . $row['timestamp'] . ' (' . round($row['filesize'] / 1024) . ' kb)</a></td>
						<td>
						' . ($first ? '<button class="duplicate-10 narrow" id="' . $row['id'] . '" >duplicate 10</button>' : '') . '
							<button class="sysDelete narrow" id="' . $row['filename'] . '" photoid="' . $row['id'] . '">vymaž</button>';

                    if ($i < PHOTO_CONFIRM && $row['checked'] == '0') {
                        echo '<img src="thumbnailer.php?location=' . $location . '&file=' . $row['filename'] . '" width="100" /> ';
                        $i ++;
                    }

                    echo ($row['checked'] == '0' ? '<button class="smaller Dismiss" id="Dismiss-' . $row['id'] . '">Dismiss</button>' : '✔') . '
						</td>
					</tr>';
                    $first = false;
                }
                echo '</tbody>';
            }
            echo '</table>';
        }
    }
}

function echoCamImage($location, $id, $action, $thumbnail = null)
{
    define("CAMMLWIDTH", 1280);
    define("CAMMLHEIGHT", 720);
    define("CAMNBWIDTH", 704);
    define("CAMNBHEIGHT", 576);
    define("CAMOSWIDTH", 704);
    define("CAMOSHEIGHT", 576);

    $expWidth = $thumbnail == '1' ? getSysConfig('thumbnail-width') : 1280;
    $expHeight = $thumbnail == '1' ? getSysConfig('thumbnail-height') : 720;

    $inpWidth = null;
    $inpHeight = null;
    if ($location == 'ml') {
        $inpWidth = CAMMLWIDTH;
        $inpHeight = CAMMLHEIGHT;
    }
    if ($location == 'nb') {
        $inpWidth = CAMNBWIDTH;
        $inpHeight = CAMNBHEIGHT;
    }
    if ($location == 'os') {
        $inpWidth = CAMNBWIDTH;
        $inpHeight = CAMNBHEIGHT;
    }

    $filename = $location . '-camera-' . $id . '-' . date('Y-m-d-H-i-s', time()) . '.jpg';
    if(/*$action == 'save' ||*/ $action == 'view') {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);

        header('Content-Disposition: attachment; filename="' . basename($filename) . '";');

        header('Content-Type: image/jpeg');
        header('Content-Transfer-Encoding: binary');
    }
    $file = '';
    if ($location == 'ml') {
        // $file = 'http://xxx.ddns.net/?a=photo&id='.$id;
    }
    if ($location == 'nb') {
        $file = 'http://xxx.ddns.net/goform/Capture?{%22data%22:{},%22ch%22:' . $id . ',%22type%22:0,%22param%22:{%22pwd%22:%22xxx%22,%22ip%22:%xxx.ddns.net%22,%22username%22:%22admin%22}}';
    }

    $image = imagecreatetruecolor($expWidth, $expHeight);
    $source = imagecreatefromjpeg($file);
    imagecopyresampled($image, $source, 0, 0, 0, 0, $expWidth, $expHeight, $inpWidth, $inpHeight);

    if ($action == 'save') {
        $url = UPLOADS . '/cam/' . $location . '/' . $filename;
        $status = imagejpeg($image, $url, 98);
        $filesize = filesize($url);
        $sha = getShaOfImageResized($url);
        imagedestroy($image);
        // j('velkost fotky ktoru ukladam je:'.$filesize);
        if (is_numeric($filesize) && ($filesize > 40000)) {
            $returnArray = Array();
            $returnArray[0] = $filename;
            $returnArray[1] = $filesize;
            $returnArray[2] = $sha;
            return $returnArray;
        } else {
            unlink($url);
            return false;
        }
    } else {
        imagejpeg($image, null, 95);
        imagedestroy($image);
    }
}

function smartdowntoserver()
{
    $photo = echoCamImage(escapeQuotes($_REQUEST['location']), escapeQuotes($_REQUEST['id']), 'save');
    if ($photo[1] > 1024) {
        // need to get whether it is day or night
        $isDay = getIsDayByLocation(escapeQuotes($_REQUEST['location']));
        smart_mysql_query('INSERT into smartphoto (location, filename, smartEnumCamid, checked, filesize, sha, isday) VALUES ("' . escapeQuotes($_REQUEST['location']) . '", "' . $photo[0] . '", "' . escapeQuotes($_REQUEST['id']) . '", "1", "' . $photo[1] . '", "' . $photo[2] . '", "' . $isDay . '")');
        return true;
    }
    return false;
}

function camAutoSavingAsynch($location)
{}

function camAutoSaving($location)
{
    $schedule['1 min'] = 1;
    $schedule['2 min'] = 2;
    $schedule['3 min'] = 3;
    $schedule['5 min'] = 5;
    $schedule['15 min'] = 15;
    $schedule['30 min'] = 30;
    $schedule['1 hod'] = 60;

    echo '
	<script><!--
	$(function () {
		$(".start").click(function() {
			attr = "?a=smartUpdateSchedulable&table=enumcam&id="+$(this).attr(\'id\').substring(6)+"&value="+$(this).val();
			ajax(attr);
		});		
		$(".schedule").change(function() {		
			var attr = "?a=smartUpdateSchedulable&table=enumcam&id="+$(this).attr(\'id\')+"&schedule="+$(this).val();
			ajax(attr);   
		});		
		$(".add").click(function() {		
			var attr = "?a=smartsmartSchedulableSchedule&table=enumcamschedule&id="+$(this).attr(\'id\')+"&start="+$(\'#start\'+$(this).attr(\'id\')).val()+($(\'#stop\'+$(this).attr(\'id\')).length?"&stop="+$(\'#stop\'+$(this).attr(\'id\')).val():"");
			ajax(attr);					
		});	
		$(".add-sensor").click(function() {		
			var attr = "?a=smartSwitchSensorAdd&table=enumcam&treshold="+$(\'#treshold-\'+$(this).attr(\'id\')).val()+"&enumsensorid="+$(\'#enumsensorid-\'+$(this).attr(\'id\')).val()+"&id="+$(this).attr(\'id\');
			ajax(attr);
		});	
		$(".remove").click(function() {	
			var attr = "?a=smartsmartSchedulableScheduleRemove&table=enumcamschedule&id="+$(this).attr(\'id\');
			ajax(attr);
		});		
		$(".manual").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumcam&type=0&value=0&schedule=0&id="+$(this).attr(\'id\').substring(7);
			ajax(attr);
		});	
		$(".sensor").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumcam&type=3&value=0&schedule=0&id="+$(this).attr(\'id\').substring(7);
			ajax(attr);
		});
		$(".timer").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumcam&type=1&id="+$(this).attr(\'id\');
			ajax(attr);
		});		
		$(".auto").click(function() {
			var attr = "?a=smartUpdateSchedulable&table=enumcam&type=2&value=0&schedule&id="+$(this).attr(\'id\');
			ajax(attr);
		});			
		$(".add-email").click(function() {		
			var id = $(this).attr(\'id\');
			var attr = "?a=smartSwitchEmailScheduleAdd&table=enumcam&id="+id+"&email="+urlEncode($("#email"+id).val());
			ajax(attr);
		});	
		$(".expand").click(function() {
			$(this).parent().children(".hidden").slideToggle("fast");
			$(this).slideToggle("fast");
		});		
		$(".stop-after").click(function() {
			var attr = "?a=smartstopSavingPhotoAfter&id="+$(this).attr(\'id\');
			ajax(attr, false);
			$(this).toggleClass("selected");
		});
		$(".autostart").click(function() {
			var attr = "?a=smartcameraAutoStart&id="+$(this).attr(\'id\');
			ajax(attr, false);
			$(this).toggleClass("selected");
		});
		$(".autostop").click(function() {
			var attr = "?a=smartcameraAutoStop&id="+$(this).attr(\'id\');
			ajax(attr, false);
			$(this).toggleClass("selected");
		});
		$(".autotimelapse").click(function() {
			var attr = "?a=smartcameraAutoTimelapse&id="+$(this).attr(\'id\');
			ajax(attr, false);
			$(this).toggleClass("selected");
		});
		$(".timelapsesysDelete").click(function() {
			var attr = "?a=smarttimelapseDelete&id="+$(this).attr(\'id\');
			ajax(attr, false);
			$(this).toggleClass("selected");
		});
		
	});
	--></script>';

    $items = sqlGetRows('SELECT c.autostop as autostop, c.timelapsesysDelete as timelapsesysDelete, c.schedule as schedule, c.autostart as autostart, c.autotimelapse as autotimelapse, c.id as id, c.name as name, c.type as type, c.email as email, c.stopsavingaftersimilar as stopsavingaftersimilar, c.fullname as fullname, s.name as switchname, ss.name as switchnamefinish, s.id as enumswitchid from enumcam c LEFT JOIN smartEnumCamenumswitchfinish csf ON csf.enumcamid = c.id LEFT JOIN enumcamenumswitch cs ON cs.enumcamid = c.id LEFT JOIN smartenumswitch s ON s.id = cs.enumswitchid LEFT JOIN smartenumswitch ss ON ss.id = csf.enumswitchid where c.location = "' . $location . '" and c.status = 1 order by c.name asc', false);

    echo '
	<table id="example2" class="table table-bordered table-hover">
	 <thead><tr><th>Kamera</th><th>Aktívny časovač</th><th>Aktívny automaticky</th><th>Zároveň poslať na email(y)</th></tr></thead>
	 <tbody>';
    foreach ($items as $item) {
        echo '
		<tr>
		 <td title="id: ' . $item['id'] . '">' . $item['name'] . ($item['fullname'] ? ' / ' . $item['fullname'] : '') . '</td>';
        if ($item['type'] != 1) {
            echo '<td><button class="timer" id="' . $item['id'] . '">Aktivuj</button>';
        } else {
            echo '<td class="active">
		  	Frekvencia: <select class="schedule custom-select" name="schedule" id="' . $item['id'] . '" >';
            foreach ($schedule as $key => $scheduleItem) {
                echo '<option value="' . $scheduleItem . '" ' . ($scheduleItem == $item['schedule'] ? 'selected="selected"' : '') . '>' . $key . '</option>' . "\r\n";
            }
            echo '</select><br />
			<button class="stop-after' . ($item['stopsavingaftersimilar'] == '1' ? ' selected' : '') . '" id="' . $item['id'] . '">Zastaviť po ' . getSysConfig('SIMILAR_PHOTO_COUNT') . ' rovnakých fotografiách</button><br />
			' . ($item['switchname'] != '' ? ('
				<br /><button class="autostart' . ($item['autostart'] == '1' ? ' selected' : '') . '" id="' . $item['id'] . '">Zapnúť vypínač `' . $item['switchname'] . '`, ak je kamera vypnutá</button><br />
				<br /><button class="autotimelapse' . ($item['autotimelapse'] == '1' ? ' selected' : '') . '" id="' . $item['id'] . '">Create timelapse po dokončení</button>
				<br /><button class="timelapsesysDelete' . ($item['timelapsesysDelete'] == '1' ? ' selected' : '') . '" id="' . $item['id'] . '">Po vytvoreni timelapse vymazať fotky</button>				
			') : '') . '
			' . ($item['switchnamefinish'] != '' ? ('				
				<br /><br /><button class="autostop' . ($item['autostop'] == '1' ? ' selected' : '') . '" id="' . $item['id'] . '">Po dokončení vypnúť vypínač `' . $item['switchnamefinish'] . '`</button>
			') : '') . '
		 </td>';
        }
        echo '
		 <td ' . ($item['type'] == 2 ? 'class="active"' : '') . '>
		  ' . ($item['type'] == 2 ? '' : '<button class="auto" id="' . $item['id'] . '">Aktivuj</button><br />');
        $itemschedule = smart_mysql_query('SELECT * FROM smartEnumCamschedule where smartEnumCamid = "' . $item['id'] . '"');
        if ($itemschedule->num_rows > 0) {
            while ($ssRow = $itemschedule->fetch_assoc()) {
                echo 'Start: ' . $ssRow['start'] . '<button class="remove" id="' . $ssRow['id'] . '" class="small">sysDelete</button><br />';
            }
        }
        echo '<button class="expand">pridaj</button>
			  <div class="hidden">
				start:<input type="text" name="hour" size="4" id="start' . $item['id'] . '" value="12" /> <input type="submit" value="OK" class="add" id="' . $item['id'] . '" />
			  </div>
			</div>				
		 </td>
		 <td>Oddelené bodkočiarkou:<br /><input type="text" name="email" size="15" id="email' . $item['id'] . '" value="' . $item['email'] . '" /><input type="button" value="OK" class="add-email" id="' . $item['id'] . '" /></td>
		</tr>';
    }
    echo '</tbody></table>';
}

function smartstopSavingPhotoAfter()
{
    smart_mysql_query('UPDATE enumcam set stopsavingaftersimilar = case when stopsavingaftersimilar=0 then 1 else 0 end where id = "' . escapeQuotes($_REQUEST['id']) . '"');
}

function smartcameraAutoStart()
{
    smart_mysql_query('UPDATE enumcam set autostart = case when autostart=0 then 1 else 0 end where id = "' . escapeQuotes($_REQUEST['id']) . '"');
}

function smartcameraAutoStop()
{
    smart_mysql_query('UPDATE enumcam set autostop = case when autostop=0 then 1 else 0 end where id = "' . escapeQuotes($_REQUEST['id']) . '"');
}

function smartcameraAutoTimelapse()
{
    smart_mysql_query('UPDATE enumcam set autotimelapse = case when autotimelapse=0 then 1 else 0 end where id = "' . escapeQuotes($_REQUEST['id']) . '"');
}

function smarttimelapseDelete()
{
    smart_mysql_query('UPDATE enumcam set timelapsesysDelete = case when timelapsesysDelete=0 then 1 else 0 end where id = "' . escapeQuotes($_REQUEST['id']) . '"');
}

function smartduplicate10camImage()
{
	$id = escapeQuotes($_REQUEST['id']);

    $photo = sqlGetRow('select * from smartphoto where id = "' . $id . '"');
    $datetime = strtotime($photo['timestamp']);
    for ($i = 0; $i < 10; $i ++) {
        $datetime ++;
        $filename = $photo['location'] . '-camera-' . $photo['enumcamid'] . '-' . date('Y-m-d-H-i-s', $datetime) . '.jpg';
        smart_mysql_query('INSERT INTO photo (location, filename, enumcamid, checked, filesize, sha, timestamp, isday) VALUES ("' . $photo['location'] . '","' . $filename . '","' . $photo['enumcamid'] . '","1","' . $photo['filesize'] . '","' . $photo['sha'] . '","' . date('Y-m-d H-i-s', $datetime) . '","' . $photo['isday'] . '")');
        copy(UPLOADS . '/cam/' . $photo['location'] . '/' . $photo['filename'], UPLOADS . '/cam/' . $photo['location'] . '/' . $filename);
    }
}

function smartstopAfterSimilarPhotosSaved($location = 'ml', $camName = 7)
{

    $photos = sqlGetRows($sql = 'SELECT * from smartphoto where location = "' . $location . '" and enumcamid="' . $camName . '" order by id desc LIMIT 0,' . getSysConfig('SIMILAR_PHOTO_COUNT'));
    $stopSaving = true;

    if (count($photos) < getSysConfig('SIMILAR_PHOTO_COUNT')) {
        $stopSaving = false;
        return $stopSaving;
    }

    $previousFilesize = 0;
    $isFirst = true;
    foreach ($photos as $photo) {
        if (! $isFirst) {
            // echo 'abs($photo[filesize])'.abs($photo['filesize']);
            // echo '$previousFilesize'.$previousFilesize;
            // echo "\n".'abs($photo[filesize]-$previousFilesize):'.abs($photo['filesize']-$previousFilesize);
            // echo "\n";
            if (abs($photo['filesize'] - $previousFilesize) > 6144) {
                $stopSaving = false;
                break;
            }
        }
        $isFirst = false;
        $previousFilesize = $photo['filesize'];
    }

    return $stopSaving;
}

function getShaOfImageResized($filename)
{
    list ($width, $height) = getimagesize($filename);
    $newwidth = $width * 0.01;
    $newheight = $height * 0.01;

    $thumb = imagecreatetruecolor($newwidth, $newheight);
    $source = imagecreatefromjpeg($filename);

    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    $filenameNew = $filename . '.2.jpg';
    imagejpeg($thumb, $filenameNew);
    $md5 = md5(file_get_contents($filename));
    unlink($filenameNew);

    return $md5;
}

function smartCamGetLastImage()
{
    $photo = sqlGetRow($sql = 'select * from smartphoto where location = "' . escapeQuotes($_REQUEST['location']) . '" and enumcamid = "' . escapeQuotes($_REQUEST['camId']) . '" and timestamp > "' . date('Y-m-d H:i:s', escapeQuotes($_REQUEST['time'])) . '" ORDER by ID desc LIMIT 0,1');

    echo $photo['filename'];
}


function smartexecVideoCreation()
{
    $location = escapeQuotes($_REQUEST['location'] );
    $id = escapeQuotes($_REQUEST['id']);
    $fps = escapeQuotes($_REQUEST['fps']);
    // get camera rotation
    $rotationSql = sqlGetRow('SELECT rotation from smartEnumCam where name = "' . escapeQuotes($id) . '"');
    $rotationSql = $rotationSql['rotation'];
    $rotation = $rotationSql != 0 ? ('-vf "transpose=' . ($rotationSql == '90' ? '1' : '0') . '"') : '';

    echo 'location:' . $location . ' / id:' . $id;
    $filename = $location . '-cam-' . $id . '-' . date('Y-m-d-H-i-s', time()) . '.avi';
    $attr = null;
    $output = null;
    $bps = $fps * 1000;
    $attr = 'ffmpeg -framerate ' . $fps . '/1  -f image2 -i "' . UPLOADS . '/cam/' . $location . '/' . $location . '-camera-' . $id . '-%*.jpg" -r 30 -vcodec mpeg2video -b:v ' . $bps . 'k "' . UPLOADS . '/vid/' . $location . '/' . $filename . '" ' . ($rotation) . ' 2>&1';
    // $attr = 'ffmpeg -f image2 -i "'.UPLOADS.'/cam/'.$location.'/camera-'.$id.'-%*.jpg" -c:v libx264 -pix_fmt yuv420p "'.UPLOADS.'/vid/'.$location.'/'.$filename.'" 2>&1';
    logDebug($attr);
    exec($attr, $output);

    smart_mysql_query('INSERT into smartlogvideo (location, smartEnumCamid, output) VALUES ("' . $location . '", "' . $id . '", "' . escapeQuotes(print_r($output, true)) . '")');
}
