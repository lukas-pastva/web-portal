<?php

function businessBazosView()
{
    sysPrintHeader();
    sysPrintBlockHeader(6, 'Bazos');
    echo '<script>
		<!--
		/*$(function () {
			$(".bazos-checked").click(function() {
				var id = $(this).attr(\'id\'); 
				var attr = "index.php?a=businessBazosChecked&id="+id;
				ajax(attr, false, null);
				$(this).parent().parent().hide();
			});
			
			$(".bazos-refresh").click(function() {
				var attr = "index.php?a=businessBazosLoad";
				logConsole("Nacitam Bazos.");
				ajax(attr, false, null);				
			});
			$(".bazar-refresh").click(function() {
				var attr = "index.php?a=businessBazarLoad";
				logConsole("Nacitam Bazar.");
				ajax(attr, false);				
			});
            $(".bazos-check-all").click(function() {
				var attr = "index.php?a=businessBazosCheckAll&time='.time().'";
				ajax(attr, false);				
			});
            $(".bazos-validate-all").click(function() {
				var attr = "index.php?a=businessbusinessBazosValidateAll";
				ajax(attr, true);				
			});
            $(".bazos-queue-cleanup-full").click(function() {
                logConsole("Queue clenaup full.");
				var attr = "index.php?a=businessBazosQueueCleanupFull";
				ajax(attr, true);				
			});
            $(".bazos-most-used").click(function() {
                logConsole("Echoing most used titles from bazos DB.");
				var attr = "index.php?a=businessBazosMostUsed";
				ajax(attr, true);				
			});

			
		});	*/		
		-->
		</script><table id="example2" class="table table-bordered table-hover"><thead>
	<tr ><th colspan="4"><button class="bazos-refresh btn btn-secondary" >Bazos (' . date('l H:i', strtotime(getSysConfig('BAZOS_LOADED'))) . ' - ' . round(sysModuleGetNrOfRows('businessBazos')) . ')</button> <button class="bazos-check-all btn btn-secondary" >✓ </button> <button class="bazos-validate-all btn btn-secondary" ><span style="font-size:24px; line-height:23px; display: block; padding: 0 0 0 0; margin: -6px 0 0 0;">⌕</span> </button> ' . sysModuleGetNrOfRows('businessBazosqueue', 'AND timestamp > DATE_ADD(now(), INTERVAL -15 MINUTE)') . ' / ' . sysModuleGetNrOfRows('businessBazosqueue', 'AND timestamp < DATE_ADD(now(), INTERVAL -15 MINUTE)') . ' <button class="bazos-queue-cleanup-full btn btn-secondary">!</button><button class="bazos-most-used btn btn-secondary">.</button></th></tr>
	<tr ><th></th><th>Name</th><th>price</th><th>img</th></tr>
	</thead><tbody>';
    $inzeraty = sqlGetRows("SELECT * FROM businessBazos where status = 2");
    foreach ($inzeraty as $inzerat) {
        echo '<tr>
                <td style="vertical-align:top;"><button class="bazos-checked btn btn-secondary" id="' . $inzerat['id'] . '">check</button></td>
                <td style="vertical-align:top;"><a href="' . $inzerat['url'] . '" target="_blank">' . $inzerat['name'] . '</a></td>
                <td style="vertical-align:top;">' . $inzerat['price'] . '</td>
                <td><img src="' . $inzerat['img'] . '" alt="" style="max-width: 100px; max-height: 100px;" /></td>
              </tr>';
    }
    echo '</tbody></table>';
    sysPrintBlockFooter();
    sysPrintFooter();
}

