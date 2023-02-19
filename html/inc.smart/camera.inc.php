<?php

function smartCameraDisplay($location)
{
	$result = smart_mysql_query("SELECT * FROM enumsensor");
	if ($result->num_rows > 0) {
		echo '<table id="example2" class="table table-bordered table-hover">';
		while ($row = $result->fetch_assoc()) {
			if (sysCanUserRead('temp' . $row['name']) && (substr($row['name'], 0, 2) == $location)) {
				echo '<tr><td><b>Teplota ' . 'temp' . $row['name'] . '</b>:</td><td>' . getCurrentValue($row['name']) . '</td></tr>';
			}
		}
		echo '</table><br />';
	}

	$result = smart_mysql_query("SELECT * from smartEnumCam where location = '$location' and status = 1");
	if ($result->num_rows > 0) {

		echo '
			<script>
			<!--
			$(function () {
				$(".cameraImage IMG").click(function() {		
					window.location.replace("echocamimage.php?a=view&location=' . $location . '&id="+$(this).attr(\'id\'));
				});
				$(".downtopc").click(function() {
					window.location.replace("echocamimage.php?a=view&location=' . $location . '&id="+$(this).attr(\'id\'));
				});
				$(".smartdowntoserver").click(function() {
					var attr = "?a=smartdowntoserver&location=' . $location . '&id="+$(this).attr(\'id\');
					ajax(attr, false);
				});	
			});
			-->
			</script>';

		while ($row = $result->fetch_assoc()) {

			echo '<script><!--
			$(function () {
				$("#cameraImage' . $row['name'] . '-img").attr("src", function(index, src) {
     				//return "echocamimage.php?a=view&location=' . $location . '&id=' . $row['name'] . '&thumbnail=1";
				});
			});
			--></script>';

			// id="cameraImage'.$row['name'].'-img"
			// img/loading.gif
			echo '<div class="cameraImage" >
					<img src="echocamimage.php?a=view&location=' . $location . '&id=' . $row['name'] . '&thumbnail=1" width="' . getSysConfig('thumbnail-width') . '" height="' . getSysConfig('thumbnail-height') . '" id="' . $row['name'] . '" />
					<span style="height:' . (getSysConfig('thumbnail-height') / 2 - 2) . 'px"  class="downtopc" title="Stiahni do pocitaca" id="' . $row['name'] . '"></span>
					<br /><span style="height:' . (getSysConfig('thumbnail-height') / 2 - 2) . 'px"  class="smartdowntoserver" title="Stiahni na server" id="' . $row['name'] . '"></span>
					<span class="cleaner"></span>
			      </div>';
		}

		echo '<div class="cleaner"></div>';
	}
}

function smartCameraDisplayAsynch($location)
{
	global $asynchCallEnum;
	$result = smart_mysql_query("SELECT * from smartEnumCam where location = '$location' and status = 1");
	if ($result->num_rows > 0) {
		echo '
			<script><!--
			$(function () {
				$(".cameraImage IMG").click(function() {
					window.location.replace("echocamimagelatest.php?location=' . $location . '&id="+$(this).attr(\'id\').substring(4));
				});
				$(".downtopc").click(function() {
					window.location.replace("echocamimagelatest.php?location=' . $location . '&id="+$(this).attr(\'id\'));
				});	
				$(".refresh").click(function() {
					var camId = $(this).attr(\'id\');
					var attr = "?a=syssmartinsertAsynchCall&attr-type=' . $asynchCallEnum['cam-' . $location] . '&attr-id="+camId;
					ajax(attr);
					 
					var button = $(this);
					button.addClass("loading");
					button.removeClass("refresh");	
 
					//$("img-"+$(this).attr(\'id\')).src = $("img-"+$(this).attr(\'id\')).src + "&time=" + new Date().getTime();
					
					
					var cameraRefreshCount = Array();
					cameraRefreshCount[camId] = 0;
					var time = new Date().getTime()/1000;
					var timer = setInterval(function(){
					       getLastImageNameForCamera("' . $location . '", camId, time, "#img-"+camId, timer, button);
					       
					       //vypnem ak sa nevypne automaticky do pol minuty
					       cameraRefreshCount[camId] ++;						   
						   if(cameraRefreshCount[camId] > 30){
						   		clearInterval(timer);	
						   		button.removeClass("loading");
								button.addClass("cross");
						   }
					}, 500); 
					
				});			
			});
			--></script>';

		while ($row = $result->fetch_assoc()) {
			$photo = sqlGetRow($sql = 'select * from smartphoto where location = "' . $location . '" and smartEnumCamid = "' . $row['name'] . '" ORDER by ID desc LIMIT 0,1');

			// echocamimagelatest.php?a=view&location='.$location.'&id='.$row['name'].'&thumbnail=1
			echo '<div class="cameraImage" >
					<img src="files/cam/' . $location . '/' . $photo['filename'] . '" title="' . $photo['timestamp'] . '" width="' . getSysConfig('thumbnail-width') . '" height="' . getSysConfig('thumbnail-height') . '" id="img-' . $row['name'] . '" />
					<span style="height:' . (getSysConfig('thumbnail-height') / 2 - 2) . 'px" class="refresh" title="Aktualizuj" id="' . $row['name'] . '"></span><br />
					<span style="height:' . (getSysConfig('thumbnail-height') / 2 - 2) . 'px" class="downtopc" title="Stiahni do pocitaca" id="' . $row['name'] . '"></span>
					<span class="cleaner"></span>					
			      </div>';
		}
		echo '<div class="cleaner"></div>';
	}
}

function smartCameraDisplaySavedVids($location)
{
	echo '<script>
			<!--
			$(function () {
				$(".create").click(function() {
					logConsole("Generujem video, station moze byt pocas nasledujucich minut nedostupny.");
					var attr = "?a=smartexecVideoCreation&location=' . $location . '&id="+$(this).attr(\'id\')+"&fps="+$(\'.fps\').val();
					logConsole(attr);
					ajax(attr, true);
				});
				
				$(".sysDelete").click(function() {					    
					$(this).parent().delay(3000).hide();
					var attr = "?a=syssmartdeleteVideo&location=' . $location . '&filename="+$(this).attr(\'id\').substring(7);
					ajax(attr, true);
				});			
			});
			-->
			</script>';

	$resultCam = smart_mysql_query("SELECT * from smartEnumCam where location = '$location' order by name asc");
	$files = scandir(UPLOADS . '/vid/' . $location, 1);
	if ($resultCam->num_rows > 0) {
		echo '
				<table id="example2" class="table table-bordered table-hover"><thead><tr><th>FPS: <input type="text" value="10" class="fps short" /></th></tr></thead></table>
		
		<table id="example2" class="table table-bordered table-hover">
		 <tr><th>Kamera</th><th>akcia</th><th>hotové videá</th></tr>';
		while ($rowCam = $resultCam->fetch_assoc()) {
			echo '
			<tr>
			 <td class="">' . $rowCam['name'] . ' - ' . $rowCam['fullname'] . '</td>
			 <td class=""><button id="' . $rowCam['name'] . '" class="btn btn-block btn-default create">Create video</button></td>
			 <td class="">';
			for ($i = 0; $i < count($files); $i ++) {
				$filename = UPLOADS . '/vid/' . $location . '/' . $files[$i];
				if (is_dir($filename))
				continue;
				$filesize = round(filesize($filename) / 1014 / 1014) . ' MB';
				$camNr = substr($files[$i], 7, (strpos($files[$i], '-') - 1));

				if ($camNr == $rowCam['name']) {
					$filenameWeb = '/files/vid/' . $location . '/' . $files[$i];
					echo '
					 <span>' . $files[$i] . ' (' . $filesize . ') 
					 	<buttonclass="btn btn-block btn-default " onclick="window.open(\'' . $filenameWeb . '\');" target="_blank">stiahni</button> 
					 	<button class="btn btn-block btn-default sysDelete" id="sysDelete-' . $files[$i] . '">vymaž</button><br />
					 </span>';
				}
			}
			echo '
			 </td>
			</tr>';
		}
		echo '</table>';
	}
	echo '<div class="cleaner"></div>';
}

function smartCameraCamMachine($location)
{
	if (strlen($_REQUEST['date']) == 0)
	$_REQUEST['date'] = date('Y-m-d', time());

	// new
	$rows = (strlen($_REQUEST['rows']) == 0 ? 100 : $_REQUEST['rows']);
	$channels = 0;

	if ($_REQUEST['cam0'] == 'true')
	$channels += 1;
	if ($_REQUEST['cam1'] == 'true')
	$channels += 2;
	if ($_REQUEST['cam2'] == 'true')
	$channels += 4;
	if ($_REQUEST['cam3'] == 'true')
	$channels += 8;
	if ($_REQUEST['cam4'] == 'true')
	$channels += 16;
	if ($_REQUEST['cam5'] == 'true')
	$channels += 32;
	if ($_REQUEST['cam6'] == 'true')
	$channels += 64;
	if ($_REQUEST['cam7'] == 'true')
	$channels += 128;

	$videos = getUrlContent($sss = 'http://192.168.x.x/cgi-bin/gw.cgi?xml=%3Cjuan%20ver=%220%22%20squ=%22abcdef%22%20dir=%220%22%20enc=%221%22%3E%3Crecsearch%20usr=%22admin%22%20pwd=%22xxx%22%20channels=%22' . $channels . '%22%20types=%2215%22%20date=%22' . $_REQUEST['date'] . '%22%20begin=%220:0:0%22%20end=%2223:59:59%22%20session_index=%220%22%20session_count=%22' . $rows . '%22/%3E%3C/juan%3E&_=1505164693834');
	echo '<span class="smaller">' . htmlspecialchars(urldecode($sss)) . '</span>';
	$videoArrReq = explode("\r\n", str_ireplace("\t", '', strip_tags($videos)));
	$videoArr = Array();

	for ($i = 2; $i < count($videoArrReq); $i ++) {
		$videoArr[] = explode('|', $videoArrReq[$i]);
	}

	echo '
	<script>	
	<!--
	$(function () {
		$(".filterbtn").click(function() {
			var attr = "?a=camstation' . $location . '&date="+$(".date").val()+"&rows="+$(".rows").val()' . getforeach('+"&cam1="+$(".cam1").prop("checked")', 0, 7) . ';
				logConsole(attr);	
			window.location.replace(attr);
		});
	});
	
	-->
	</script>	
	<table id="example2" class="table table-bordered table-hover">
		<tr><th colspan="2">Filter</th></tr>
		<tr><td>Datum:</td><td><input class="date shorter" type="text" value="' . (strlen($_REQUEST['date']) == 0 ? date('Y-m-d', time()) : $_REQUEST['date']) . '" /></tr>
		<tr><td>Zaznamov na stranku:</td><td><input class="rows shorter" type="text" value="' . $rows . '" /></td></tr>
		<tr><td>Kamery:</td>
			<td>';
	for ($i = 0; $i < 8; $i ++) {
		echo 'cam ' . $i . ': <input type="checkbox" class="cam' . $i . '" ' . ($_REQUEST['cam' . $i] == 'true' ? 'checked="checked"' : '') . '/><br />';
	}
	echo '</td>
		</tr>
		<tr><td><button class="btn btn-block btn-default filterbtn" >OK</button></td></tr>
	</table>
	<table id="example2" class="table table-bordered table-hover">
	 <tr><th>id</th><th>start</th><th>end</th><th>dlzka</th><th>akcia</th></tr>';
	foreach ($videoArr as $key => $value) {
		echo '<tr><td>kamera ' . $value[2] . '</td><td>' . date('Y-m-d H-i-s', $value[4]) . '</td><td>' . date('Y-m-d H-i-s', $value[5]) . '</td><td>' . date('i:s', ($value[5] - $value[4])) . '</td><td><a href="echocamstation.php?location=' . $location . '&from=' . $value[4] . '&to=' . $value[5] . '&channel=' . $value[2] . '">stiahni</a></td></tr>';
	}
	echo '</table>';
}

function cronCam($row){
    global $asynchCallEnum;
    // KAMERY
    // najprv spravim tie ktore su na type 1
    $cams = sqlGetRows("SELECT
	c.autostop as autostop, 
	ss.id as enumswitchidfinish, 
	s.id as enumswitchid, 
	c.autostart as autostart, 
	c.autotimelapse as autotimelapse, 
	c.location as location, 
	c.name as name, 
	c.schedule as schedule, 
	c.email as email,
	c.asynch as asynch,  
	c.stopsavingaftersimilar as stopsavingaftersimilar 
	FROM smartEnumCam c 
	LEFT JOIN enumcamenumswitchfinish csf ON csf.enumcamid = c.id 
	LEFT JOIN enumswitch ss ON ss.id = csf.enumswitchid 
	LEFT JOIN enumcamenumswitch cs ON cs.enumcamid = c.id 
	LEFT JOIN enumswitch s ON s.id = cs.enumswitchid 
	where c.schedule > 0 and c.type = 1 and c.status = 1");

    $saved = false;
    foreach ($cams as $cam) {
        $resultPhoto = sqlGetRow("SELECT count(*) as count from smartphoto where location = '" . $cam['location'] . "' and smartEnumCamid = '" . $cam['name'] . "' and timestamp > DATE_ADD(now(), INTERVAL - " . $cam['schedule'] . " MINUTE) ");

        if ($resultPhoto['count'] == 0) {
            // but first start camera if it is needed to be started - and continue, maybe it is already started and this will work.
            if ($cam['autostart'] == '1') {
                smart_mysql_query('UPDATE smartenumswitch set value = "1" where id = "' . $cam['smartenumswitchid'] . '" ');
                sleep(6);
            }

            // ak je kamera asynchronna, tak potom ukladam tak ze zavolam task
            if ($cam['asynch'] == '1') {
                syssmartinsertAsynchCall($asynchCallEnum['cam-' . $cam['location']], $cam['name'], true);
                $saved = false;
            } else {
                if (smartdowntoserver($cam['location'], $cam['name'])) {
                    $saved = true;
                }
            }
            if ($saved) {
                // TODO laos auto stop save previous value!!!
                if ($cam['autostart'] == '1') {
                    smart_mysql_query('UPDATE smartenumswitch set value = "0" where id = "' . $cam['enumswitchid'] . '" ');
                }

                if ($cam['stopsavingaftersimilar'] == '1') {
                    $stopSaving = smartstopAfterSimilarPhotosSaved($cam['location'], $cam['name']);
                    if ($stopSaving) {
                        smart_mysql_query('UPDATE smartEnumCam set type = 2 where location = "' . $cam['location'] . '" and name="' . $cam['name'] . '" ');

                        if ($cam['autostop'] == '1') {
                            smart_mysql_query("UPDATE smartenumswitch set value = '0' where id = '" . $cam['enumswitchidfinish'] . "'");
                        }

                        if ($cam['autotimelapse'] == '1') {
                            $photosCount = sqlGetRows('SELECT count(*) from smartphoto where location = "' . $cam['location'] . '" and smartEnumCamid="' . $cam['name'] . '"');

                            $fps = 10;
                            if ($photosCount > 200)
                                $fps = 20;
                            if ($photosCount > 500)
                                $fps = 30;
                            smartexecVideoCreation($cam['location'], $cam['name'], $fps);
                            logDebug('Vytvaram video ' . $cam['location'] . '-cam-' . $cam['name'] . ' - ' . $fps . ' fps', 1);
                        }
                    }
                }
            }
        }
        if ($saved)
            break;
    }

    $cams = sqlGetRows("SELECT * from smartEnumCam where type = 2 and status = 1");
    $saved = false;
    foreach ($cams as $cam) {
        $start = date('H', time());

        // ale iba ak ma kamera v schedule hodnotu
        $camschedule = smart_mysql_query("SELECT start FROM smartEnumCamschedule where smartEnumCamid = '" . $cam['id'] . "' and start = '$start' ");
        if ($camschedule->num_rows == 1) {
            // ale iba ak este nema fotku, resp ma fotku ale s inou hodinou
            $doSave = false;
            $resultPhoto = smart_mysql_query("SELECT timestamp from smartphoto where location = '" . $cam['location'] . "' and enumcamid = '" . $cam['name'] . "' order by timestamp desc LIMIT 1");
            $resultPhotoItem = $resultPhoto->fetch_assoc();
            if ($resultPhoto->num_rows > 0) {
                $timetodayFullhour = date('Y-m-d H:', time()) . '00:00';
                if (strtotime($resultPhotoItem['timestamp']) < strtotime($timetodayFullhour)) {
                    $doSave = true;
                }
            } else {
                $doSave = true;
            }

            if ($doSave) {
                // ak je kamera asynchronna, tak potom ukladam tak ze zavolam task
                if ($cam['asynch'] == '1') {
                    syssmartinsertAsynchCall($asynchCallEnum['cam-' . $cam['location']], $cam['name'], true);
                } else {
                    if (smartdowntoserver($cam['location'], $cam['name'])) {
                        $saved = true;
                    }
                }
            }
        }
        if ($saved)
            break;
    }
}