<?php

function echoSunsetAjax()
{
    $location = sqlGetRow('select * from smartenumlocation where name = "ml"');
    $ajaxData = getSunAjaxByLocation($location);
    $data = '
	Sunrise: ' . date('H:i:s', strtotime($ajaxData->results->sunrise)) . '<br /> 
	Sunset: ' . date('H:i:s', strtotime($ajaxData->results->sunset)) . '<br />
	Noon: ' . date('H:i:s', strtotime($ajaxData->results->solar_noon)) . '<br />
	Daylight: ' . date('H:i:s', strtotime($ajaxData->results->day_length)) . ' 
	';

    echo '<tr><td>' . $data . '</td></tr>';
}

function getSunAjaxByLocation($location)
{
    $url = 'http://api.sunrise-sunset.org/json?lat=' . $location['lat'] . '&lng=' . $location['lng'];
    return json_decode(getUrlContent($url));
}

function getUrlContent($url, $recursiveCount = 0)
{
    $return = callCurl($url);
    if ($return['err']['redirect_url'] != '' && strlen($return['data']) < 50) {
        if ($recursiveCount <= 2) {
            $recursiveCount++;
            return getUrlContent($return['err']['redirect_url'], $recursiveCount);
        }
    }
    return $return['data'];
}

function callCurl($url, $proxy = '', $usernameAndPassword = null, $file = null): array
{
    $return = array();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    if ($file != null) {
        if ($fileHandle = fopen($file, 'r')) {
            curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_INFILE, $fileHandle);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
        } else {
            echo "No data in file";
        }
    }
    if ($usernameAndPassword != null) {
        curl_setopt($ch, CURLOPT_USERPWD, $usernameAndPassword);
    }
    $return['data'] = curl_exec($ch);
    $return['err'] = curl_getinfo($ch);
    curl_close($ch);

    return $return;
}