function businessBazarLoad()
{
    $sysInserted = 0;
    $rules = sqlGetRows('SELECT * from businessBazosRule order by ID asc');
    $lastId = getSysConfig('BAZAR_LAST_ID');
    $iteration = 50;
    $currentId = $lastId + 1;
    $errCount = 0;

    for ($i = 0; $i < $iteration; $i ++) {
        $doInsert = false;
        $currentId ++;
        $url = 'https://auto.bazar.sk/' . $currentId . '-' . $currentId . '/';
        $return = getUrlContent($url);

        if (strlen($return) < 50) {
            $errCount ++;
        } else {
            if (strpos($return, 'nka nebola n') !== false) {
                $errCount ++;
            }
        }

        if ($errCount > 1) {
            break;
        }

        $nameStart = strpos($return, 'og:title" content="') + 19;

        if ($nameStart <= 19)
            continue;
        $name = substr($return, $nameStart, (strpos($return, '|', ($nameStart + 1)) - $nameStart));
        $name = strip_tags($name);
        $name = trim($name);

        $priceStart = strpos($return, 'Cena:') + 5;
        if ($priceStart <= 5)
            continue;

        $price = substr($return, $priceStart, (strpos($return, '</div>', ($priceStart + 6)) - $priceStart));
        $price = trim(strip_tags(substr($price, 0, 300)));

        // echo 'url: '.$url.', name: '.$name.', cena: '.$price.'<br />';

        foreach ($rules as $rule) {
            // echo 'porovnavam: '.strtoupper(s($name)).' s: '.strtoupper(s($rule['name']));
            if (strpos(strtoupper(escapeQuotes($name)), strtoupper(escapeQuotes($rule['name']))) !== false) {
                $doInsert = true;
                if ($rule['notcontain'] != '') {
                    $notContainArr = explode(',', $rule['notcontain']);
                    foreach ($notContainArr as $notContainItem) {
                        $notContainItem = strtoupper(trim($notContainItem));
                        // echo 'porovnavam not contain:>'.strtoupper(s(trim($name))).':s:' .strtoupper(s($notContainItem)).'.a vysledok je:'.(strpos(strtoupper(s(trim($name))), strtoupper(s($notContainItem)))==false).'<br />';
                        if (strpos(strtoupper(escapeQuotes(trim($name))), strtoupper(escapeQuotes($notContainItem))) != false) {
                            $doInsert = false;
                        }
                    }
                }
            }
        }

        if ($doInsert) {
            smart_mysql_query("INSERT into businessBazos (name, price, url, checked, businessBazosRuleid, inzeratid) VALUES ('" . escapeQuotes($name) . "', '" . strip_tags(escapeQuotes($price)) . "', '" . strip_tags(escapeQuotes($url)) . "', 0, '" . $rule['id'] . "', '" . ($currentId - 1) . "') ");
            $sysInserted ++;
        }
    }
    smart_mysql_query("UPDATE sysConfig SET text = '$currentId' WHERE name = 'BAZAR_LAST_ID'");
    smart_mysql_query("UPDATE sysConfig set text = '" . (date('Y-m-d H:i:s', time())) . "' where name = 'BAZAR_LOADED'");
    echo 'presiel som inzeatov bazar: ' . $i . ' a z toho som vlozil: ' . $sysInserted;
}

function businessBazosFetch($inzeratStatus, $currentId, $skipWords, $updating = false)
{
    $inzeratStatus['failedToWebLoad'] = false;

    $url = 'https://pc.bazos.sk/inzerat/' . $currentId . '/' . $currentId . '.php';

    $return = getUrlContent($url);
    echo '<br />'.($return);
    $inzeratStatus['totalResLength'] += strlen($return);

    if (strpos($return, 't bol vymazan') !== false) {
        $inzeratStatus['skippedVymazany'] ++;
        $inzeratStatus['failedToWebLoad'] = true;
        if (! $updating) {
            smart_mysql_query("INSERT INTO businessBazosqueue (businessBazosid, status) VALUES (" . $currentId . ",'1')");
        }
        return $inzeratStatus;
    }

    if (strpos($return, 'daniu nevyhovuj') !== false) {
        $inzeratStatus['skippedNeexistujuci'] ++;
        $inzeratStatus['failedToWebLoad'] = true;
        if (! $updating) {
            smart_mysql_query("INSERT INTO businessBazosqueue (businessBazosid, status) VALUES (" . $currentId . ",'1')");
        }

        return $inzeratStatus;
    }

    // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // name
    $nameStart = strpos($return, '<title>') + 7;

    if ($nameStart < 8) {
        $inzeratStatus['skippedKratkeName'] ++;
        return $inzeratStatus;
    }

    $name = substr($return, $nameStart, (strpos($return, '<', ($nameStart + 1)) - $nameStart));
    $name = escapeQuotes(strip_tags($name));

    // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // skipwords

    foreach ($skipWords as $skipWord) {
        if (strpos(strtolower($name), $skipWord['name']) !== false) {
            $inzeratStatus['skippedInSkipwords'] ++;
            return $inzeratStatus;
        }
    }

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // image
    $imgStart = strpos($return, 'og:image" content="') + 19;
    if ($imgStart > 19) {
        $img = substr($return, $imgStart, (strpos($return, '"', ($imgStart + 1)) - $imgStart));
        $img = escapeQuotes(trim(strip_tags($img)));
    }

    // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // price
    $priceStart = strpos($return, 'Cena:') + 5;
    if ($priceStart < 6) {
        $inzeratStatus['skippedKratkaCena'] ++;
        return $inzeratStatus;
    }
    $price = substr($return, $priceStart, (strpos($return, ',', $priceStart) - $priceStart));
    $price = str_ireplace('€', '', $price);
    $price = str_ireplace(' ', '', $price);
    $price = is_numeric($price) ? $price : '-';
    // skipnem az priliz drahe veci by general
    if ($price > 1000) {
        $inzeratStatus['skippedInPrice'] ++;
        return $inzeratStatus;
    }

    // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // text
    $textStart = strpos($return, '<div class=popis>') + 17;

    if ($textStart < 18) {
        $inzeratStatus['skippedKratkyText'] ++;
        return $inzeratStatus;
    }
    $text = substr($return, $textStart, (strpos($return, '</div>', ($textStart + 6)) - $textStart));
    $text = escapeQuotes(strip_tags($text));
    // skipwords for text as well!
    foreach ($skipWords as $skipWord) {
        if (strpos(strtolower($text), strtolower($skipWord['name'])) !== false) {
            $inzeratStatus['skippedInText'] ++;
            return $inzeratStatus;
        }
    }

    // //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    smart_mysql_query("INSERT into businessBazos (name, price, url, img, status, text) VALUES ('" . $name . "', '" . $price . "', '" . $url . "', '" . $img . "', '0', '" . $text . "') ");
    $inzeratStatus['sysInserted'] ++;

    return $inzeratStatus;
}

