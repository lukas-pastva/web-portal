<?php

function sysCron()
{
    echo "\nExecuting cron\n";
    $running = sqlGetRow('SELECT * FROM sysConfig WHERE name="CRON_RUNNING"');
    $forced = isset($_REQUEST['forced']) && $_REQUEST['forced'] == 'true';

    // If running for shorter period than 25 seconds
    if (($running['text'] == '1') && (strtotime($running['timestamp']) > (time() - 25))) {
        return;
    }
    echo "\nCron not running, so executing cron\n";
    // Start
    smart_mysql_query("UPDATE sysConfig SET text = '1' WHERE name = 'CRON_RUNNING'");

    // cron 1 minute
    $row = sqlGetRow("SELECT * from sysConfig where name = 'timestampCron60'");

    if (((time() - strtotime($row['text'])) > 59) || $forced) {
        echo "\n60 seconds cron\n";
        //Running reports
        sysUpdateOnlineUser();

        smart_mysql_query("UPDATE sysConfig set text = '" . (date('Y-m-d H:i:s', time())) . "' where name = 'timestampCron60'");
    }

    // cron 30 minutes
    $row = sqlGetRow("SELECT * from sysConfig where name = 'timestampCron3600'");
    if ((time() - strtotime($row['text'])) > 3599) {
        echo "\n3600 seconds cron\n";
        smart_mysql_query("UPDATE sysConfig set text = '" . (date('Y-m-d H:i:s', time())) . "' where name = 'timestampCron3600'");
    }

    // cron 24 hours
    $row = sqlGetRow("SELECT * from sysConfig where name = 'timestampCron86400'");
    if ((time() - strtotime($row['text'])) > 86399) {
        echo "\n8640 seconds cron\n";

        // SUN
        /*$rows = sqlGetRows("SELECT * FROM enumlocation");
        foreach ($rows as $row) {
            $ajaxData = getSunAjaxByLocation($row);
            if ($ajaxData->results->sunrise) {
                smart_mysql_query('UPDATE enumlocation set sunrise="' . date('Y-m-d H:i:s', strtotime($ajaxData->results->sunrise)) . '", sunset="' . date('Y-m-d H:i:s', strtotime($ajaxData->results->sunset)) . '" where id = "' . $row['id'] . '" ');
            }
        }*/

        smart_mysql_query("UPDATE sysConfig set text = '" . (date('Y-m-d H:i:s', time())) . "' where name = 'timestampCron86400'");
    }


    // finish cron
    smart_mysql_query("UPDATE sysConfig SET text = '0' WHERE name = 'CRON_RUNNING'");
}

