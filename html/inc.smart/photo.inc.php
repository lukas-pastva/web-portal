<?php

function smartPhotoCheck(){
    smart_mysql_query('UPDATE smartphoto set checked = 1 where id = "'.escapeQuotes($_REQUEST['id']).'"  ' );

}