function businessBazosMostUsed()
{
    $names = sqlGetRows('SELECT name FROM businessBazos LIMIT 1,5');
    $namesStr = '';
    foreach ($names as $name) {
        $namesStr .= strtolower(trim($name['name'])) . ' ';
    }

    $namesArr = explode(' ', $namesStr);
    $namesArrCount = array_count_values($namesArr);
    rsort($namesArrCount);

    for ($i = 0; $i < 50; $i ++) {
        echo $namesArrCount[$i] . '<br />';
    }
}

function businessBazosLoad()
{

    // status = 0 //sysInserted, not checked
    // status = 1 //sysInserted, checked
    // status = 2 //sysInserted, checked, picked
    // status = 3 //sysInserted, checked, picked, finalized
    $skipWords = sqlGetRows('SELECT * FROM businessBazosSkipWords ORDER BY count DESC');

    $inzeratStatus = businessBazosInitializeMonitoringArray();

    $currentId = getSysConfig('BAZOS_LAST_ID');
    for ($i = $currentId; $i <= ($currentId + 49); $i ++) {
        $inzeratStatus = businessBazosFetch($inzeratStatus, $i, $skipWords);
        smart_mysql_query("UPDATE sysConfig SET text = '" . $i . "' WHERE name = 'BAZOS_LAST_ID'");
        if (($inzeratStatus['skippedNeexistujuci'] + $inzeratStatus['skippedVymazany']) == 5) {
            break;
        }
    }

    businessBazosViewNotification($inzeratStatus, ($i - $currentId));

    smart_mysql_query("UPDATE sysConfig set text = '" . (date('Y-m-d H:i:s', time())) . "' where name = 'BAZOS_LOADED'");

    businessBazosValidate(false);

    businessBazosCleanupQueue($skipWords);
    businessBazosQueueCleanupFull();
}

function businessBazosViewNotification($inzeratStatus, $count)
{
    echo '<br />Bazos: ' . $count . ' / ' . ceil($inzeratStatus['totalResLength'] / 1024) . ' kB, new: ' . $inzeratStatus['sysInserted'] .
        //
        ', neexistujuci: ' . $inzeratStatus['skippedNeexistujuci'] .
        //
        ', vymazany: ' . $inzeratStatus['skippedVymazany'] .
        //
        ', text: ' . $inzeratStatus['skippedInText'] .
        //
        ', skipwords:' . $inzeratStatus['skippedInSkipwords'] .
        //
        ', cena:' . $inzeratStatus['skippedInPrice'] .
        // ' a kratke Name:' . $inzeratStatus['skippedKratkeName'] .
        // ' a kratka cena:' . $inzeratStatus['skippedKratkaCena'] .
        // ' a kratky text:' . $inzeratStatus['skippedKratkyText'] .
        //
        '.';
}

function businessBazosQueueCleanupFull()
{
    $inzeratStatus = businessBazosInitializeMonitoringArray();
    $skipWords = sqlGetRows('SELECT * FROM businessBazosSkipWords ORDER BY count DESC');

    $queue = sqlGetRows('SELECT * FROM businessBazosqueue WHERE timestamp < DATE_ADD(now(), INTERVAL -60 MINUTE) ORDER BY id ASC LIMIT 0, 50');
    foreach ($queue as $item) {
        $inzeratStatus['count'] ++;
        $inzeratStatus = businessBazosFetch($inzeratStatus, $item['businessBazosid'], $skipWords, true);
        smart_mysql_query('DELETE FROM businessBazosqueue WHERE id = "' . $item['id'] . '"');
    }
    businessBazosViewNotification($inzeratStatus, $inzeratStatus['count']);
}

function businessBazosCleanupQueue($skipWords)
{
    $inzeratStatus = businessBazosInitializeMonitoringArray();
    $skipWords = sqlGetRows('SELECT * FROM businessBazosSkipWords ORDER BY count DESC');
    $queue = sqlGetRows('SELECT * FROM businessBazosqueue WHERE timestamp < DATE_ADD(now(), INTERVAL -1 MINUTE) AND timestamp > DATE_ADD(now(), INTERVAL -45 MINUTE) ORDER BY id ASC LIMIT 0, 20');
    foreach ($queue as $item) {
        $inzeratStatus['count'] ++;
        $inzeratStatus = businessBazosFetch($inzeratStatus, $item['businessBazosid'], $skipWords, true);
        if ($inzeratStatus['failedToWebLoad'] == false) {
            smart_mysql_query('DELETE FROM businessBazosqueue WHERE id = "' . $item['id'] . '"');
        }
    }
    businessBazosViewNotification($inzeratStatus, $inzeratStatus['count']);
}

function businessBazosValidate($all)
{
    $validated = 0;
    $rules = sqlGetRows('SELECT * FROM businessBazosRule');
    $inzeraty = sqlGetRows('SELECT * FROM businessBazos WHERE 1=1 ' . ($all ? ' AND status NOT IN (2,3)' : ' AND status = 0') . ' LIMIT 0, 100000');

    echo '<br />Filtering' . ($all ? ' all, will take some time.' : '');

    foreach ($inzeraty as $inzerat) {
        $doInsert = false;

        $selectedRule = '';
        foreach ($rules as $rule) {

            // filter in name
            if (strpos(strtolower($inzerat['name']), strtolower($rule['name'])) !== false) {
                $selectedRule = $rule['name'];
                $doInsert = true;

                if ($rule['notcontain'] != '') {
                    $notContainArr = explode(',', $rule['notcontain']);
                    foreach ($notContainArr as $notContainItem) {
                        logDebug(('NAME porovnavam tento notcontain ' . strtolower($inzerat['name']) . ', s:' . strtolower(trim($notContainItem))), 2);
                        if (strpos(strtolower($inzerat['name']), strtolower(trim($notContainItem))) !== false) {
                            $doInsert = false;
                            break;
                        }
                    }
                }
            }

            // filter in text
            if ($rule['searchintext'] == '1') {
                if ((! $doInsert) && (strpos(strtolower($inzerat['text']), strtolower($rule['name'])) !== false)) {
                    $selectedRule = $rule['name'];
                    $doInsert = true;

                    if ($rule['notcontain'] != '') {
                        $notContainArr = explode(',', $rule['notcontain']);
                        foreach ($notContainArr as $notContainItem) {
                            /*
                             * j(
                             * ( 'TEXT porovnavam tento notcontain '.strtolower($inzerat['text']).', s:'.strtolower(trim($notContainItem)) ),
                             * 2);
                             */
                            if (strpos(strtolower($inzerat['text']), strtolower(trim($notContainItem))) !== false) {
                                $doInsert = false;
                                break;
                            }
                        }
                    }
                }
            }
            // cena
            if (is_numeric($inzerat['price']) && $doInsert) {
                if (is_numeric($rule['pricefrom']) && ($inzerat['price'] < $rule['pricefrom'])) {
                    $doInsert = false;
                }

                if (is_numeric($rule['priceto']) && ($inzerat['price'] > $rule['priceto'])) {
                    $doInsert = false;
                }
            }
        }

        if ($doInsert) {
            smart_mysql_query('UPDATE businessBazos SET status = 2, name = CONCAT("' . $selectedRule . ' - ",name) WHERE id = "' . $inzerat['id'] . '"');
            $validated ++;
        } else {
            smart_mysql_query('UPDATE businessBazos SET status = 1 WHERE id = "' . $inzerat['id'] . '"');
        }
    }
    echo ' validoval som:' . $validated;
}

function businessBazosInitializeMonitoringArray()
{
    $inzeratStatus = Array();
    $inzeratStatus['totalResLength'] = 0;
    $inzeratStatus['skippedInText'] = 0;
    $inzeratStatus['skippedInSkipwords'] = 0;
    $inzeratStatus['skippedInPrice'] = 0;
    $inzeratStatus['skippedKratkeName'] = 0;
    $inzeratStatus['skippedKratkaCena'] = 0;
    $inzeratStatus['skippedKratkyText'] = 0;
    $inzeratStatus['skippedNeexistujuci'] = 0;
    $inzeratStatus['skippedVymazany'] = 0;
    $inzeratStatus['skippedInText'] = 0;
    $inzeratStatus['sysInserted'] = 0;
    $inzeratStatus['failedToWebLoad'] = false;
    $inzeratStatus['count'] = 0;
    return $inzeratStatus;
}